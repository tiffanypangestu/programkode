<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Definitions of the 40 outlets according to the 4-Cluster rules
// C1: Sangat Tinggi (Sangat Ramai) - 10 outlets
// C2: Tinggi (Ramai) - 10 outlets
// C3: Sedang - 10 outlets
// C4: Rendah (Sepi) - 10 outlets

$c1_configs = [
    92  => ['omzet' => 19500000, 'frek' => 7, 'qty' => 435, 'growth' => 25.0], // Toko Cantik
    94  => ['omzet' => 19200000, 'frek' => 7, 'qty' => 425, 'growth' => 10.5], // Olivia Salon Supplier
    95  => ['omzet' => 21200000, 'frek' => 8, 'qty' => 480, 'growth' => 22.1], // Christine Salon Cosmetics & Spa
    97  => ['omzet' => 20700000, 'frek' => 7, 'qty' => 465, 'growth' => 12.8], // Christine Cosmetic Salon & Supplier 3
    100 => ['omzet' => 21800000, 'frek' => 8, 'qty' => 495, 'growth' => 15.2], // Toko Maharani
    101 => ['omzet' => 20400000, 'frek' => 7, 'qty' => 455, 'growth' => 16.4], // Toko Djaya Cosmetic
    103 => ['omzet' => 19900000, 'frek' => 7, 'qty' => 440, 'growth' => 14.1], // Bandung Collection
    104 => ['omzet' => 22500000, 'frek' => 8, 'qty' => 510, 'growth' => 18.5], // Bandung Collection
    112 => ['omzet' => 18900000, 'frek' => 7, 'qty' => 415, 'growth' => 19.3], // Toko Amerta Dadi Cosmetic
    122 => ['omzet' => 18600000, 'frek' => 7, 'qty' => 410, 'growth' => 11.5], // D & D Store
];

$c2_configs = [
    98  => ['omzet' => 17200000, 'frek' => 6, 'qty' => 385, 'growth' => 32.5], // Christine Cosmetic & Salon Supplier 4
    102 => ['omzet' => 16800000, 'frek' => 6, 'qty' => 375, 'growth' => 28.4], // Toko FA Collection
    110 => ['omzet' => 16400000, 'frek' => 5, 'qty' => 360, 'growth' => 41.2], // Aris Jaya Salon Supplier
    113 => ['omzet' => 16000000, 'frek' => 6, 'qty' => 355, 'growth' => 25.8], // Central Swalayan
    116 => ['omzet' => 15700000, 'frek' => 5, 'qty' => 345, 'growth' => 38.0], // Toko Sehat Baru Mojosari
    117 => ['omzet' => 15400000, 'frek' => 6, 'qty' => 335, 'growth' => 22.4], // SEHAT
    120 => ['omzet' => 15100000, 'frek' => 5, 'qty' => 330, 'growth' => 35.1], // Samudra Swalayan
    125 => ['omzet' => 14800000, 'frek' => 5, 'qty' => 320, 'growth' => 29.6], // Sri Rejeki
    127 => ['omzet' => 14500000, 'frek' => 6, 'qty' => 315, 'growth' => 24.3], // Toko Melody Cosmetic
    130 => ['omzet' => 14200000, 'frek' => 5, 'qty' => 310, 'growth' => 31.0], // Toko Sandat
];

$c3_configs = [
    105 => ['omzet' => 12900000, 'frek' => 4, 'qty' => 275, 'growth' => 12.5], // Salon Yong
    106 => ['omzet' => 12500000, 'frek' => 4, 'qty' => 265, 'growth' => 8.2],  // Supermarket Sinar Terang
    107 => ['omzet' => 12100000, 'frek' => 3, 'qty' => 255, 'growth' => 14.0], // Toko Duta Swalayan
    108 => ['omzet' => 11700000, 'frek' => 4, 'qty' => 245, 'growth' => 6.5],  // PRIMA
    109 => ['omzet' => 11400000, 'frek' => 3, 'qty' => 240, 'growth' => 10.8], // Remaja Swalayan
    114 => ['omzet' => 11000000, 'frek' => 4, 'qty' => 230, 'growth' => 5.2],  // TOKO KARTINI
    119 => ['omzet' => 10700000, 'frek' => 3, 'qty' => 225, 'growth' => 11.4], // Sumber Baru Swalayan
    121 => ['omzet' => 10400000, 'frek' => 3, 'qty' => 215, 'growth' => 7.8],  // Toko Blums
    128 => ['omzet' => 10100000, 'frek' => 4, 'qty' => 210, 'growth' => 4.3],  // Toserba Diva
    129 => ['omzet' => 9800000,  'frek' => 3, 'qty' => 205, 'growth' => 9.1],  // Toko Sehat Ayu Kosmetik
];

$c4_configs = [
    93  => ['omzet' => 7900000, 'frek' => 2, 'qty' => 175, 'growth' => -22.5], // Central Supermarket
    96  => ['omzet' => 7500000, 'frek' => 2, 'qty' => 165, 'growth' => -31.0], // Christine Salon Supplier 2
    99  => ['omzet' => 7100000, 'frek' => 2, 'qty' => 155, 'growth' => -18.4], // Christine Cosmetic & Salon Supplier 5
    111 => ['omzet' => 6700000, 'frek' => 1, 'qty' => 145, 'growth' => -42.1], // Arjuno
    115 => ['omzet' => 6400000, 'frek' => 2, 'qty' => 135, 'growth' => -26.5], // Toko Sumber Sehat
    118 => ['omzet' => 6000000, 'frek' => 1, 'qty' => 125, 'growth' => -38.2], // BAGUS
    123 => ['omzet' => 5700000, 'frek' => 2, 'qty' => 115, 'growth' => -21.0], // DEA WIJAYA
    124 => ['omzet' => 5400000, 'frek' => 1, 'qty' => 105, 'growth' => -45.5], // BAGUS
    126 => ['omzet' => 5000000, 'frek' => 1, 'qty' => 95,  'growth' => -33.8], // Toko Agung (Cik Niken)
    131 => ['omzet' => 4600000, 'frek' => 1, 'qty' => 85,  'growth' => -29.4], // TOKO RAHAYU
];

$all_configs = $c1_configs + $c2_configs + $c3_configs + $c4_configs;

echo "Total outlets configured: " . count($all_configs) . "\n";

// Outlet metadata (wilayah and staff)
$outletsMeta = [];
foreach ($all_configs as $oid => $cfg) {
    $outlet = DB::table('outlet')->where('outlet_id', $oid)->first();
    $existing = DB::table('penjualan')->where('outlet_id', $oid)->first();
    
    $outletsMeta[$oid] = [
        'wilayah_id' => $outlet->wilayah_id ?? 1,
        'karyawan_id' => $existing->karyawan_id ?? 64,
        'bc_karyawan_id' => $existing->bc_karyawan_id ?? 104,
        'spg_karyawan_id' => $existing->spg_karyawan_id ?? 64,
    ];
}

// Fetch products per wilayah
$productsByWilayah = [];
for ($w = 1; $w <= 3; $w++) {
    $productsByWilayah[$w] = DB::table('harga_produk')
        ->where('wilayah_id', $w)
        ->select('harga_produk_id', 'produk_id', 'harga_produk')
        ->orderBy('harga_produk', 'asc')
        ->get();
}

// Target periods to populate
$periodPairs = [
    ['current' => '2026-11', 'prev' => '2026-10'],
    ['current' => '2025-07', 'prev' => '2025-06'],
];

DB::beginTransaction();
try {
    foreach ($periodPairs as $pair) {
        $curr = $pair['current'];
        $prev = $pair['prev'];
        
        echo "\nProcessing period pair: $curr (current) & $prev (prev)...\n";
        
        // 1. Delete existing records for current and prev
        foreach ([$curr, $prev] as $m) {
            $penjualanIds = DB::table('penjualan')
                ->whereRaw('DATE_FORMAT(tanggal_penjualan, "%Y-%m") = ?', [$m])
                ->pluck('penjualan_id')
                ->all();
                
            if (!empty($penjualanIds)) {
                $trNos = array_map(fn($id) => "TR-$id", $penjualanIds);
                $txIds = DB::table('transaksi')->whereIn('nomor_transaksi', $trNos)->pluck('transaksi_id')->all();
                
                if (!empty($txIds)) {
                    DB::table('transaksi_detail')->whereIn('transaksi_id', $txIds)->delete();
                    DB::table('transaksi')->whereIn('transaksi_id', $txIds)->delete();
                }
                DB::table('penjualan')->whereIn('penjualan_id', $penjualanIds)->delete();
            }
        }
        
        // 2. Generate records for prev month (to produce exact growth)
        foreach ($all_configs as $oid => $cfg) {
            $meta = $outletsMeta[$oid];
            $wId = $meta['wilayah_id'];
            $prods = $productsByWilayah[$wId];
            
            // Calculate previous month target omzet:
            // growth = (omzet - omzet_lalu) / omzet_lalu * 100
            // omzet_lalu = omzet / (1 + growth / 100)
            $growth = $cfg['growth'];
            $omzet_lalu = round($cfg['omzet'] / (1 + ($growth / 100)), 2);
            $frek_lalu = max(1, min(6, $cfg['frek']));
            $qty_lalu = max(50, (int) round($cfg['qty'] / (1 + ($growth / 100))));
            
            generateMonthlySales($oid, $prev, $omzet_lalu, $frek_lalu, $qty_lalu, $meta, $prods);
        }
        
        // 3. Generate records for current month
        foreach ($all_configs as $oid => $cfg) {
            $meta = $outletsMeta[$oid];
            $wId = $meta['wilayah_id'];
            $prods = $productsByWilayah[$wId];
            
            generateMonthlySales($oid, $curr, $cfg['omzet'], $cfg['frek'], $cfg['qty'], $meta, $prods);
        }
    }
    
    DB::commit();
    echo "\nDatabase update completed successfully!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

function generateMonthlySales($outletId, $yearMonth, $targetOmzet, $frekuensi, $targetQty, $meta, $prods)
{
    // Distribute omzet and qty across $frekuensi transactions
    list($year, $month) = explode('-', $yearMonth);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
    
    // Choose spaced out days
    $step = max(1, (int) floor($daysInMonth / ($frekuensi + 1)));
    $days = [];
    for ($i = 1; $i <= $frekuensi; $i++) {
        $days[] = min($daysInMonth, $i * $step);
    }
    
    $omzetPerTx = [];
    $qtyPerTx = [];
    $baseOmzet = floor($targetOmzet / $frekuensi);
    $remOmzet = $targetOmzet - ($baseOmzet * $frekuensi);
    
    $baseQty = floor($targetQty / $frekuensi);
    $remQty = $targetQty - ($baseQty * $frekuensi);
    
    for ($i = 0; $i < $frekuensi; $i++) {
        $omzetPerTx[$i] = $baseOmzet + ($i == $frekuensi - 1 ? $remOmzet : 0);
        $qtyPerTx[$i] = $baseQty + ($i == $frekuensi - 1 ? $remQty : 0);
    }
    
    for ($i = 0; $i < $frekuensi; $i++) {
        $txOmzet = $omzetPerTx[$i];
        $txQty = $qtyPerTx[$i];
        $dayStr = str_pad($days[$i], 2, '0', STR_PAD_LEFT);
        $txDate = "$yearMonth-$dayStr";
        
        // 1. Insert penjualan
        $penjualanId = DB::table('penjualan')->insertGetId([
            'outlet_id' => $outletId,
            'karyawan_id' => $meta['karyawan_id'],
            'bc_karyawan_id' => $meta['bc_karyawan_id'],
            'spg_karyawan_id' => $meta['spg_karyawan_id'],
            'tanggal_penjualan' => $txDate,
            'total_harga' => $txOmzet,
            'status_penjualan' => 'selesai',
            'created_at' => "$txDate 10:00:00",
            'updated_at' => "$txDate 10:00:00",
        ]);
        
        // 2. Insert transaksi
        $transaksiId = DB::table('transaksi')->insertGetId([
            'wilayah_id' => $meta['wilayah_id'],
            'outlet_id' => $outletId,
            'karyawan_id' => $meta['karyawan_id'],
            'nomor_transaksi' => "TR-$penjualanId",
            'tanggal' => $txDate,
            'total_transaksi' => $txOmzet,
            'tipe_transaksi' => 'penjualan',
            'created_at' => "$txDate 10:00:00",
            'updated_at' => "$txDate 10:00:00",
        ]);
        
        // 3. Insert transaksi_detail
        // Pick 2-3 products and distribute $txQty and $txOmzet
        // To make sure sum(subtotal) == txOmzet and sum(jumlah) == txQty:
        // We pick product 1 with price P1 and product 2 with price P2.
        // We can solve: q1 + q2 = txQty and q1*P1 + q2*P2 = txOmzet.
        // Average price needed = txOmzet / txQty
        $targetAvg = $txOmzet / max(1, $txQty);
        
        // Find one product with price <= targetAvg and one with price >= targetAvg
        $lowerProd = null;
        $upperProd = null;
        foreach ($prods as $p) {
            if ($p->harga_produk <= $targetAvg) {
                $lowerProd = $p;
            }
            if ($p->harga_produk >= $targetAvg && !$upperProd) {
                $upperProd = $p;
            }
        }
        
        if (!$lowerProd) $lowerProd = $prods->first();
        if (!$upperProd) $upperProd = $prods->last();
        
        $p1 = (float)$lowerProd->harga_produk;
        $p2 = (float)$upperProd->harga_produk;
        
        if ($p1 == $p2) {
            $q1 = $txQty;
            $q2 = 0;
            $subtotal1 = $txOmzet;
            $subtotal2 = 0;
        } else {
            // q1 * p1 + (txQty - q1) * p2 = txOmzet
            // q1 * (p1 - p2) = txOmzet - txQty * p2
            $q1 = (int) round(($txOmzet - ($txQty * $p2)) / ($p1 - $p2));
            $q1 = max(0, min($txQty, $q1));
            $q2 = $txQty - $q1;
            
            $subtotal1 = round($q1 * $p1, 2);
            $subtotal2 = round($txOmzet - $subtotal1, 2);
        }
        
        if ($q1 > 0) {
            DB::table('transaksi_detail')->insert([
                'transaksi_id' => $transaksiId,
                'produk_id' => $lowerProd->produk_id,
                'harga_produk_id' => $lowerProd->harga_produk_id,
                'jumlah' => $q1,
                'harga_satuan' => $p1,
                'subtotal' => $subtotal1,
                'created_at' => "$txDate 10:00:00",
                'updated_at' => "$txDate 10:00:00",
            ]);
        }
        
        if ($q2 > 0) {
            DB::table('transaksi_detail')->insert([
                'transaksi_id' => $transaksiId,
                'produk_id' => $upperProd->produk_id,
                'harga_produk_id' => $upperProd->harga_produk_id,
                'jumlah' => $q2,
                'harga_satuan' => $p2,
                'subtotal' => $subtotal2,
                'created_at' => "$txDate 10:00:00",
                'updated_at' => "$txDate 10:00:00",
            ]);
        }
    }
}
