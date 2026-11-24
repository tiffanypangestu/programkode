<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$mismatch = DB::table('transaksi_detail')
    ->join('harga_produk', 'transaksi_detail.harga_produk_id', '=', 'harga_produk.harga_produk_id')
    ->whereRaw('transaksi_detail.harga_satuan != harga_produk.harga_produk')
    ->count();

echo "Mismatched harga_satuan vs harga_produk: $mismatch\n";

$sample = DB::table('transaksi_detail')
    ->join('harga_produk', 'transaksi_detail.harga_produk_id', '=', 'harga_produk.harga_produk_id')
    ->select('transaksi_detail.*', 'harga_produk.harga_produk as master_harga')
    ->limit(5)
    ->get();

foreach ($sample as $s) {
    echo "Detail ID: {$s->transaksi_detail_id}, Prod ID: {$s->produk_id}, Jml: {$s->jumlah}, Satuan: {$s->harga_satuan}, Master: {$s->master_harga}, Subtotal: {$s->subtotal}\n";
}
