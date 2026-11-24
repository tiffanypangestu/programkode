<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AnalisisPerformaController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Login as admin
$admin = User::whereHas('role', fn($q) => $q->where('nama_role', 'Admin'))->first();
Auth::login($admin);

$controller = new AnalisisPerformaController();
$data = $controller->hitungIndikator('2026-11');

file_put_contents('scratch/indikator_2026_11.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Indikator saved, total outlets: " . count($data) . "\n";
