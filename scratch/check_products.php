<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$products = DB::table('harga_produk')
    ->join('produk', 'harga_produk.produk_id', '=', 'produk.produk_id')
    ->select('harga_produk.harga_produk_id', 'harga_produk.produk_id', 'harga_produk.wilayah_id', 'produk.nama_produk', 'harga_produk.harga_produk')
    ->get();

echo "TOTAL HARGA PRODUK: " . count($products) . "\n";
foreach ($products->take(10) as $p) {
    echo "HP_ID: {$p->harga_produk_id} | Prod_ID: {$p->produk_id} | Wilayah: {$p->wilayah_id} | Nama: {$p->nama_produk} | Harga: {$p->harga_produk}\n";
}
