<?php

namespace Database\Seeders;

use App\Models\Bc;
use App\Models\HargaProduk;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\Outlet;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Spg;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleTransaksiDetailSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role tersedia
        $roleAdmin = Role::firstOrCreate(['nama_role' => 'Admin'], ['deskripsi_role' => 'Administrator']);
        $roleSupervisor = Role::firstOrCreate(['nama_role' => 'Supervisor'], ['deskripsi_role' => 'Supervisor']);
        $roleBc = Role::firstOrCreate(['nama_role' => 'Beauty Consultant'], ['deskripsi_role' => 'Beauty Consultant']);
        $roleSpg = Role::firstOrCreate(['nama_role' => 'Sales Promotion Girl'], ['deskripsi_role' => 'Sales Promotion Girl']);

        // 2. Pastikan Wilayah tersedia
        $wilayah = Wilayah::firstOrCreate(
            ['nama_wilayah' => 'Medan Kota'],
            ['deskripsi_wilayah' => 'Wilayah operasional Medan Kota']
        );

        // 3. Pastikan Outlet tersedia
        $outlet1 = Outlet::firstOrCreate(
            ['kode_outlet' => 'OUT-MDF'],
            [
                'wilayah_id' => $wilayah->wilayah_id,
                'nama_outlet' => 'Outlet Plaza Medan Fair',
                'alamat_outlet' => 'Jl. Gatot Subroto No. 30, Medan',
                'tipe_outlet' => 'Mall',
                'status_outlet' => 'Aktif',
            ]
        );

        $outlet2 = Outlet::firstOrCreate(
            ['kode_outlet' => 'OUT-SUN'],
            [
                'wilayah_id' => $wilayah->wilayah_id,
                'nama_outlet' => 'Outlet Sun Plaza',
                'alamat_outlet' => 'Jl. KH. Zainul Arifin No. 7, Medan',
                'tipe_outlet' => 'Mall',
                'status_outlet' => 'Aktif',
            ]
        );

        $outlet3 = Outlet::firstOrCreate(
            ['kode_outlet' => 'OUT-MCP'],
            [
                'wilayah_id' => $wilayah->wilayah_id,
                'nama_outlet' => 'Outlet Centre Point',
                'alamat_outlet' => 'Jl. Jawa No. 8, Medan',
                'tipe_outlet' => 'Mall',
                'status_outlet' => 'Aktif',
            ]
        );

        // 4. Pastikan Kategori & Produk tersedia
        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => 'Lip Product'],
            ['deskripsi_kategori' => 'Produk kosmetik bibir']
        );

        $produk1 = Produk::firstOrCreate(
            ['nama_produk' => 'Lip Cream 01'],
            [
                'kategori_id' => $kategori->kategori_id,
                'deskripsi_produk' => 'Matte Lip Cream Shade 01 Nude',
                'harga_produk' => 65000,
            ]
        );

        $produk2 = Produk::firstOrCreate(
            ['nama_produk' => 'Lip Cream 02'],
            [
                'kategori_id' => $kategori->kategori_id,
                'deskripsi_produk' => 'Matte Lip Cream Shade 02 Berry Red',
                'harga_produk' => 65000,
            ]
        );

        // 5. Pastikan Harga Produk di wilayah tersedia
        $harga1 = HargaProduk::firstOrCreate(
            [
                'produk_id' => $produk1->produk_id,
                'wilayah_id' => $wilayah->wilayah_id,
            ],
            [
                'harga_produk' => 65000,
                'tanggal_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                'tanggal_berakhir' => null,
            ]
        );

        $harga2 = HargaProduk::firstOrCreate(
            [
                'produk_id' => $produk2->produk_id,
                'wilayah_id' => $wilayah->wilayah_id,
            ],
            [
                'harga_produk' => 65000,
                'tanggal_mulai' => Carbon::now()->subMonths(3)->toDateString(),
                'tanggal_berakhir' => null,
            ]
        );

        // 6. Buat Akun & Karyawan untuk SPG, BC, dan Supervisor
        $akunList = [
            [
                'role' => $roleSpg,
                'email' => 'spg@udmedan.com',
                'name' => 'Anisa Putri (SPG)',
                'jabatan' => 'Sales Promotion Girl',
                'outlet' => $outlet1,
                'produk' => $produk1,
                'harga' => $harga1,
                'jumlah' => 4,
            ],
            [
                'role' => $roleBc,
                'email' => 'bc@udmedan.com',
                'name' => 'Dewi Lestari (BC)',
                'jabatan' => 'Beauty Consultant',
                'outlet' => $outlet2,
                'produk' => $produk2,
                'harga' => $harga2,
                'jumlah' => 6,
            ],
            [
                'role' => $roleSupervisor,
                'email' => 'supervisor@udmedan.com',
                'name' => 'Siti Rahma (Supervisor)',
                'jabatan' => 'Supervisor',
                'outlet' => $outlet3,
                'produk' => $produk1,
                'harga' => $harga1,
                'jumlah' => 10,
            ],
        ];

        foreach ($akunList as $index => $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'nama_lengkap' => $item['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $item['role']->role_id,
                    'nomor_telepon' => '0812345678' . $index,
                ]
            );

            $karyawan = Karyawan::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'role_id' => $item['role']->role_id,
                    'nama_karyawan' => $item['name'],
                    'jabatan' => $item['jabatan'],
                    'nomor_telepon' => '0812345678' . $index,
                    'tahun_masuk' => 2024,
                ]
            );

            // Simpan juga ke tabel spg atau bc sesuai rolenya
            if ($item['role']->nama_role === 'Sales Promotion Girl') {
                Spg::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'nama_spg' => $item['name'],
                        'nomor_telepon' => '0812345678' . $index,
                        'tanggal_masuk' => '2024-01-01',
                    ]
                );
            } elseif ($item['role']->nama_role === 'Beauty Consultant') {
                Bc::firstOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'nama_bc' => $item['name'],
                        'nomor_telepon' => '0812345678' . $index,
                        'tanggal_masuk' => '2024-01-01',
                    ]
                );
            }

            // Simulasikan transaksi penjualan yang diinput oleh akun ini
            $totalHarga = (float) $item['harga']->harga_produk * $item['jumlah'];
            $tanggal = Carbon::now()->subDays($index)->toDateString();

            // 1. Buat penjualan
            $penjualan = Penjualan::create([
                'outlet_id' => $item['outlet']->outlet_id,
                'karyawan_id' => $karyawan->karyawan_id,
                'tanggal_penjualan' => $tanggal,
                'total_harga' => $totalHarga,
                'status_penjualan' => 'selesai',
            ]);

            // 2. Buat transaksi
            $transaksi = Transaksi::create([
                'wilayah_id' => $wilayah->wilayah_id,
                'outlet_id' => $item['outlet']->outlet_id,
                'karyawan_id' => $karyawan->karyawan_id,
                'nomor_transaksi' => 'TR-' . $penjualan->penjualan_id,
                'tanggal' => $tanggal,
                'total_transaksi' => $totalHarga,
                'tipe_transaksi' => 'penjualan',
            ]);

            // 3. Buat transaksi_detail
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->transaksi_id,
                'produk_id' => $item['produk']->produk_id,
                'harga_produk_id' => $item['harga']->harga_produk_id,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => (float) $item['harga']->harga_produk,
                'subtotal' => $totalHarga,
            ]);
        }
    }
}
