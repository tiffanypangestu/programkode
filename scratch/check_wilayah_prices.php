<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

for ($w = 1; $w <= 3; $w++) {
    $count = DB::table('harga_produk')->where('wilayah_id', $w)->count();
    $avgPrice = DB::table('harga_produk')->where('wilayah_id', $w)->avg('harga_produk');
    echo "Wilayah $w: $count harga_produk records, avg price: " . round($avgPrice, 2) . "\n";
}
