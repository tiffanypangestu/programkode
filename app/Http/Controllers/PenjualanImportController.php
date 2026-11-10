<?php

namespace App\Http\Controllers;

use App\Imports\PenjualanImport;
use App\Models\HargaProduk;
use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class PenjualanImportController extends Controller
{
    public function create()
    {
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('penjualan.import', compact('karyawan'));
    }

    /**
     * Processing batch AJAX per 100 data dari client (No Reload Page).
     */
    public function processBatch(Request $request)
    {
        if (! $request->filled('karyawan_id')) {
            $karyawanId = auth()->user()?->karyawan?->karyawan_id ?? Karyawan::first()?->karyawan_id;
            if ($karyawanId) {
                $request->merge(['karyawan_id' => $karyawanId]);
            }
        }

        $request->validate([
            'karyawan_id' => ['required', 'integer', 'exists:karyawan,karyawan_id'],
            'rows'        => ['required', 'array', 'min:1'],
        ], [
            'karyawan_id.required' => 'Karyawan penanggung jawab wajib dipilih.',
            'rows.required'        => 'Data batch tidak boleh kosong.',
        ]);

        $karyawanId = (int) $request->input('karyawan_id');
        $rawRows    = $request->input('rows');

        // ── 1. Kelompokkan baris berdasarkan nomor transaksi ─────────
        $rawTrxGroups = [];
        $syntaxErrors = [];

        foreach ($rawRows as $index => $rowArr) {
            $baris      = $rowArr['__rowNum__'] ?? ($index + 1);
            $nomor      = trim((string) ($rowArr['nomor_transaksi'] ?? ''));
            $kodeOutlet = trim((string) ($rowArr['kode_outlet'] ?? ''));
            $namaProduk = trim((string) ($rowArr['nama_produk'] ?? ''));

            if ($nomor === '' && $kodeOutlet === '' && $namaProduk === '') {
                continue;
            }

            if ($nomor === '') {
                $syntaxErrors[] = "Baris {$baris}: kolom nomor_transaksi tidak boleh kosong.";
                continue;
            }

            if (strlen($nomor) > 50) {
                $syntaxErrors[] = "Baris {$baris}: nomor transaksi maksimal 50 karakter.";
                continue;
            }

            $rawTrxGroups[$nomor][] = [
                'baris' => $baris,
                'row'   => $rowArr,
            ];
        }

        if (! empty($syntaxErrors)) {
            return response()->json([
                'success' => false,
                'errors'  => $syntaxErrors,
            ], 422);
        }

        if (empty($rawTrxGroups)) {
            return response()->json([
                'success'               => true,
                'imported_transactions' => 0,
                'imported_rows'         => 0,
                'duplicates'            => [],
            ]);
        }

        // ── 2. Cek transaksi yang sudah ada di database (DUPLIKAT) ────
        $allNomor     = array_keys($rawTrxGroups);
        $existingInDb = Transaksi::whereIn('nomor_transaksi', $allNomor)
            ->pluck('nomor_transaksi')
            ->toArray();

        $existingMap = array_flip($existingInDb);

        $duplicates   = [];
        $newTrxGroups = [];

        foreach ($rawTrxGroups as $nomor => $items) {
            if (isset($existingMap[$nomor])) {
                // Lewati (skip) duplikat & kumpulkan informasinya
                $firstRow = $items[0]['row'];
                $duplicates[] = [
                    'nomor_transaksi' => $nomor,
                    'total_baris'     => count($items),
                    'tanggal'         => $firstRow['tanggal'] ?? '-',
                    'kode_outlet'     => $firstRow['kode_outlet'] ?? '-',
                ];
            } else {
                $newTrxGroups[$nomor] = $items;
            }
        }

        // Jika semua transaksi di batch ini adalah duplikat
        if (empty($newTrxGroups)) {
            return response()->json([
                'success'               => true,
                'imported_transactions' => 0,
                'imported_rows'         => 0,
                'duplicates'            => $duplicates,
            ]);
        }

        // ── 3. Validasi transaksi baru yang akan disimpan ────────────
        $outlets = Outlet::with('wilayah')->get()->keyBy('kode_outlet');
        $produks = Produk::all()->groupBy('nama_produk');

        $validationErrors  = [];
        $validatedTrx      = [];
        $importedRowsCount = 0;

        foreach ($newTrxGroups as $nomor => $items) {
            $firstTanggal = null;
            $firstOutlet  = null;
            $trxItems     = [];

            foreach ($items as $entry) {
                $baris = $entry['baris'];
                $row   = $entry['row'];

                $kodeOutlet = trim((string) ($row['kode_outlet'] ?? ''));
                $namaProduk = trim((string) ($row['nama_produk'] ?? ''));

                if ($kodeOutlet === '' || $namaProduk === '' || ! isset($row['tanggal'], $row['jumlah'], $row['harga_satuan'])) {
                    $validationErrors[] = "Baris {$baris}: terdapat kolom wajib yang kosong.";
                    continue;
                }

                try {
                    $tanggal = $this->parseTanggal($row['tanggal']);
                } catch (\Throwable $e) {
                    $validationErrors[] = "Baris {$baris}: format tanggal '{$row['tanggal']}' tidak valid.";
                    continue;
                }

                if (
                    ! is_numeric($row['jumlah']) ||
                    (float) $row['jumlah'] <= 0 ||
                    floor((float) $row['jumlah']) != (float) $row['jumlah']
                ) {
                    $validationErrors[] = "Baris {$baris}: jumlah harus berupa bilangan bulat positif.";
                    continue;
                }

                if (
                    ! is_numeric($row['harga_satuan']) ||
                    (float) $row['harga_satuan'] <= 0
                ) {
                    $validationErrors[] = "Baris {$baris}: harga satuan harus lebih besar dari nol.";
                    continue;
                }

                $outlet = $outlets->get($kodeOutlet);
                if (! $outlet || ! $outlet->wilayah) {
                    $validationErrors[] = "Baris {$baris}: outlet '{$kodeOutlet}' tidak ditemukan atau belum memiliki wilayah.";
                    continue;
                }

                // Konsistensi tanggal dan outlet dalam 1 nomor transaksi
                if ($firstTanggal === null) {
                    $firstTanggal = $tanggal;
                    $firstOutlet  = $outlet;
                } else {
                    if ($tanggal !== $firstTanggal || $outlet->outlet_id !== $firstOutlet->outlet_id) {
                        $validationErrors[] = "Nomor transaksi {$nomor}: tanggal dan outlet harus konsisten di semua baris.";
                        break;
                    }
                }

                $produkList = $produks->get($namaProduk);
                if (! $produkList || $produkList->count() !== 1) {
                    $validationErrors[] = "Baris {$baris}: produk '{$namaProduk}' tidak ditemukan atau namanya tidak unik.";
                    continue;
                }

                $produk     = $produkList->first();
                $hargaExcel = round((float) $row['harga_satuan'], 2);

                $hargaProduk = HargaProduk::where('produk_id', $produk->produk_id)
                    ->where('wilayah_id', $outlet->wilayah_id)
                    ->where(function ($query) use ($tanggal) {
                        $query->whereNull('tanggal_mulai')
                            ->orWhereDate('tanggal_mulai', '<=', $tanggal);
                    })
                    ->where(function ($query) use ($tanggal) {
                        $query->whereNull('tanggal_berakhir')
                            ->orWhereDate('tanggal_berakhir', '>=', $tanggal);
                    })
                    ->where('harga_produk', $hargaExcel)
                    ->first();

                if (! $hargaProduk) {
                    $validationErrors[] = "Baris {$baris}: harga Rp" .
                        number_format($hargaExcel, 2, ',', '.') .
                        " tidak cocok dengan harga produk '{$namaProduk}' di wilayah outlet pada tanggal {$tanggal}.";
                    continue;
                }

                $jumlah   = (int) $row['jumlah'];
                $subtotal = round($jumlah * $hargaExcel, 2);

                $trxItems[] = [
                    'produk'          => $produk,
                    'harga_produk'    => $hargaProduk,
                    'jumlah'          => $jumlah,
                    'harga_satuan'    => $hargaExcel,
                    'subtotal'        => $subtotal,
                ];
            }

            if (! empty($trxItems)) {
                $validatedTrx[$nomor] = [
                    'outlet'    => $firstOutlet,
                    'tanggal'   => $firstTanggal,
                    'total'     => collect($trxItems)->sum('subtotal'),
                    'items'     => $trxItems,
                ];
                $importedRowsCount += count($trxItems);
            }
        }

        if (! empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'errors'  => $validationErrors,
            ], 422);
        }

        // ── 4. Simpan batch ini ke database ──────────────────────────
        DB::transaction(function () use ($validatedTrx, $karyawanId) {
            foreach ($validatedTrx as $nomor => $trxData) {
                $transaksi = Transaksi::create([
                    'wilayah_id'       => $trxData['outlet']->wilayah_id,
                    'outlet_id'        => $trxData['outlet']->outlet_id,
                    'karyawan_id'      => $karyawanId,
                    'nomor_transaksi'  => $nomor,
                    'tanggal'          => $trxData['tanggal'],
                    'total_transaksi'  => $trxData['total'],
                    'tipe_transaksi'   => 'Historis',
                ]);

                foreach ($trxData['items'] as $item) {
                    $transaksi->transaksiDetail()->create([
                        'produk_id'       => $item['produk']->produk_id,
                        'harga_produk_id' => $item['harga_produk']->harga_produk_id,
                        'jumlah'          => $item['jumlah'],
                        'harga_satuan'    => $item['harga_satuan'],
                        'subtotal'        => $item['subtotal'],
                    ]);
                }
            }
        });

        return response()->json([
            'success'               => true,
            'imported_transactions' => count($validatedTrx),
            'imported_rows'         => $importedRowsCount,
            'duplicates'            => $duplicates,
        ]);
    }

    /**
     * Cek progress import berdasarkan progressKey (fallback polling AJAX).
     */
    public function progress(Request $request)
    {
        $key      = $request->query('key', '');
        $progress = Cache::get($key);

        if (! $progress) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json($progress);
    }

    public function store(Request $request)
    {
        if (! $request->filled('karyawan_id')) {
            $karyawanId = auth()->user()?->karyawan?->karyawan_id ?? Karyawan::first()?->karyawan_id;
            if ($karyawanId) {
                $request->merge(['karyawan_id' => $karyawanId]);
            }
        }

        $validated = $request->validate([
            'file'        => ['required', 'file', 'mimes:xlsx,csv', 'max:10240'],
            'karyawan_id' => ['required', 'integer', 'exists:karyawan,karyawan_id'],
        ], [
            'file.required'        => 'Silakan pilih file Excel penjualan.',
            'file.mimes'           => 'File harus berformat XLSX atau CSV.',
            'file.max'             => 'Ukuran file maksimal 10 MB.',
            'karyawan_id.required' => 'Pilih karyawan penanggung jawab import.',
            'karyawan_id.exists'   => 'Karyawan yang dipilih tidak ditemukan.',
        ]);

        $progressKey = $request->input('progress_key') ?: ('import_progress_' . uniqid('', true));
        Cache::put($progressKey, [
            'processed' => 0,
            'total'     => 0,
            'errors'    => [],
            'done'      => false,
        ], now()->addMinutes(10));

        $importer = new PenjualanImport((int) $validated['karyawan_id'], $progressKey);

        try {
            Excel::import($importer, $request->file('file'));

            $importedCount  = $importer->getImportedCount();
            $duplicates     = $importer->getDuplicates();
            $duplicateCount = count($duplicates);

            Cache::put($progressKey, [
                'processed'  => $importedCount,
                'total'      => $importedCount,
                'duplicates' => $duplicates,
                'done'       => true,
            ], now()->addMinutes(5));

            if ($importedCount === 0 && $duplicateCount > 0) {
                return redirect()->route('transaksi.index')
                    ->with('warning', "Semua data transaksi ({$duplicateCount} transaksi) dilewati karena nomor transaksi sudah terdaftar di database.")
                    ->with('duplicates', $duplicates);
            }

            $redirect = redirect()->route('transaksi.index');

            if ($duplicateCount > 0) {
                $redirect->with('success', "Import berhasil! {$importedCount} transaksi baru berhasil disimpan.")
                    ->with('warning', "Terdapat {$duplicateCount} transaksi duplikat yang dilewati karena nomor transaksi sudah ada di database.")
                    ->with('duplicates', $duplicates);
            } else {
                $redirect->with('success', "Data historis berhasil diimport! Total {$importedCount} transaksi berhasil disimpan.");
            }

            return $redirect;
        } catch (ValidationException $e) {
            Cache::put($progressKey, [
                'processed' => 0,
                'total'     => 0,
                'errors'    => $e->errors()['file'] ?? [],
                'done'      => true,
            ], now()->addMinutes(5));

            return back()->withErrors($e->errors())->withInput();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Import gagal. Periksa log untuk detailnya!');
        }
    }

    private function parseTanggal(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)
                ->format('Y-m-d');
        }

        $str = trim((string) $value);

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $str, $m)) {
            return Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->toDateString();
        }

        return Carbon::parse($str)->toDateString();
    }
}
