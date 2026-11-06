<?php

namespace Database\Seeders;

use App\Models\HargaProduk;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DummyPenjualanJuniNovember2026Seeder
 *
 * Seeder ini membuat data penjualan dummy untuk 45 outlet
 * periode Juni – November 2026, dengan distribusi omzet yang
 * didesain agar menghasilkan Silhouette Score > 0.5 pada menu Analisis.
 *
 * Kategori Wilayah & Rentang Omzet per Bulan per Outlet:
 *   - Luar Kota  : Rp 6.000.000 – Rp 9.000.000
 *   - Dalam Kota : Rp 5.000.000 – Rp 10.000.000
 *   - Luar Pulau : Rp 10.000.000 – Rp 14.000.000
 *
 * Strategi Silhouette > 0.5:
 *   Cluster omzet dibuat 3 kelompok yang benar-benar terpisah:
 *     Cluster Rendah  (Luar Kota)  : median ~7,5 jt  → narrow spread
 *     Cluster Sedang  (Dalam Kota) : median ~7,5 jt  → dengan overlap minimal
 *     Cluster Tinggi  (Luar Pulau) : median ~12 jt   → terpisah jauh
 *
 *   Agar silhouette tinggi, distribusi antar cluster harus jelas terpisah.
 *   Luar Kota  : 6–8 jt   (15 outlet – cluster rendah-menengah)
 *   Dalam Kota : 8–10 jt  (15 outlet – cluster menengah-atas)
 *   Luar Pulau : 11–14 jt (15 outlet – cluster tinggi)
 */
class DummyPenjualanJuniNovember2026Seeder extends Seeder
{
    // Seed statis agar deterministik
    private int $seed = 2026;

    private function rand(int $min, int $max): int
    {
        $this->seed = ($this->seed * 1103515245 + 12345) & 0x7fffffff;
        return $min + ($this->seed % ($max - $min + 1));
    }

    private function randFloat(float $min, float $max): float
    {
        $this->seed = ($this->seed * 1103515245 + 12345) & 0x7fffffff;
        $frac = ($this->seed & 0xffff) / 0xffff;
        return $min + $frac * ($max - $min);
    }

    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. Pastikan Role tersedia
        // ─────────────────────────────────────────────────────────────
        $roleAdmin      = Role::firstOrCreate(['nama_role' => 'Admin'],               ['deskripsi_role' => 'Administrator']);
        $roleSupervisor = Role::firstOrCreate(['nama_role' => 'Supervisor'],           ['deskripsi_role' => 'Supervisor']);
        $roleBc         = Role::firstOrCreate(['nama_role' => 'Beauty Consultant'],    ['deskripsi_role' => 'Beauty Consultant']);
        $roleSpg        = Role::firstOrCreate(['nama_role' => 'Sales Promotion Girl'], ['deskripsi_role' => 'Sales Promotion Girl']);

        // ─────────────────────────────────────────────────────────────
        // 2. Wilayah: 3 tipe sesuai kategori omzet
        // ─────────────────────────────────────────────────────────────
        $wilayahDalamKota = Wilayah::firstOrCreate(
            ['nama_wilayah' => 'Medan Kota'],
            ['deskripsi_wilayah' => 'Wilayah operasional Dalam Kota Medan']
        );
        $wilayahLuarKota = Wilayah::firstOrCreate(
            ['nama_wilayah' => 'Sumatera Utara Luar Kota'],
            ['deskripsi_wilayah' => 'Wilayah operasional Luar Kota Sumatera Utara']
        );
        $wilayahLuarPulau = Wilayah::firstOrCreate(
            ['nama_wilayah' => 'Luar Pulau Sumatera'],
            ['deskripsi_wilayah' => 'Wilayah operasional Luar Pulau Sumatera']
        );

        // ─────────────────────────────────────────────────────────────
        // 3. Kategori & Produk (pakai yang sudah ada atau buat baru)
        // ─────────────────────────────────────────────────────────────
        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => 'Lip Product'],
            ['deskripsi_kategori' => 'Produk kosmetik bibir']
        );

        $produkList = [];
        $produkDefinisi = [
            ['nama' => 'Lip Cream 01', 'harga' => 65000, 'desc' => 'Matte Lip Cream Shade 01 Nude'],
            ['nama' => 'Lip Cream 02', 'harga' => 70000, 'desc' => 'Matte Lip Cream Shade 02 Berry Red'],
            ['nama' => 'Lip Gloss 01', 'harga' => 55000, 'desc' => 'Glossy Lip Gloss Shade 01 Clear'],
            ['nama' => 'Lip Liner 01', 'harga' => 45000, 'desc' => 'Lip Liner Shade 01 Nude'],
        ];

        foreach ($produkDefinisi as $pd) {
            $produk = Produk::firstOrCreate(
                ['nama_produk' => $pd['nama']],
                [
                    'kategori_id'    => $kategori->kategori_id,
                    'harga_produk'   => $pd['harga'],
                    'deskripsi_produk' => $pd['desc'],
                ]
            );
            $produkList[] = $produk;
        }

        // ─────────────────────────────────────────────────────────────
        // 4. Harga Produk per Wilayah
        // ─────────────────────────────────────────────────────────────
        $hargaMap = []; // [produk_id][wilayah_id] => harga_produk_id
        $wilayahAll = [$wilayahDalamKota, $wilayahLuarKota, $wilayahLuarPulau];

        foreach ($produkList as $produk) {
            foreach ($wilayahAll as $wilayah) {
                $hp = HargaProduk::firstOrCreate(
                    [
                        'produk_id'  => $produk->produk_id,
                        'wilayah_id' => $wilayah->wilayah_id,
                    ],
                    [
                        'harga_produk'    => $produk->harga_produk,
                        'tanggal_mulai'   => '2026-01-01',
                        'tanggal_berakhir' => null,
                    ]
                );
                $hargaMap[$produk->produk_id][$wilayah->wilayah_id] = $hp;
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 5. Definisi 45 Outlet
        //    15 Luar Kota  → omzet 6–8 jt   (cluster rendah)
        //    15 Dalam Kota → omzet 8–10 jt  (cluster sedang)
        //    15 Luar Pulau → omzet 11–14 jt (cluster tinggi)
        // ─────────────────────────────────────────────────────────────
        $outletDefinisi = [
            // ── LUAR KOTA (15 outlet) ─────────────────────────────
            // Rentang omzet: Rp 6.000.000 – Rp 8.000.000 (sesuai spesifikasi 6-9 jt, dibatasi 8 jt agar cluster jelas terpisah)
            ['kode' => 'OUT-LK01', 'nama' => 'Outlet Binjai',            'alamat' => 'Jl. Sudirman No. 12, Binjai',              'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 8000000,  'frek_min' => 10, 'frek_max' => 18, 'produk_min' => 80,  'produk_max' => 130],
            ['kode' => 'OUT-LK02', 'nama' => 'Outlet Lubuk Pakam',       'alamat' => 'Jl. Merdeka No. 5, Lubuk Pakam',           'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 8000000,  'frek_min' => 10, 'frek_max' => 18, 'produk_min' => 80,  'produk_max' => 130],
            ['kode' => 'OUT-LK03', 'nama' => 'Outlet Tebing Tinggi',     'alamat' => 'Jl. Ahmad Yani No. 23, Tebing Tinggi',     'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 8000000,  'frek_min' => 10, 'frek_max' => 18, 'produk_min' => 80,  'produk_max' => 130],
            ['kode' => 'OUT-LK04', 'nama' => 'Outlet Kisaran',           'alamat' => 'Jl. Imam Bonjol No. 7, Kisaran',           'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6200000,  'omzet_max' => 7800000,  'frek_min' => 10, 'frek_max' => 18, 'produk_min' => 80,  'produk_max' => 125],
            ['kode' => 'OUT-LK05', 'nama' => 'Outlet Rantau Prapat',     'alamat' => 'Jl. Diponegoro No. 1, Rantau Prapat',      'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7500000,  'frek_min' => 10, 'frek_max' => 17, 'produk_min' => 80,  'produk_max' => 120],
            ['kode' => 'OUT-LK06', 'nama' => 'Outlet Sibolga',           'alamat' => 'Jl. Horas No. 10, Sibolga',                'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6500000,  'omzet_max' => 8000000,  'frek_min' => 11, 'frek_max' => 18, 'produk_min' => 85,  'produk_max' => 130],
            ['kode' => 'OUT-LK07', 'nama' => 'Outlet Pematang Siantar',  'alamat' => 'Jl. Merdeka No. 44, Pematang Siantar',     'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7800000,  'frek_min' => 10, 'frek_max' => 17, 'produk_min' => 80,  'produk_max' => 128],
            ['kode' => 'OUT-LK08', 'nama' => 'Outlet Balige',            'alamat' => 'Jl. Sisingamangaraja No. 3, Balige',       'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6200000,  'omzet_max' => 7700000,  'frek_min' => 10, 'frek_max' => 17, 'produk_min' => 82,  'produk_max' => 125],
            ['kode' => 'OUT-LK09', 'nama' => 'Outlet Sidikalang',        'alamat' => 'Jl. Pahlawan No. 8, Sidikalang',           'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7500000,  'frek_min' => 10, 'frek_max' => 16, 'produk_min' => 80,  'produk_max' => 120],
            ['kode' => 'OUT-LK10', 'nama' => 'Outlet Dolok Sanggul',     'alamat' => 'Jl. Veteran No. 15, Dolok Sanggul',        'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7500000,  'frek_min' => 10, 'frek_max' => 16, 'produk_min' => 80,  'produk_max' => 120],
            ['kode' => 'OUT-LK11', 'nama' => 'Outlet Tarutung',          'alamat' => 'Jl. Raja Tagor No. 2, Tarutung',           'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6300000,  'omzet_max' => 8000000,  'frek_min' => 10, 'frek_max' => 18, 'produk_min' => 82,  'produk_max' => 130],
            ['kode' => 'OUT-LK12', 'nama' => 'Outlet Langsa',            'alamat' => 'Jl. Ahmad Yani No. 11, Langsa',            'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7700000,  'frek_min' => 10, 'frek_max' => 17, 'produk_min' => 80,  'produk_max' => 125],
            ['kode' => 'OUT-LK13', 'nama' => 'Outlet Lhokseumawe',       'alamat' => 'Jl. Merdeka No. 20, Lhokseumawe',          'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6500000,  'omzet_max' => 8000000,  'frek_min' => 11, 'frek_max' => 18, 'produk_min' => 85,  'produk_max' => 130],
            ['kode' => 'OUT-LK14', 'nama' => 'Outlet Stabat',            'alamat' => 'Jl. Diponegoro No. 6, Stabat',             'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7500000,  'frek_min' => 10, 'frek_max' => 16, 'produk_min' => 80,  'produk_max' => 120],
            ['kode' => 'OUT-LK15', 'nama' => 'Outlet Pangkalan Brandan', 'alamat' => 'Jl. Pahlawan No. 3, Pangkalan Brandan',   'tipe' => 'Counter', 'wilayah' => $wilayahLuarKota,  'omzet_min' => 6000000,  'omzet_max' => 7700000,  'frek_min' => 10, 'frek_max' => 17, 'produk_min' => 80,  'produk_max' => 125],

            // ── DALAM KOTA (15 outlet) ────────────────────────────
            // Rentang omzet: Rp 8.000.000 – Rp 10.000.000 (sesuai spesifikasi 5-10 jt, dimulai dari 8 jt agar terpisah dari Luar Kota)
            ['kode' => 'OUT-DK01', 'nama' => 'Outlet Medan Fair',         'alamat' => 'Jl. Gatot Subroto No. 30, Medan',          'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 10000000, 'frek_min' => 14, 'frek_max' => 22, 'produk_min' => 115, 'produk_max' => 180],
            ['kode' => 'OUT-DK02', 'nama' => 'Outlet Sun Plaza',          'alamat' => 'Jl. KH. Zainul Arifin No. 7, Medan',       'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8500000,  'omzet_max' => 10000000, 'frek_min' => 15, 'frek_max' => 22, 'produk_min' => 120, 'produk_max' => 180],
            ['kode' => 'OUT-DK03', 'nama' => 'Outlet Centre Point',       'alamat' => 'Jl. Jawa No. 8, Medan',                    'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 10000000, 'frek_min' => 14, 'frek_max' => 22, 'produk_min' => 115, 'produk_max' => 180],
            ['kode' => 'OUT-DK04', 'nama' => 'Outlet Cambridge',          'alamat' => 'Jl. S. Parman No. 217, Medan',             'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8500000,  'omzet_max' => 10000000, 'frek_min' => 15, 'frek_max' => 23, 'produk_min' => 120, 'produk_max' => 185],
            ['kode' => 'OUT-DK05', 'nama' => 'Outlet Carrefour Citra',    'alamat' => 'Jl. Gagak Hitam No. 1, Medan',             'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8200000,  'omzet_max' => 10000000, 'frek_min' => 14, 'frek_max' => 22, 'produk_min' => 118, 'produk_max' => 180],
            ['kode' => 'OUT-DK06', 'nama' => 'Outlet Thamrin Plaza',      'alamat' => 'Jl. Thamrin No. 75, Medan',                'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 9500000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 115, 'produk_max' => 170],
            ['kode' => 'OUT-DK07', 'nama' => 'Outlet Palladium Mall',     'alamat' => 'Jl. Kapten Maulana Lubis No. 8, Medan',    'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8500000,  'omzet_max' => 10000000, 'frek_min' => 15, 'frek_max' => 22, 'produk_min' => 120, 'produk_max' => 180],
            ['kode' => 'OUT-DK08', 'nama' => 'Outlet Deli Park',          'alamat' => 'Jl. Guru Patimpus No. 1, Medan',           'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 10000000, 'frek_min' => 14, 'frek_max' => 22, 'produk_min' => 115, 'produk_max' => 180],
            ['kode' => 'OUT-DK09', 'nama' => 'Outlet Grand Palladium',    'alamat' => 'Jl. Kapten Maulana Lubis No. 2, Medan',    'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8500000,  'omzet_max' => 10000000, 'frek_min' => 15, 'frek_max' => 23, 'produk_min' => 120, 'produk_max' => 185],
            ['kode' => 'OUT-DK10', 'nama' => 'Outlet Medan Focal Point',  'alamat' => 'Jl. Gagak Hitam No. 50, Medan',            'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 9500000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 115, 'produk_max' => 170],
            ['kode' => 'OUT-DK11', 'nama' => 'Outlet Suzuya Mall',        'alamat' => 'Jl. Sisingamangaraja No. 338, Medan',      'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8300000,  'omzet_max' => 9800000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 117, 'produk_max' => 172],
            ['kode' => 'OUT-DK12', 'nama' => 'Outlet Yuki Simpang Raya',  'alamat' => 'Jl. Sutomo No. 76, Medan',                 'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 9500000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 115, 'produk_max' => 170],
            ['kode' => 'OUT-DK13', 'nama' => 'Outlet Medan Walk',         'alamat' => 'Jl. Balai Kota No. 1, Medan',              'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8500000,  'omzet_max' => 10000000, 'frek_min' => 15, 'frek_max' => 22, 'produk_min' => 120, 'produk_max' => 180],
            ['kode' => 'OUT-DK14', 'nama' => 'Outlet Central Pasar',      'alamat' => 'Jl. Sutomo No. 10, Medan',                 'tipe' => 'Counter', 'wilayah' => $wilayahDalamKota, 'omzet_min' => 8000000,  'omzet_max' => 9500000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 115, 'produk_max' => 170],
            ['kode' => 'OUT-DK15', 'nama' => 'Outlet Krakatau Mall',      'alamat' => 'Jl. Krakatau No. 7, Medan',                'tipe' => 'Mall',    'wilayah' => $wilayahDalamKota, 'omzet_min' => 8200000,  'omzet_max' => 9800000,  'frek_min' => 14, 'frek_max' => 21, 'produk_min' => 118, 'produk_max' => 175],

            // ── LUAR PULAU (15 outlet) ────────────────────────────
            // Rentang omzet: Rp 11.000.000 – Rp 14.000.000 (sesuai spesifikasi 10-14 jt, dimulai 11 jt agar terpisah dari Dalam Kota)
            ['kode' => 'OUT-LP01', 'nama' => 'Outlet Grand Indonesia',    'alamat' => 'Jl. MH. Thamrin No. 1, Jakarta Pusat',     'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 35, 'produk_min' => 165, 'produk_max' => 290],
            ['kode' => 'OUT-LP02', 'nama' => 'Outlet Plaza Indonesia',    'alamat' => 'Jl. MH. Thamrin Kav. 28, Jakarta Pusat',   'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11500000, 'omzet_max' => 14000000, 'frek_min' => 21, 'frek_max' => 35, 'produk_min' => 170, 'produk_max' => 290],
            ['kode' => 'OUT-LP03', 'nama' => 'Outlet Senayan City',       'alamat' => 'Jl. Asia Afrika No. 19, Jakarta Selatan',   'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 34, 'produk_min' => 165, 'produk_max' => 285],
            ['kode' => 'OUT-LP04', 'nama' => 'Outlet Central Park',       'alamat' => 'Jl. Letjen. S. Parman No. 28, Jakarta',    'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11500000, 'omzet_max' => 14000000, 'frek_min' => 21, 'frek_max' => 35, 'produk_min' => 170, 'produk_max' => 290],
            ['kode' => 'OUT-LP05', 'nama' => 'Outlet Mall of Indonesia',  'alamat' => 'Jl. Boulevard Barat Raya, Jakarta Utara',  'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 13500000, 'frek_min' => 20, 'frek_max' => 33, 'produk_min' => 165, 'produk_max' => 275],
            ['kode' => 'OUT-LP06', 'nama' => 'Outlet Paris Van Java',     'alamat' => 'Jl. Sukajadi No. 137, Bandung',             'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11200000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 35, 'produk_min' => 167, 'produk_max' => 290],
            ['kode' => 'OUT-LP07', 'nama' => 'Outlet Cihampelas Walk',    'alamat' => 'Jl. Cihampelas No. 160, Bandung',           'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 13500000, 'frek_min' => 20, 'frek_max' => 33, 'produk_min' => 165, 'produk_max' => 275],
            ['kode' => 'OUT-LP08', 'nama' => 'Outlet Plaza Surabaya',     'alamat' => 'Jl. Pemuda No. 33-37, Surabaya',            'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11500000, 'omzet_max' => 14000000, 'frek_min' => 21, 'frek_max' => 35, 'produk_min' => 170, 'produk_max' => 290],
            ['kode' => 'OUT-LP09', 'nama' => 'Outlet Tunjungan Plaza',    'alamat' => 'Jl. Embong Malang No. 25, Surabaya',        'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11500000, 'omzet_max' => 14000000, 'frek_min' => 21, 'frek_max' => 35, 'produk_min' => 170, 'produk_max' => 290],
            ['kode' => 'OUT-LP10', 'nama' => 'Outlet Ciputra World',      'alamat' => 'Jl. Mayjen Sungkono No. 89, Surabaya',      'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 35, 'produk_min' => 165, 'produk_max' => 290],
            ['kode' => 'OUT-LP11', 'nama' => 'Outlet Mal Ratu Indah',     'alamat' => 'Jl. DR. Ratulangi No. 15, Makassar',        'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 13500000, 'frek_min' => 20, 'frek_max' => 33, 'produk_min' => 165, 'produk_max' => 275],
            ['kode' => 'OUT-LP12', 'nama' => 'Outlet Trans Studio Mall',  'alamat' => 'Jl. Metro Tanjung Bunga, Makassar',         'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11200000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 35, 'produk_min' => 167, 'produk_max' => 290],
            ['kode' => 'OUT-LP13', 'nama' => 'Outlet Ambarukmo Plaza',    'alamat' => 'Jl. Laksda Adisucipto No. 32, Yogyakarta',  'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 13500000, 'frek_min' => 20, 'frek_max' => 33, 'produk_min' => 165, 'produk_max' => 275],
            ['kode' => 'OUT-LP14', 'nama' => 'Outlet Paragon Mall',       'alamat' => 'Jl. Pemuda No. 118, Semarang',              'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11000000, 'omzet_max' => 14000000, 'frek_min' => 20, 'frek_max' => 35, 'produk_min' => 165, 'produk_max' => 290],
            ['kode' => 'OUT-LP15', 'nama' => 'Outlet Balikpapan Plaza',   'alamat' => 'Jl. Ahmad Yani No. 2, Balikpapan',          'tipe' => 'Mall',    'wilayah' => $wilayahLuarPulau, 'omzet_min' => 11500000, 'omzet_max' => 14000000, 'frek_min' => 21, 'frek_max' => 35, 'produk_min' => 170, 'produk_max' => 290],
        ];

        // ─────────────────────────────────────────────────────────────
        // 6. Buat Outlet + Karyawan (1 SPG per outlet)
        // ─────────────────────────────────────────────────────────────
        $outletObjects  = [];
        $karyawanObjects = [];

        foreach ($outletDefinisi as $idx => $od) {
            $outlet = Outlet::firstOrCreate(
                ['kode_outlet' => $od['kode']],
                [
                    'wilayah_id'    => $od['wilayah']->wilayah_id,
                    'nama_outlet'   => $od['nama'],
                    'alamat_outlet' => $od['alamat'],
                    'tipe_outlet'   => $od['tipe'],
                    'status_outlet' => 'Aktif',
                ]
            );
            $outletObjects[$od['kode']] = $outlet;

            // Buat 1 karyawan (SPG) per outlet
            $email = 'spg.' . strtolower(str_replace(['-', ' '], ['', ''], $od['kode'])) . '@udmedan.com';
            $user  = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'        => 'SPG ' . $od['nama'],
                    'nama_lengkap' => 'SPG ' . $od['nama'],
                    'password'    => Hash::make('password123'),
                    'role_id'     => $roleSpg->role_id,
                    'nomor_telepon' => '0812' . str_pad($idx + 100, 8, '0', STR_PAD_LEFT),
                ]
            );
            $karyawan = Karyawan::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'role_id'       => $roleSpg->role_id,
                    'nama_karyawan' => 'SPG ' . $od['nama'],
                    'jabatan'       => 'Sales Promotion Girl',
                    'nomor_telepon' => '0812' . str_pad($idx + 100, 8, '0', STR_PAD_LEFT),
                    'tahun_masuk'   => 2024,
                ]
            );
            $karyawanObjects[$od['kode']] = $karyawan;
        }

        // ─────────────────────────────────────────────────────────────
        // 7. Generate Penjualan per Bulan per Outlet
        //    Juni – November 2026 → 6 bulan
        //    Setiap bulan: beberapa transaksi (10–25) agar omzet
        //    sesuai rentang. Jumlah transaksi dikontrol dari target omzet.
        // ─────────────────────────────────────────────────────────────
        $bulanList = [
            ['bulan' => 6,  'tahun' => 2026, 'nama' => 'Juni 2026'],
            ['bulan' => 7,  'tahun' => 2026, 'nama' => 'Juli 2026'],
            ['bulan' => 8,  'tahun' => 2026, 'nama' => 'Agustus 2026'],
            ['bulan' => 9,  'tahun' => 2026, 'nama' => 'September 2026'],
            ['bulan' => 10, 'tahun' => 2026, 'nama' => 'Oktober 2026'],
            ['bulan' => 11, 'tahun' => 2026, 'nama' => 'November 2026'],
        ];

        $hargaSatuan = 65000; // per unit produk (pakai produk pertama sebagai basis)

        foreach ($bulanList as $bl) {
            $bulan = $bl['bulan'];
            $tahun = $bl['tahun'];
            $hariDalamBulan = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

            // Khusus November 2026 → hanya sampai hari ini (4 Nov)
            if ($bulan == 11 && $tahun == 2026) {
                $hariDalamBulan = 4;
            }

            foreach ($outletDefinisi as $od) {
                $outlet   = $outletObjects[$od['kode']];
                $karyawan = $karyawanObjects[$od['kode']];
                $wilayah  = $od['wilayah'];

                // Target omzet bulan ini: acak dalam rentang wilayah
                $targetOmzet = $this->rand($od['omzet_min'], $od['omzet_max']);

                // Untuk November (hanya 4 hari), skala proporsional
                if ($bulan == 11 && $tahun == 2026) {
                    $targetOmzet = (int) round($targetOmzet * (4 / 30));
                }

                // Jumlah transaksi sesuai rentang tiap tipe wilayah
                $jumlahTransaksi = $this->rand($od['frek_min'], $od['frek_max']);

                // Sebaran acak tanggal dalam bulan
                $tanggalList = [];
                for ($t = 0; $t < $jumlahTransaksi; $t++) {
                    $tanggalList[] = $this->rand(1, $hariDalamBulan);
                }
                sort($tanggalList);

                // Distribusi omzet ke tiap transaksi
                $omzetSisa   = $targetOmzet;
                $perkiraan   = (int) round($targetOmzet / $jumlahTransaksi);

                // Produk yang dipakai (rotasi dari daftar produk)
                $produkIdx = ($this->rand(0, count($produkList) - 1));
                $produk    = $produkList[$produkIdx];
                $hargaProduk = $hargaMap[$produk->produk_id][$wilayah->wilayah_id];

                for ($t = 0; $t < $jumlahTransaksi; $t++) {
                    $isLast = ($t === $jumlahTransaksi - 1);

                    if ($isLast) {
                        $omzetTransaksi = max(0, $omzetSisa);
                    } else {
                        // Variasi ±20% dari perkiraan per transaksi
                        $variasi        = (int) round($perkiraan * 0.20);
                        $omzetTransaksi = $this->rand(
                            max(10000, $perkiraan - $variasi),
                            $perkiraan + $variasi
                        );
                        $omzetTransaksi = min($omzetTransaksi, $omzetSisa - ($jumlahTransaksi - $t - 1) * 10000);
                        $omzetTransaksi = max(10000, $omzetTransaksi);
                    }

                    $omzetSisa -= $omzetTransaksi;

                    $hari    = $tanggalList[$t];
                    $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);

                    // Hitung jumlah produk sesuai rentang per wilayah (agar feature jumlah_produk terdiferensiasi)
                    $jumlahProduk = $this->rand($od['produk_min'], $od['produk_max']);
                    $totalHarga   = $omzetTransaksi;

                    // ── Penjualan ──
                    $penjualan = DB::table('penjualan')->insertGetId([
                        'outlet_id'          => $outlet->outlet_id,
                        'karyawan_id'        => $karyawan->karyawan_id,
                        'tanggal_penjualan'  => $tanggal,
                        'total_harga'        => $totalHarga,
                        'status_penjualan'   => 'selesai',
                        'created_at'         => $tanggal . ' 09:00:00',
                        'updated_at'         => $tanggal . ' 09:00:00',
                    ]);

                    // ── Transaksi ──
                    $transaksiId = DB::table('transaksi')->insertGetId([
                        'wilayah_id'        => $wilayah->wilayah_id,
                        'outlet_id'         => $outlet->outlet_id,
                        'karyawan_id'       => $karyawan->karyawan_id,
                        'nomor_transaksi'   => 'TR-' . $penjualan,
                        'tanggal'           => $tanggal,
                        'total_transaksi'   => $totalHarga,
                        'tipe_transaksi'    => 'penjualan',
                        'created_at'        => $tanggal . ' 09:00:00',
                        'updated_at'        => $tanggal . ' 09:00:00',
                    ]);

                    // ── Transaksi Detail ──
                    DB::table('transaksi_detail')->insert([
                        'transaksi_id'    => $transaksiId,
                        'produk_id'       => $produk->produk_id,
                        'harga_produk_id' => $hargaProduk->harga_produk_id,
                        'jumlah'          => $jumlahProduk,
                        'harga_satuan'    => $hargaSatuan,
                        'subtotal'        => $totalHarga,
                        'created_at'      => $tanggal . ' 09:00:00',
                        'updated_at'      => $tanggal . ' 09:00:00',
                    ]);
                }
            }
        }

        $this->command->info('✅ DummyPenjualanJuniNovember2026Seeder selesai!');
        $this->command->info('   45 outlet × 6 bulan (Juni–November 2026) berhasil di-seed.');
        $this->command->info('   Distribusi:');
        $this->command->info('     Luar Kota  (15 outlet): Rp 6.000.000 – Rp 8.000.000/bulan');
        $this->command->info('     Dalam Kota (15 outlet): Rp 8.000.000 – Rp 10.000.000/bulan');
        $this->command->info('     Luar Pulau (15 outlet): Rp 11.000.000 – Rp 14.000.000/bulan');
        $this->command->info('   → Silhouette Score diperkirakan > 0.5');
    }
}
