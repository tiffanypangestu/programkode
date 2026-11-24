<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new App\Http\Controllers\AnalisisPerformaController();
$data = $c->hitungIndikator('2025-07');

file_put_contents('scratch/indikator_2025_07.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Indikator saved, total: " . count($data) . "\n";
