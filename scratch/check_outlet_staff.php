<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$outlets = DB::table('outlet')->pluck('outlet_id');
foreach ($outlets as $oid) {
    $existing = DB::table('penjualan')->where('outlet_id', $oid)->first();
    $staff = DB::table('karyawan_outlet')->where('outlet_id', $oid)->pluck('karyawan_id');
    echo "Outlet $oid: existing karyawan_id=" . ($existing->karyawan_id ?? 'none') . 
         ", bc=" . ($existing->bc_karyawan_id ?? 'none') . 
         ", spg=" . ($existing->spg_karyawan_id ?? 'none') . 
         ", assigned staff=" . $staff->implode(',') . "\n";
}
