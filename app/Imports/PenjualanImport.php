<?php

namespace App\Imports;

use App\Models\HargaProduk;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  Collection  $collection
     */
    protected int $karyawan_id;

    public function __construct(int $karyawan_id)
    {
        $this->karyawan_id = $karyawan_id;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'File Excel tidak memiliki data penjualan.',
            ]);
        }

        $data = [];
        $errors = [];

        // validasi seluruh baris sebelum disimpan
        foreach ($rows as $index => $row) {
            $row = $row->toArray();
            $baris_excel = $index + 2;
            $nomor = trim((string) ($row['nomor_transaksi'] ?? ''));
            $kode_outlet = trim((string) ($row['kode_outlet'] ?? ''));
            $nama_produk = trim((string) ($row['nama_produk'] ?? ''));

            if ($nomor === '' || $kode_outlet === '' || $nama_produk === '' || ! isset($row['tanggal'], $row['jumlah'], $row['harga_satuan'])) {
                $errors[] = "Baris {$baris_excel}: terdapat kolom wajib yang kosong";
            }

            if (strlen($nomor) > 50) {
                $errors[] = "Baris {$baris_excel}: nomor transaksi maksimal 50 karakter.";

                continue;
            }

            try {
                $tanggal = $this->parseTanggal($row['tanggal']);
            } catch (\Throwable $e) {
                $errors[] = "Baris {$baris_excel}: format tanggal tidak valid.";

                continue;
            }

            if (
                ! is_numeric($row['jumlah']) ||
                (float) $row['jumlah'] <= 0 ||
                floor((float) $row['jumlah']) != (float) $row['jumlah']
            ) {
                $errors[] = "Baris {$baris_excel}: jumlah harus berupa bilangan bulat positif.";

                continue;
            }

            if (
                ! is_numeric($row['harga_satuan']) ||
                (float) $row['harga_satuan'] <= 0
            ) {
                $errors[] = "Baris {$baris_excel}: harga satuan harus lebih besar dari nol.";

                continue;
            }

            $outlet = Outlet::with('wilayah')->where('kode_outlet', $kode_outlet)->first();
            if (! $outlet || ! $outlet->wilayah) {
                $errors[] = "Baris {$baris_excel}: outlet {$kode_outlet} tidak ditemukan atau belum memiliki wilayah.";

                continue;
            }

            // Nama produk harus cocok tepat dengan satu produk.
            $produkList = Produk::where('nama_produk', $nama_produk)->get();

            if ($produkList->count() !== 1) {
                $errors[] = "Baris {$baris_excel}: produk '{$nama_produk}' tidak ditemukan atau namanya tidak unik.";

                continue;
            }

            $produk = $produkList->first();
            $hargaExcel = round((float) $row['harga_satuan'], 2);

            // cari harga yang sesuai dengan produk dan wilayah penjualan, tanggal transaksi, data historis dari excel
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
                $errors[] = "Baris {$baris_excel}: harga Rp".
                    number_format($hargaExcel, 2, ',', '.').
                    " tidak cocok dengan harga produk '{$nama_produk}' di wilayah outlet pada tanggal {$tanggal}.";

                continue;
            }

            $jumlah = (int) $row['jumlah'];
            $subtotal = round($jumlah * $hargaExcel, 2);

            $data[] = [
                'baris' => $baris_excel,
                'nomor_transaksi' => $nomor,
                'tanggal' => $tanggal,
                'outlet' => $outlet,
                'produk' => $produk,
                'harga_produk' => $hargaProduk,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaExcel,
                'subtotal' => $subtotal,
            ];

        }
        // Pastikan semua baris dalam satu transaksi konsisten.
        $groups = collect($data)->groupBy('nomor_transaksi');

        foreach ($groups as $nomor => $items) {
            $pertama = $items->first();

            foreach ($items as $item) {
                if (
                    $item['tanggal'] !== $pertama['tanggal'] ||
                    $item['outlet']->outlet_id !== $pertama['outlet']->outlet_id
                ) {
                    $errors[] = "Nomor transaksi {$nomor}: tanggal dan outlet harus konsisten  semua baris.";
                    break;
                }
            }

            if (Transaksi::where('nomor_transaksi', $nomor)->exists()) {
                $errors[] = "Nomor transaksi {$nomor} sudah ada di database.";
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages([
                'file' => $errors,
            ]);
        }

        // simpan seluruh transaksi dalam 1 database
        DB::transaction(function () use ($groups) {
            foreach ($groups as $nomor => $items) {
                $pertama = $items->first();
                $transaksi = Transaksi::create([
                    'wilayah_id' => $pertama['outlet']->wilayah_id,
                    'outlet_id' => $pertama['outlet']->outlet_id,
                    'karyawan_id' => $this->karyawan_id,
                    'nomor_transaksi' => $nomor,
                    'tanggal' => $pertama['tanggal'],
                    'total_transaksi' => $items->sum('subtotal'),
                    'tipe_transaksi' => 'Historis',
                ]);

                foreach ($items as $item) {
                    $transaksi->transaksiDetail()->create([
                        'produk_id' => $item['produk']->produk_id,
                        'harga_produk_id' => $item['harga_produk']->harga_produk_id,
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }
        });
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
