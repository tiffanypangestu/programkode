<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new App\Http\Controllers\AnalisisPerformaController();
$d = $c->hitungIndikator('2026-11');
file_put_contents('scratch/data_2026_11.json', json_encode($d));
echo "Total outlets: " . count($d) . "\n";
