<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$karyawans = DB::table('karyawan')->get();
echo "Total karyawan: " . count($karyawans) . PHP_EOL;
foreach ($karyawans as $k) {
    echo "ID: {$k->karyawan_id} | Nama: {$k->nama_karyawan} | Jabatan: {$k->jabatan}\n";
}
