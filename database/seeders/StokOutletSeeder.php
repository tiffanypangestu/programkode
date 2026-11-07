<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\Produk;
use App\Models\StokOutlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokOutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::pluck('outlet_id');
        $produks = Produk::pluck('produk_id');

        if ($outlets->isEmpty() || $produks->isEmpty()) {
            return;
        }

        // Ambil semua total terjual sekaligus dalam 1 query ringkas
        $salesSummary = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
            ->select('transaksi.outlet_id', 'transaksi_detail.produk_id', DB::raw('SUM(transaksi_detail.jumlah) as total_terjual'))
            ->groupBy('transaksi.outlet_id', 'transaksi_detail.produk_id')
            ->get()
            ->keyBy(function ($row) {
                return $row->outlet_id . '-' . $row->produk_id;
            });

        $now = now();
        $batch = [];

        foreach ($outlets as $outletId) {
            foreach ($produks as $produkId) {
                $key = $outletId . '-' . $produkId;
                $terjual = isset($salesSummary[$key]) ? (int) $salesSummary[$key]->total_terjual : 0;
                $stokAwal = max($terjual + 35, 50);

                $batch[] = [
                    'outlet_id' => $outletId,
                    'produk_id' => $produkId,
                    'stok_masuk' => $stokAwal,
                    'stok_minimum' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert or ignore / upsert batch
        foreach (array_chunk($batch, 200) as $chunk) {
            StokOutlet::upsert(
                $chunk,
                ['outlet_id', 'produk_id'],
                ['stok_masuk', 'stok_minimum', 'updated_at']
            );
        }
    }
}
