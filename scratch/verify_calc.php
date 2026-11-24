<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new App\Http\Controllers\AnalisisPerformaController();

foreach (['2026-11', '2025-07'] as $p) {
    $data = $c->hitungIndikator($p);
    file_put_contents("scratch/indikator_{$p}_verified.json", json_encode($data, JSON_PRETTY_PRINT));
    echo "Saved indikator_{$p}_verified.json, count: " . count($data) . "\n";
}
