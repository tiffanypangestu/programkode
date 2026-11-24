<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek struktur tabel penjualan, transaksi, transaksi_detail
$penjualanSample = DB::table('penjualan')->whereRaw('DATE_FORMAT(tanggal_penjualan, "%Y-%m") = "2026-11"')->first();
echo "SAMPLE PENJUALAN 2026-11:\n" . json_encode($penjualanSample, JSON_PRETTY_PRINT) . "\n\n";

$outletCount = DB::table('outlet')->count();
echo "TOTAL OUTLETS: $outletCount\n";

// Cek bagaimana transaksi_detail berelasi dengan penjualan
$txSample = DB::table('transaksi')->where('nomor_transaksi', 'TR-' . ($penjualanSample->penjualan_id ?? 0))->first();
echo "SAMPLE TRANSAKSI:\n" . json_encode($txSample, JSON_PRETTY_PRINT) . "\n\n";

if ($txSample) {
    $detailSample = DB::table('transaksi_detail')->where('transaksi_id', $txSample->transaksi_id)->get();
    echo "SAMPLE TRANSAKSI DETAIL:\n" . json_encode($detailSample, JSON_PRETTY_PRINT) . "\n\n";
}
