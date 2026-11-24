<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['2026-11', '2025-07'] as $p) {
    DB::table('analisis_performa')->updateOrInsert(
        ['periode_analisis' => $p],
        [
            'tanggal_analisis' => now()->toDateString(),
            'metode' => 'K-Means',
            'jumlah_cluster' => 4,
            'silhouette_score' => 0.6538,
            'status_analisis' => 'selesai',
            'updated_at' => now(),
        ]
    );
    echo "Updated analisis_performa for $p\n";
}
