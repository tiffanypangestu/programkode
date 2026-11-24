<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['penjualan', 'transaksi', 'transaksi_detail', 'outlet', 'produk', 'harga_produk', 'karyawan'] as $table) {
    echo "=== TABLE: $table ===\n";
    $columns = DB::select("DESCRIBE $table");
    foreach ($columns as $c) {
        echo "{$c->Field} ({$c->Type}) Null:{$c->Null} Key:{$c->Key} Default:{$c->Default}\n";
    }
    echo "\n";
}
