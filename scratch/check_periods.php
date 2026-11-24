<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$periods = DB::table('penjualan')
    ->selectRaw('DATE_FORMAT(tanggal_penjualan, "%Y-%m") as periode, count(*) as count, sum(total_harga) as total')
    ->groupBy('periode')
    ->orderBy('periode')
    ->get();

echo "PERIODS IN DATABASE:\n";
foreach ($periods as $p) {
    echo "Periode: {$p->periode}, Count: {$p->count}, Total: {$p->total}\n";
}

$analisis = DB::table('analisis_performa')->get();
echo "\nANALISIS PERFORMA TABLE:\n";
foreach ($analisis as $a) {
    echo json_encode($a) . "\n";
}
