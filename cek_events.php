<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Lihat posisi terakhir binlog 37
$events = DB::select("SHOW BINLOG EVENTS IN 'binlog.000037' LIMIT 50");
foreach ($events as $e) {
    echo "Pos: {$e->Pos} | Type: {$e->Event_type} | Info: " . substr($e->Info, 0, 100) . PHP_EOL;
}
