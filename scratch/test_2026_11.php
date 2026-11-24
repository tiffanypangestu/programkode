<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new App\Http\Controllers\AnalisisPerformaController();
$data = $c->hitungIndikator('2026-11');

file_put_contents('scratch/indikator_2026_11.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Indikator 2026-11 saved, total: " . count($data) . "\n";
