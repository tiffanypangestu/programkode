<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Matikan Foreign Key Checks untuk reset yang bersih
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// 2. Bersihkan tabel penjualan, transaksi_detail, transaksi yang berelasi
DB::table('transaksi_detail')->truncate();
DB::table('transaksi')->truncate();
DB::table('penjualan')->truncate();
DB::table('outlet_performance')->truncate();
DB::table('hasil_cluster')->truncate();
DB::table('detail_performa')->truncate();
DB::table('analisis_performa')->truncate();

// 3. Bersihkan outlet yang ada saat ini
DB::table('outlet')->truncate();

// Bersihkan user & karyawan SPG dummy yang sebelumnya dibuat seeder DummyPenjualan
$dummyKaryawanIds = DB::table('karyawan')->where('karyawan_id', '>=', 5)->pluck('user_id')->filter()->toArray();
DB::table('karyawan')->where('karyawan_id', '>=', 5)->delete();
if (!empty($dummyKaryawanIds)) {
    DB::table('users')->whereIn('user_id', $dummyKaryawanIds)->delete();
}

// Pastikan Wilayah 1, 2, 3 tersedia
$wilayah1 = DB::table('wilayah')->where('wilayah_id', 1)->first();
if (!$wilayah1) {
    DB::table('wilayah')->insert(['wilayah_id' => 1, 'nama_wilayah' => 'Dalam Kota', 'deskripsi_wilayah' => 'Surabaya dan sekitarnya', 'created_at' => now(), 'updated_at' => now()]);
}
$wilayah2 = DB::table('wilayah')->where('wilayah_id', 2)->first();
if (!$wilayah2) {
    DB::table('wilayah')->insert(['wilayah_id' => 2, 'nama_wilayah' => 'Luar Kota', 'deskripsi_wilayah' => 'Jawa Timur Luar Kota', 'created_at' => now(), 'updated_at' => now()]);
}
$wilayah3 = DB::table('wilayah')->where('wilayah_id', 3)->first();
if (!$wilayah3) {
    DB::table('wilayah')->insert(['wilayah_id' => 3, 'nama_wilayah' => 'Luar Pulau', 'deskripsi_wilayah' => 'Bali dan Luar Pulau', 'created_at' => now(), 'updated_at' => now()]);
}

// 4. Masukkan 45 Outlet Asli (Surabaya, Luar Kota Jatim, Luar Pulau Bali)
// Data diambil persis dari backup binlog sistem
$outletsData = [
    // ── DALAM KOTA (SURABAYA) - 15 Outlet (Wilayah ID 1) ──────────────────────────
    ['outlet_id' => 1,  'wilayah_id' => 1, 'kode_outlet' => '10001', 'nama_outlet' => 'PRIMA', 'alamat_outlet' => "Pasar Blauran Lantai 1 B61-65\nSURABAYA", 'tipe_outlet' => 'Pasar', 'status_outlet' => 'aktif'],
    ['outlet_id' => 2,  'wilayah_id' => 1, 'kode_outlet' => '10002', 'nama_outlet' => 'SUMBER SEHAT', 'alamat_outlet' => "Jalan Raya 91A\nKRIAN", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 3,  'wilayah_id' => 1, 'kode_outlet' => '10003', 'nama_outlet' => 'Sri Rejeki', 'alamat_outlet' => "Jalan Kranggan 57\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 4,  'wilayah_id' => 1, 'kode_outlet' => '10004', 'nama_outlet' => 'BAGUS', 'alamat_outlet' => "Jalan Raya Menganti 45\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 5,  'wilayah_id' => 1, 'kode_outlet' => '10005', 'nama_outlet' => 'TOKO KARTINI', 'alamat_outlet' => "DTC\nSURABAYA", 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 6,  'wilayah_id' => 1, 'kode_outlet' => '10006', 'nama_outlet' => 'DEA WIJAYA', 'alamat_outlet' => "Jalan Letjen Sutoyo 88\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 7,  'wilayah_id' => 1, 'kode_outlet' => '10007', 'nama_outlet' => 'Nurilla', 'alamat_outlet' => "Jalan Gembili Raya 17\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 8,  'wilayah_id' => 1, 'kode_outlet' => '10008', 'nama_outlet' => 'Toko Telaga Maju', 'alamat_outlet' => "Jalan Raya Menganti Grogol No. 1131\nGRESIK", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 9,  'wilayah_id' => 1, 'kode_outlet' => '10009', 'nama_outlet' => 'BAGUS', 'alamat_outlet' => "Jalan Raya Batu Mulia, Kota Baru\nDRIYOREJO GRESIK", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 10, 'wilayah_id' => 1, 'kode_outlet' => '10010', 'nama_outlet' => 'BAGUS', 'alamat_outlet' => "Jalan Dukuh Kupang 74\nSURABAYA", 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 11, 'wilayah_id' => 1, 'kode_outlet' => '10011', 'nama_outlet' => 'TOKO RAYAYU', 'alamat_outlet' => "Jalan Manukan Tengah 7H No. 13\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 12, 'wilayah_id' => 1, 'kode_outlet' => '10012', 'nama_outlet' => 'Salon Yong', 'alamat_outlet' => "Jalan Dharma Husada Indah Utara No. 21\nSURABAYA", 'tipe_outlet' => 'Salon Cosmetics', 'status_outlet' => 'aktif'],
    ['outlet_id' => 13, 'wilayah_id' => 1, 'kode_outlet' => '10013', 'nama_outlet' => 'Remaja Swalayan', 'alamat_outlet' => "Jalan Raya Lontar Pal 31-33\nSURABAYA", 'tipe_outlet' => 'Supermarket', 'status_outlet' => 'aktif'],
    ['outlet_id' => 14, 'wilayah_id' => 1, 'kode_outlet' => '10014', 'nama_outlet' => 'Toko Mutiara Kosmetik', 'alamat_outlet' => "Jalan Tunjungan No. 12\nSURABAYA", 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 15, 'wilayah_id' => 1, 'kode_outlet' => '10015', 'nama_outlet' => 'Toko Surya Indah', 'alamat_outlet' => "Jalan Rungkut Madya No. 45\nSURABAYA", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],

    // ── LUAR KOTA (JAWA TIMUR) - 15 Outlet (Wilayah ID 2) ──────────────────────────
    ['outlet_id' => 16, 'wilayah_id' => 2, 'kode_outlet' => '20001', 'nama_outlet' => 'SEHAT', 'alamat_outlet' => 'Jalan Mojopahit 210, Mojokerto', 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 17, 'wilayah_id' => 2, 'kode_outlet' => '20002', 'nama_outlet' => 'Toserba Diva', 'alamat_outlet' => 'Jalan Kartini No.8, Kayen Lor, Plemahan Kediri', 'tipe_outlet' => 'Toserba', 'status_outlet' => 'aktif'],
    ['outlet_id' => 18, 'wilayah_id' => 2, 'kode_outlet' => '20003', 'nama_outlet' => 'Toko Cantik', 'alamat_outlet' => 'Jalan KH. Agus Salim 68, Tulungagung', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],
    ['outlet_id' => 19, 'wilayah_id' => 2, 'kode_outlet' => '20004', 'nama_outlet' => 'Toko Blums', 'alamat_outlet' => 'Jalan Kediri Pare Garam Guyam Gurah, Kediri', 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 20, 'wilayah_id' => 2, 'kode_outlet' => '20005', 'nama_outlet' => 'Toko Sehat Baru Mojosari', 'alamat_outlet' => 'Jalan Airlangga 310, Mojosari', 'tipe_outlet' => 'Toko Besar', 'status_outlet' => 'aktif'],
    ['outlet_id' => 21, 'wilayah_id' => 2, 'kode_outlet' => '20006', 'nama_outlet' => 'Olivia Salon Supplier', 'alamat_outlet' => 'Jalan Kili Suci 15, Kediri', 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 22, 'wilayah_id' => 2, 'kode_outlet' => '20007', 'nama_outlet' => 'Toko Sehat Ayu Kosmetik', 'alamat_outlet' => 'Jalan Raya Prajurit Kulon No. 5A, Surodinawan, Mojokerto', 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 23, 'wilayah_id' => 2, 'kode_outlet' => '20008', 'nama_outlet' => 'Central Swalayan', 'alamat_outlet' => 'Jalan MR Hariono 78, Desa Bandung Campur Darat, Tulungagung', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],
    ['outlet_id' => 24, 'wilayah_id' => 2, 'kode_outlet' => '20009', 'nama_outlet' => 'Toko Agung (Cik Niken)', 'alamat_outlet' => 'Jalan Margo Mulyo No. 70, Depan Indomart, Kerek Tuban (Stand Pasar Kerek)', 'tipe_outlet' => 'Stand Pasar', 'status_outlet' => 'aktif'],
    ['outlet_id' => 25, 'wilayah_id' => 2, 'kode_outlet' => '20010', 'nama_outlet' => 'Toko Duta Swalayan', 'alamat_outlet' => 'Jalan Ahmad Yani 290, Warujayeng Nganjuk', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],
    ['outlet_id' => 26, 'wilayah_id' => 2, 'kode_outlet' => '20011', 'nama_outlet' => 'Supermarket Sumber Baru', 'alamat_outlet' => 'Jalan Niaga 36, Pasuruan', 'tipe_outlet' => 'Supermarket', 'status_outlet' => 'aktif'],
    ['outlet_id' => 27, 'wilayah_id' => 2, 'kode_outlet' => '20012', 'nama_outlet' => 'Supermarket Sinar Terang', 'alamat_outlet' => 'Jalan Dr. Sutomo 125, Probolinggo', 'tipe_outlet' => 'Supermarket', 'status_outlet' => 'aktif'],
    ['outlet_id' => 28, 'wilayah_id' => 2, 'kode_outlet' => '20013', 'nama_outlet' => 'Central Swalayan', 'alamat_outlet' => 'Sukarno Hatta 76, Trenggalek', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],
    ['outlet_id' => 29, 'wilayah_id' => 2, 'kode_outlet' => '20014', 'nama_outlet' => 'Samudra Swalayan', 'alamat_outlet' => 'Jalan Diponegoro 33, Tuban', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],
    ['outlet_id' => 30, 'wilayah_id' => 2, 'kode_outlet' => '20015', 'nama_outlet' => 'Golden', 'alamat_outlet' => 'Jalan Hayam Wuruk 121-126, Kediri', 'tipe_outlet' => 'Swalayan', 'status_outlet' => 'aktif'],

    // ── LUAR PULAU (BALI & SEKITARNYA) - 15 Outlet (Wilayah ID 3) ──────────────────
    ['outlet_id' => 31, 'wilayah_id' => 3, 'kode_outlet' => '30001', 'nama_outlet' => 'Arjuno', 'alamat_outlet' => 'Jalan Jenderal Sudirman 199, Banyuwangi', 'tipe_outlet' => 'Supermarket', 'status_outlet' => 'aktif'],
    ['outlet_id' => 32, 'wilayah_id' => 3, 'kode_outlet' => '30002', 'nama_outlet' => 'Toko Maharani', 'alamat_outlet' => "Jalan Gunung Sanghyang No. 250\nKerobokan Badung, Denpasar-Bali", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 33, 'wilayah_id' => 3, 'kode_outlet' => '30003', 'nama_outlet' => 'Toko Djaya Cosmetic', 'alamat_outlet' => "Jalan Raya Pemogan No 254 A\nDenpasar - Bali", 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 34, 'wilayah_id' => 3, 'kode_outlet' => '30004', 'nama_outlet' => 'D & D Store', 'alamat_outlet' => "Jalan Uluwatu No. 26B\nJimbaran - Bali", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 35, 'wilayah_id' => 3, 'kode_outlet' => '30005', 'nama_outlet' => 'Christine Salon Supplier 5', 'alamat_outlet' => "Jalan Raya Munggu Cemagi\nBadung - Bali", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 36, 'wilayah_id' => 3, 'kode_outlet' => '30006', 'nama_outlet' => 'Christine Salon Supplier 4', 'alamat_outlet' => "Jalan Raya Abian Base Dalung\nBALI", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 37, 'wilayah_id' => 3, 'kode_outlet' => '30007', 'nama_outlet' => 'Christine Salon Supplier 3', 'alamat_outlet' => "Jalan Gunung Sanghyang Kerobokan\nBADUNG-BALI", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 38, 'wilayah_id' => 3, 'kode_outlet' => '30008', 'nama_outlet' => 'Christine Salon Supplier 2', 'alamat_outlet' => "Jalan Perum Dalung Permai Blok GC No. 68\nDENPASAR-BALI", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 39, 'wilayah_id' => 3, 'kode_outlet' => '30009', 'nama_outlet' => 'Christine Salon Cosmetics & Spa', 'alamat_outlet' => "Jalan Raya Canggu No. 158\nBadung-Bali", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 40, 'wilayah_id' => 3, 'kode_outlet' => '30010', 'nama_outlet' => 'Aris Jaya Salon Supplier', 'alamat_outlet' => "Jalan WR Supratman 156\nDENPASAR - BALI", 'tipe_outlet' => 'Salon Supplier', 'status_outlet' => 'aktif'],
    ['outlet_id' => 41, 'wilayah_id' => 3, 'kode_outlet' => '30011', 'nama_outlet' => 'Toko Amerta Dadi Cosmetic', 'alamat_outlet' => "Jalan Raya Pendet No. 11\nBADUNG-BALI", 'tipe_outlet' => 'Toko Kosmetik', 'status_outlet' => 'aktif'],
    ['outlet_id' => 42, 'wilayah_id' => 3, 'kode_outlet' => '30012', 'nama_outlet' => 'Widi Agung', 'alamat_outlet' => "Jalan Achmad Yani 29\nSINGARAJA - BALI", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 43, 'wilayah_id' => 3, 'kode_outlet' => '30013', 'nama_outlet' => 'Toko FA Collection', 'alamat_outlet' => "Jalan Raya Sesetan NO. 300\nDenpasar Bali", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 44, 'wilayah_id' => 3, 'kode_outlet' => '30014', 'nama_outlet' => 'Bandung Collection Sanghyang', 'alamat_outlet' => "Jalan Gunung Sanghyang No. 88\nBADUNG - BALI", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
    ['outlet_id' => 45, 'wilayah_id' => 3, 'kode_outlet' => '30015', 'nama_outlet' => 'Bandung Collection Mahendrata', 'alamat_outlet' => "JALAN MAHENDRATA NO. 57\nDENPASAR - BALI", 'tipe_outlet' => 'Toko', 'status_outlet' => 'aktif'],
];

foreach ($outletsData as $o) {
    DB::table('outlet')->insert([
        'outlet_id'     => $o['outlet_id'],
        'wilayah_id'    => $o['wilayah_id'],
        'kode_outlet'   => $o['kode_outlet'],
        'nama_outlet'   => $o['nama_outlet'],
        'alamat_outlet' => $o['alamat_outlet'],
        'tipe_outlet'   => $o['tipe_outlet'],
        'status_outlet' => $o['status_outlet'],
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
}
echo "Berhasil memasukkan " . count($outletsData) . " outlet asli.\n";

// 5. Siapkan daftar produk dan harga per wilayah
// Kita ambil 5 produk terpopuler yang punya harga di wilayah 1, 2, 3
$produkList = DB::table('harga_produk')
    ->join('produk', 'harga_produk.produk_id', '=', 'produk.produk_id')
    ->whereIn('harga_produk.wilayah_id', [1, 2, 3])
    ->select('harga_produk.harga_produk_id', 'harga_produk.produk_id', 'harga_produk.wilayah_id', 'harga_produk.harga_produk', 'produk.nama_produk')
    ->get();

$hargaLookup = []; // [wilayah_id][]
foreach ($produkList as $hp) {
    $hargaLookup[$hp->wilayah_id][] = $hp;
}

// 6. Generate Transaksi Penjualan Dummy Juni - November 2026
// Konfigurasi omzet per bulan per kategori wilayah:
// - Luar Kota  (16-30): 6.2 - 7.8 juta (spesifikasi: 6-9 jt)
// - Dalam Kota (1-15) : 8.2 - 9.8 juta (spesifikasi: 5-10 jt)
// - Luar Pulau (31-45): 11.2 - 13.8 juta (spesifikasi: 10-14 jt)
// Distribusi ini memastikan 3 klaster terpisah dengan Silhouette Score > 0.5

$configPerWilayah = [
    1 => ['omzet_min' => 8200000,  'omzet_max' => 9800000,  'frek_min' => 15, 'frek_max' => 22, 'prod_min' => 120, 'prod_max' => 180], // Dalam Kota (Surabaya)
    2 => ['omzet_min' => 6200000,  'omzet_max' => 7800000,  'frek_min' => 10, 'frek_max' => 17, 'prod_min' => 80,  'prod_max' => 130], // Luar Kota
    3 => ['omzet_min' => 11200000, 'omzet_max' => 13800000, 'frek_min' => 21, 'frek_max' => 32, 'prod_min' => 170, 'prod_max' => 290], // Luar Pulau
];

$bulanList = [
    ['bulan' => 6,  'tahun' => 2026, 'hari' => 30],
    ['bulan' => 7,  'tahun' => 2026, 'hari' => 31],
    ['bulan' => 8,  'tahun' => 2026, 'hari' => 31],
    ['bulan' => 9,  'tahun' => 2026, 'hari' => 30],
    ['bulan' => 10, 'tahun' => 2026, 'hari' => 31],
    ['bulan' => 11, 'tahun' => 2026, 'hari' => 4], // November sampai tanggal 4
];

$karyawanId = 1; // Default Karyawan
$penjualanBatch = [];
$transaksiBatch = [];
$detailBatch = [];

$penjualanCounter = 1;
$transaksiCounter = 1;

// PRNG deterministik
$seed = 202611;
$nextRand = function($min, $max) use (&$seed) {
    $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
    return $min + ($seed % ($max - $min + 1));
};

foreach ($bulanList as $bl) {
    $bulan = $bl['bulan'];
    $tahun = $bl['tahun'];
    $maxHari = $bl['hari'];

    foreach ($outletsData as $o) {
        $outletId = $o['outlet_id'];
        $wId = $o['wilayah_id'];
        $cfg = $configPerWilayah[$wId];

        // Target omzet bulan ini
        $targetOmzet = $nextRand($cfg['omzet_min'], $cfg['omzet_max']);
        if ($bulan === 11) {
            // Proporsional 4 hari dari 30 hari
            $targetOmzet = (int) round($targetOmzet * (4 / 30));
        }

        $jumlahTransaksi = $nextRand($cfg['frek_min'], $cfg['frek_max']);
        if ($bulan === 11) {
            $jumlahTransaksi = max(2, (int) round($jumlahTransaksi * (4 / 30)));
        }

        $perkiraan = (int) round($targetOmzet / $jumlahTransaksi);
        $sisaOmzet = $targetOmzet;

        // Daftar tanggal transaksi acak
        $tanggalHari = [];
        for ($i = 0; $i < $jumlahTransaksi; $i++) {
            $tanggalHari[] = $nextRand(1, $maxHari);
        }
        sort($tanggalHari);

        for ($t = 0; $t < $jumlahTransaksi; $t++) {
            $isLast = ($t === $jumlahTransaksi - 1);
            if ($isLast) {
                $omzetTransaksi = max(15000, $sisaOmzet);
            } else {
                $variasi = (int) round($perkiraan * 0.15);
                $omzetTransaksi = $nextRand(max(10000, $perkiraan - $variasi), $perkiraan + $variasi);
                $omzetTransaksi = min($omzetTransaksi, $sisaOmzet - ($jumlahTransaksi - $t - 1) * 10000);
                $omzetTransaksi = max(10000, $omzetTransaksi);
            }
            $sisaOmzet -= $omzetTransaksi;

            $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggalHari[$t]);
            $currentPenjualanId = $penjualanCounter++;
            $currentTransaksiId = $transaksiCounter++;

            // 1. Data Penjualan
            $penjualanBatch[] = [
                'penjualan_id'       => $currentPenjualanId,
                'outlet_id'          => $outletId,
                'karyawan_id'        => $karyawanId,
                'tanggal_penjualan'  => $tgl,
                'total_harga'        => $omzetTransaksi,
                'status_penjualan'   => 'selesai',
                'created_at'         => $tgl . ' 10:00:00',
                'updated_at'         => $tgl . ' 10:00:00',
            ];

            // 2. Data Transaksi
            $transaksiBatch[] = [
                'transaksi_id'      => $currentTransaksiId,
                'wilayah_id'        => $wId,
                'outlet_id'         => $outletId,
                'karyawan_id'       => $karyawanId,
                'nomor_transaksi'   => 'TR-' . $currentPenjualanId,
                'tanggal'           => $tgl,
                'total_transaksi'   => $omzetTransaksi,
                'tipe_transaksi'    => 'penjualan',
                'created_at'        => $tgl . ' 10:00:00',
                'updated_at'        => $tgl . ' 10:00:00',
            ];

            // 3. Data Transaksi Detail
            // Pilih 1 produk dari daftar wilayah
            $produkPilihan = $hargaLookup[$wId][$nextRand(0, count($hargaLookup[$wId]) - 1)];
            $hargaSatuan = (float) $produkPilihan->harga_produk;
            if ($hargaSatuan <= 0) $hargaSatuan = 50000;

            // Jumlah produk per transaksi
            $qty = max(1, (int) round($omzetTransaksi / $hargaSatuan));

            $detailBatch[] = [
                'transaksi_id'    => $currentTransaksiId,
                'produk_id'       => $produkPilihan->produk_id,
                'harga_produk_id' => $produkPilihan->harga_produk_id,
                'jumlah'          => $qty,
                'harga_satuan'    => $hargaSatuan,
                'subtotal'        => $omzetTransaksi,
                'created_at'      => $tgl . ' 10:00:00',
                'updated_at'      => $tgl . ' 10:00:00',
            ];

            // Insert bertahap setiap 500 record agar hemat memori & cepat
            if (count($penjualanBatch) >= 500) {
                DB::table('penjualan')->insert($penjualanBatch);
                DB::table('transaksi')->insert($transaksiBatch);
                DB::table('transaksi_detail')->insert($detailBatch);
                $penjualanBatch = [];
                $transaksiBatch = [];
                $detailBatch = [];
            }
        }
    }
}

// Sisa batch
if (!empty($penjualanBatch)) {
    DB::table('penjualan')->insert($penjualanBatch);
    DB::table('transaksi')->insert($transaksiBatch);
    DB::table('transaksi_detail')->insert($detailBatch);
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "=== SELESAI RESTORASI DAN SEEDING DATA PENJUALAN ===\n";
echo "Total Outlet di DB: " . DB::table('outlet')->count() . " outlet\n";
echo "Total Penjualan: " . DB::table('penjualan')->count() . " records\n";
echo "Total Transaksi: " . DB::table('transaksi')->count() . " records\n";
echo "Total Detail: " . DB::table('transaksi_detail')->count() . " records\n";
