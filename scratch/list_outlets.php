<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$outlets = DB::table('outlet')
    ->leftJoin('wilayah', 'outlet.wilayah_id', '=', 'wilayah.wilayah_id')
    ->select('outlet.outlet_id', 'outlet.nama_outlet', 'outlet.tipe_outlet', 'wilayah.nama_wilayah')
    ->orderBy('outlet.outlet_id')
    ->get();

foreach ($outlets as $o) {
    echo "ID: {$o->outlet_id} | Nama: {$o->nama_outlet} | Wilayah: {$o->nama_wilayah} | Tipe: {$o->tipe_outlet}\n";
}
