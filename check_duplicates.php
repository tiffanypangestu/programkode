<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duplicates = DB::table('produk')
    ->select('nama_produk', DB::raw('count(*) as c'))
    ->groupBy('nama_produk')
    ->having('c', '>', 1)
    ->get();

echo "Duplicate product names: " . $duplicates->count() . PHP_EOL;

// Check existing nomor_transaksi in transaksi
$trx = DB::table('transaksi')->pluck('nomor_transaksi')->take(10);
echo "Contoh nomor transaksi di DB: " . json_encode($trx) . PHP_EOL;
echo "Total transaksi di DB: " . DB::table('transaksi')->count() . PHP_EOL;
