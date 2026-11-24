<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$res = DB::table('analisis_performa')->where('periode_analisis', '2026-08')->first();
print_r($res);

$controller = new App\Http\Controllers\AnalisisPerformaController();
// hitungIndikator is private, let's reflect or look at hitungIndikator
