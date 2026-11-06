<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$outlets = DB::table('outlet')->get();
echo "Total outlet in DB: " . count($outlets) . PHP_EOL;
foreach ($outlets as $o) {
    echo "ID: {$o->outlet_id} | Code: {$o->kode_outlet} | Name: {$o->nama_outlet} | WilayahID: {$o->wilayah_id}\n";
}
