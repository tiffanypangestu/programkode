<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PRODUK ===" . PHP_EOL;
$produk = DB::table('produk')->get();
foreach ($produk as $p) {
    echo "ID: {$p->produk_id} | Nama: {$p->nama_produk} | Harga: {$p->harga_produk}\n";
}

echo PHP_EOL . "=== HARGA PRODUK ===" . PHP_EOL;
$hp = DB::table('harga_produk')->get();
foreach ($hp as $h) {
    echo "ID: {$h->harga_produk_id} | ProdukID: {$h->produk_id} | WilayahID: {$h->wilayah_id} | Harga: {$h->harga_produk}\n";
}
