<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$karyawan = DB::table('karyawan')
    ->join('role', 'karyawan.role_id', '=', 'role.role_id')
    ->select('karyawan.karyawan_id', 'karyawan.nama_karyawan', 'karyawan.jabatan', 'role.nama_role', 'role.role_id')
    ->get();

echo "TOTAL KARYAWAN: " . count($karyawan) . "\n";
foreach ($karyawan->take(10) as $k) {
    echo "ID: {$k->karyawan_id} | Nama: {$k->nama_karyawan} | Jabatan: {$k->jabatan} | Role: {$k->nama_role} (ID: {$k->role_id})\n";
}

$outletKaryawan = DB::table('karyawan_outlet')
    ->join('karyawan', 'karyawan_outlet.karyawan_id', '=', 'karyawan.karyawan_id')
    ->join('role', 'karyawan.role_id', '=', 'role.role_id')
    ->select('karyawan_outlet.outlet_id', 'karyawan.karyawan_id', 'karyawan.nama_karyawan', 'role.nama_role')
    ->get()
    ->groupBy('outlet_id');

echo "\nSAMPLE OUTLET ASSIGNED EMPLOYEES:\n";
foreach ($outletKaryawan->take(5) as $oid => $staff) {
    echo "Outlet $oid: " . $staff->pluck('nama_karyawan')->implode(', ') . "\n";
}
