<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\PenjualanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    Excel::import(new PenjualanImport(1), 'c:\Users\tiffany\programkode\contoh_data_penjualan_2025.xlsx');
    echo "VALIDASI SUKSES 100%! Data valid dan siap diimport.\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation error:\n";
    print_r($e->errors());
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Transaction rolled back (database tetap bersih).\n";
}
