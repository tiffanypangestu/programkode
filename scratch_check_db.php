<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== KARYAWAN ===" . PHP_EOL;
foreach(App\Models\Karyawan::take(5)->get() as $k) {
    echo "ID: {$k->karyawan_id}, Nama: {$k->nama_karyawan}" . PHP_EOL;
}

echo PHP_EOL . "=== OUTLETS ===" . PHP_EOL;
foreach(App\Models\Outlet::with('wilayah')->take(10)->get() as $o) {
    echo "Kode: {$o->kode_outlet}, Nama: {$o->nama_outlet}, Wilayah ID: {$o->wilayah_id}, Wilayah: " . ($o->wilayah->nama_wilayah ?? 'none') . PHP_EOL;
}

echo PHP_EOL . "=== PRODUK & HARGA ===" . PHP_EOL;
foreach(App\Models\Produk::take(10)->get() as $p) {
    echo "Produk ID: {$p->produk_id}, Nama: '{$p->nama_produk}'" . PHP_EOL;
    $hargas = App\Models\HargaProduk::where('produk_id', $p->produk_id)->get();
    foreach($hargas as $h) {
        echo "   Wilayah ID: {$h->wilayah_id}, Harga: {$h->harga_produk}, Mulai: {$h->tanggal_mulai}, Berakhir: {$h->tanggal_berakhir}" . PHP_EOL;
    }
}
