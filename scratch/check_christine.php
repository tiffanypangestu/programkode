<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\AnalisisPerformaController();
$data = $controller->hitungIndikator('2026-06');

echo "Total outlets: " . count($data) . "\n\n";

$christines = [];
foreach ($data as $d) {
    if (stripos($d['nama_outlet'], 'Christine') !== false) {
        $christines[] = $d;
        echo "ID: {$d['id_outlet']} | {$d['nama_outlet']}\n";
        echo "  Omzet: " . number_format($d['omzet'], 0, ',', '.') . "\n";
        echo "  Growth: {$d['growth']}%\n";
        echo "  Frekuensi: {$d['frekuensi']}x\n";
        echo "  Jumlah Produk: {$d['jumlah_produk']}\n";
        echo "  Kontribusi: {$d['kontribusi']}%\n\n";
    }
}

file_put_contents('scratch/data_2026_06.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Saved to scratch/data_2026_06.json\n";
