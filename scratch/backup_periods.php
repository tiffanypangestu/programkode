<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$periods = ['2026-11', '2026-10', '2025-07', '2025-06'];

foreach ($periods as $p) {
    $penjualan = DB::table('penjualan')
        ->whereRaw('DATE_FORMAT(tanggal_penjualan, "%Y-%m") = ?', [$p])
        ->get();
    
    $pIds = $penjualan->pluck('penjualan_id')->all();
    
    $transaksi = DB::table('transaksi')
        ->whereIn('nomor_transaksi', array_map(fn($id) => "TR-$id", $pIds))
        ->get();
        
    $tIds = $transaksi->pluck('transaksi_id')->all();
    
    $details = DB::table('transaksi_detail')
        ->whereIn('transaksi_id', $tIds)
        ->get();
        
    $backup = [
        'period' => $p,
        'penjualan' => $penjualan,
        'transaksi' => $transaksi,
        'transaksi_detail' => $details,
    ];
    
    file_put_contents("scratch/backup_$p.json", json_encode($backup, JSON_PRETTY_PRINT));
    echo "Backed up $p: " . count($penjualan) . " penjualan, " . count($transaksi) . " transaksi, " . count($details) . " details.\n";
}
