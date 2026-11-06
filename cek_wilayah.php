<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DAFTAR WILAYAH ===" . PHP_EOL;
$wilayah = DB::table('wilayah')->get();
foreach ($wilayah as $w) {
    echo "ID: {$w->wilayah_id} | Nama: {$w->nama_wilayah} | Deskripsi: {$w->deskripsi_wilayah}" . PHP_EOL;
}
