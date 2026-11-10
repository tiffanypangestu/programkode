<?php

namespace App\Imports;

use App\Models\HargaProduk;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    protected int $karyawan_id;

    protected string $progressKey;

    /** Daftar transaksi duplikat yang dilewati */
    protected array $duplicates = [];

    /** Jumlah transaksi baru yang berhasil disimpan */
    protected int $importedCount = 0;

    /** Ukuran batch pemrosesan data ke database */
    const CHUNK_SIZE = 100;

    public function __construct(int $karyawan_id, string $progressKey = '')
    {
        $this->karyawan_id = $karyawan_id;
        $this->progressKey = $progressKey ?: 'import_progress_' . uniqid('', true);
    }

    /** Kembalikan progress key agar controller bisa meneruskannya ke client */
    public function getProgressKey(): string
    {
        return $this->progressKey;
    }

    /** Kembalikan daftar transaksi duplikat yang dilewati */
    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    /** Kembalikan jumlah transaksi baru yang berhasil disimpan */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'File Excel tidak memiliki data penjualan.',
            ]);
        }

        // ── 1. Kelompokkan baris berdasarkan nomor transaksi ─────────
        $rawTrxGroups = [];
        $syntaxErrors = [];

        foreach ($rows as $index => $row) {
            $rowArr     = $row->toArray();
            $barisExcel = $index + 2;

            $nomor      = trim((string) ($rowArr['nomor_transaksi'] ?? ''));
            $kodeOutlet = trim((string) ($rowArr['kode_outlet'] ?? ''));
            $namaProduk = trim((string) ($rowArr['nama_produk'] ?? ''));

            // Lewati baris kosong atau baris summary/total
            if ($nomor === '' && $kodeOutlet === '' && $namaProduk === '') {
                continue;
            }

            if ($nomor === '') {
                $syntaxErrors[] = "Baris {$barisExcel}: kolom nomor_transaksi tidak boleh kosong.";
                continue;
            }

            if (strlen($nomor) > 50) {
                $syntaxErrors[] = "Baris {$barisExcel}: nomor transaksi maksimal 50 karakter.";
                continue;
            }

            $rawTrxGroups[$nomor][] = [
                'baris' => $barisExcel,
                'row'   => $rowArr,
            ];
        }

        if (! empty($syntaxErrors)) {
            throw ValidationException::withMessages(['file' => $syntaxErrors]);
        }

        if (empty($rawTrxGroups)) {
            throw ValidationException::withMessages([
                'file' => 'Tidak ada baris data transaksi yang valid dalam file Excel.',
            ]);
        }

        // ── 2. Cek nomor transaksi yang sudah ada di database ────────
        $allNomorTrx = array_keys($rawTrxGroups);
        $existingInDb = Transaksi::whereIn('nomor_transaksi', $allNomorTrx)
            ->pluck('nomor_transaksi')
            ->toArray();

        $existingMap = array_flip($existingInDb);

        // Pisahkan antara data baru dan data yang sudah ada (duplikat)
        $newTrxGroups = [];
        foreach ($rawTrxGroups as $nomor => $items) {
            if (isset($existingMap[$nomor])) {
                // Lewati (skip) nomor transaksi yang sudah ada di database!
                $firstRow = $items[0]['row'];
                $this->duplicates[] = [
                    'nomor_transaksi' => $nomor,
                    'total_baris'     => count($items),
                    'tanggal'         => $firstRow['tanggal'] ?? '-',
                    'kode_outlet'     => $firstRow['kode_outlet'] ?? '-',
                ];
            } else {
                $newTrxGroups[$nomor] = $items;
            }
        }

        // Update progress cache dengan info duplikat yang terdeteksi
        Cache::put($this->progressKey, [
            'processed'      => 0,
            'total'          => count($newTrxGroups),
            'duplicates'     => $this->duplicates,
            'current_batch'  => 0,
            'total_batches'  => ceil(count($newTrxGroups) / self::CHUNK_SIZE),
            'done'           => false,
        ], now()->addMinutes(10));

        // Jika semua transaksi sudah ada di database (semuanya duplikat)
        if (empty($newTrxGroups)) {
            Cache::put($this->progressKey, [
                'processed'      => 0,
                'total'          => 0,
                'duplicates'     => $this->duplicates,
                'done'           => true,
            ], now()->addMinutes(5));

            return;
        }

        // ── 3. Validasi baris data transaksi baru ─────────────────────
        $outlets = Outlet::with('wilayah')->get()->keyBy('kode_outlet');
        $produks = Produk::all()->groupBy('nama_produk');

        $validationErrors = [];
        $validatedTrx     = [];

        foreach ($newTrxGroups as $nomor => $items) {
            $firstTanggal = null;
            $firstOutlet  = null;
            $trxItems     = [];

            foreach ($items as $entry) {
                $baris  = $entry['baris'];
                $row    = $entry['row'];

                $kodeOutlet = trim((string) ($row['kode_outlet'] ?? ''));
                $namaProduk = trim((string) ($row['nama_produk'] ?? ''));

                if ($kodeOutlet === '' || $namaProduk === '' || ! isset($row['tanggal'], $row['jumlah'], $row['harga_satuan'])) {
                    $validationErrors[] = "Baris {$baris}: terdapat kolom wajib yang kosong.";
                    continue;
                }

                try {
                    $tanggal = $this->parseTanggal($row['tanggal']);
                } catch (\Throwable $e) {
                    $validationErrors[] = "Baris {$baris}: format tanggal tidak valid.";
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

                // Konsistensi outlet dan tanggal dalam satu transaksi
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
            }
        }

        if (! empty($validationErrors)) {
            Cache::put($this->progressKey, [
                'processed'      => 0,
                'total'          => count($newTrxGroups),
                'duplicates'     => $this->duplicates,
                'errors'         => $validationErrors,
                'done'           => true,
            ], now()->addMinutes(5));

            throw ValidationException::withMessages(['file' => $validationErrors]);
        }

        // ── 4. Simpan batch processing setiap 100 data ───────────────
        $trxCollection = collect($validatedTrx);
        $chunks        = $trxCollection->chunk(self::CHUNK_SIZE);
        $totalBatches  = $chunks->count();
        $processedTrx  = 0;

        foreach ($chunks as $batchIndex => $chunk) {
            DB::transaction(function () use ($chunk) {
                foreach ($chunk as $nomor => $trxData) {
                    $transaksi = Transaksi::create([
                        'wilayah_id'       => $trxData['outlet']->wilayah_id,
                        'outlet_id'        => $trxData['outlet']->outlet_id,
                        'karyawan_id'      => $this->karyawan_id,
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

            $processedTrx        += $chunk->count();
            $this->importedCount += $chunk->count();

            // Update status per batch di cache
            Cache::put($this->progressKey, [
                'processed'      => $processedTrx,
                'total'          => $trxCollection->count(),
                'current_batch'  => $batchIndex + 1,
                'total_batches'  => $totalBatches,
                'duplicates'     => $this->duplicates,
                'done'           => false,
            ], now()->addMinutes(10));
        }

        // Tandai selesai
        Cache::put($this->progressKey, [
            'processed'      => $processedTrx,
            'total'          => $trxCollection->count(),
            'duplicates'     => $this->duplicates,
            'current_batch'  => $totalBatches,
            'total_batches'  => $totalBatches,
            'done'           => true,
        ], now()->addMinutes(5));
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

        return Carbon::parse((string) $value)->toDateString();
    }
}
