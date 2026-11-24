<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'programkode3' 
      AND (COLUMN_NAME LIKE '%penjualan%' OR COLUMN_NAME LIKE '%transaksi%')
");

foreach ($columns as $c) {
    echo "{$c->TABLE_NAME}.{$c->COLUMN_NAME}\n";
}
