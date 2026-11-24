<?php

namespace App\Http\Controllers;

use App\Models\AnalisisPerforma;
use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Query dasar transaksi penjualan khusus milik BC, SPG, dan Supervisor.
     * Tidak menyertakan transaksi murni Admin, dan menerapkan scope wilayah/outlet sesuai role login.
     */
    private function basePenjualanQuery()
    {
        $allowedRoles = [
            'Beauty Consultant',
            'Sales Promotion Girl',
            'Supervisor',
            'BC',
            'SPG',
        ];
        $allowedRoleIds = [2, 3, 4]; // 2: Supervisor, 3: Beauty Consultant, 4: Sales Promotion Girl

        $query = DB::table('penjualan')
            ->join('karyawan', 'karyawan.karyawan_id', '=', 'penjualan.karyawan_id')
            ->join('role', 'role.role_id', '=', 'karyawan.role_id')
            ->where(function ($q) use ($allowedRoles, $allowedRoleIds) {
                $q->whereIn('role.nama_role', $allowedRoles)
                    ->orWhereIn('karyawan.jabatan', $allowedRoles)
                    ->orWhereIn('role.role_id', $allowedRoleIds);
            });

        // Filter scope berdasarkan role user login (jika bukan Admin)
        $user = Auth::user();
        if ($user) {
            $namaRole = strtolower($user->role?->nama_role ?? '');
            $karyawan = $user->karyawan;

            // 1. SPG: Hanya mengakses data transaksi dalam outlet penugasan atau yang ditugaskan kepadanya
            if (in_array($namaRole, ['sales promotion girl', 'spg'])) {
                $outletId = $karyawan?->outlets()->value('outlet.outlet_id');
                if ($outletId) {
                    $query->where(function ($q) use ($outletId, $karyawan) {
                        $q->where('penjualan.outlet_id', $outletId)
                            ->orWhere('penjualan.spg_karyawan_id', $karyawan?->karyawan_id)
                            ->orWhere('penjualan.karyawan_id', $karyawan?->karyawan_id);
                    });

                    $spgModel = DB::table('spg')->where('user_id', $user->user_id)->first();
                    $tanggalMasuk = $spgModel?->tanggal_masuk ?? $karyawan?->created_at?->toDateString();
                    if ($tanggalMasuk) {
                        $query->where(function ($q) use ($tanggalMasuk, $karyawan) {
                            $q->whereDate('penjualan.tanggal_penjualan', '>=', $tanggalMasuk)
                                ->orWhere('penjualan.spg_karyawan_id', $karyawan?->karyawan_id)
                                ->orWhere('penjualan.karyawan_id', $karyawan?->karyawan_id);
                        });
                    }
                } elseif ($karyawan) {
                    $query->where(function ($q) use ($karyawan) {
                        $q->where('penjualan.spg_karyawan_id', $karyawan->karyawan_id)
                            ->orWhere('penjualan.karyawan_id', $karyawan->karyawan_id);
                    });
                }
            }
            // 2. BC: Hanya mengakses data transaksi di outlet tempat bertugas atau yang ditugaskan kepadanya
            elseif (in_array($namaRole, ['beauty consultant', 'bc'])) {
                if ($karyawan && $karyawan->outlets()->exists()) {
                    $outletIds = $karyawan->outlets()->pluck('outlet.outlet_id')->all();
                    $query->where(function ($q) use ($outletIds, $karyawan) {
                        $q->whereIn('penjualan.outlet_id', $outletIds)
                            ->orWhere('penjualan.bc_karyawan_id', $karyawan->karyawan_id)
                            ->orWhere('penjualan.karyawan_id', $karyawan->karyawan_id);
                    });

                    $bcModel = DB::table('bc')->where('user_id', $user->user_id)->first();
                    $tanggalMasuk = $bcModel?->tanggal_masuk ?? $karyawan?->created_at?->toDateString();
                    if ($tanggalMasuk) {
                        $query->where(function ($q) use ($tanggalMasuk, $karyawan) {
                            $q->whereDate('penjualan.tanggal_penjualan', '>=', $tanggalMasuk)
                                ->orWhere('penjualan.bc_karyawan_id', $karyawan->karyawan_id)
                                ->orWhere('penjualan.karyawan_id', $karyawan->karyawan_id);
                        });
                    }
                } elseif ($karyawan) {
                    $query->where(function ($q) use ($karyawan) {
                        $q->where('penjualan.bc_karyawan_id', $karyawan->karyawan_id)
                            ->orWhere('penjualan.karyawan_id', $karyawan->karyawan_id);
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            // 3. Supervisor: Mengakses data transaksi seluruh wilayah penjualan
            elseif ($namaRole === 'supervisor') {
                // Supervisor dapat memantau data seluruh wilayah
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $penjualanBase = $this->basePenjualanQuery();

        $selectedWilayah = $request->query('wilayah');
        $sortBy = $request->query('sort_by', 'omzet_desc');
        $selectedStatus = $request->query('status');
        $searchQuery = $request->query('q');

        $daftarWilayah = DB::table('wilayah')->orderBy('nama_wilayah')->pluck('nama_wilayah');

        // 1. Daftar semua periode yang tersedia dari data riil penjualan BC/SPG/Supervisor
        $availablePeriods = (clone $penjualanBase)
            ->selectRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m") as periode')
            ->groupByRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")')
            ->orderByDesc('periode')
            ->pluck('periode');

        // Periode terpilih: dari request parameter atau default ke periode terbaru
        $selectedPeriode = $request->query('periode');
        if (! $selectedPeriode || ! $availablePeriods->contains($selectedPeriode)) {
            $selectedPeriode = $availablePeriods->first() ?? now()->format('Y-m');
        }

        $periodeObj = Carbon::parse($selectedPeriode.'-01');
        $periodeLaluObj = (clone $periodeObj)->subMonth();
        $bulanSekarang = $periodeObj->format('m');
        $tahunSekarang = $periodeObj->format('Y');
        $bulanLalu = $periodeLaluObj->format('m');
        $tahunLalu = $periodeLaluObj->format('Y');

        // 2. Query penjualan periode berjalan & periode lalu
        $penjualanBulanIni = (clone $penjualanBase)
            ->whereMonth('penjualan.tanggal_penjualan', $bulanSekarang)
            ->whereYear('penjualan.tanggal_penjualan', $tahunSekarang);

        $penjualanBulanLalu = (clone $penjualanBase)
            ->whereMonth('penjualan.tanggal_penjualan', $bulanLalu)
            ->whereYear('penjualan.tanggal_penjualan', $tahunLalu);

        // 3. Ringkasan Statistik Perusahaan
        $totalOmzet = (float) (clone $penjualanBulanIni)->sum('penjualan.total_harga');
        $totalOmzetLalu = (float) (clone $penjualanBulanLalu)->sum('penjualan.total_harga');
        $growthOmzetPerusahaan = $totalOmzetLalu > 0 ? (($totalOmzet - $totalOmzetLalu) / $totalOmzetLalu) * 100 : 0;

        $totalTransaksi = (int) (clone $penjualanBulanIni)->count();
        $totalTransaksiLalu = (int) (clone $penjualanBulanLalu)->count();
        $growthTransaksiPerusahaan = $totalTransaksiLalu > 0 ? (($totalTransaksi - $totalTransaksiLalu) / $totalTransaksiLalu) * 100 : 0;

        $totalOutletTerdaftar = Outlet::count();
        $totalProdukTerdaftar = Produk::count();

        // 4. Omzet per outlet bulan lalu (untuk hitung growth MoM)
        $omzetBulanLaluPerOutlet = (clone $penjualanBulanLalu)
            ->selectRaw('penjualan.outlet_id, SUM(penjualan.total_harga) as omzet_lalu')
            ->groupBy('penjualan.outlet_id')
            ->pluck('omzet_lalu', 'penjualan.outlet_id');

        // 5. Total produk terjual per outlet & total produk keseluruhan
        $totalProdukTerjual = (int) (clone $penjualanBulanIni)
            ->join('transaksi', function ($join) {
                $join->whereRaw("transaksi.nomor_transaksi = CONCAT('TR-', penjualan.penjualan_id)");
            })
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->sum('transaksi_detail.jumlah');

        $jumlahProdukPerOutlet = (clone $penjualanBulanIni)
            ->join('transaksi', function ($join) {
                $join->whereRaw("transaksi.nomor_transaksi = CONCAT('TR-', penjualan.penjualan_id)");
            })
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->selectRaw('penjualan.outlet_id, SUM(transaksi_detail.jumlah) as total_produk')
            ->groupBy('penjualan.outlet_id')
            ->pluck('total_produk', 'penjualan.outlet_id');

        // 6. Produk paling laku pada setiap outlet
        $topProdukPerOutlet = (clone $penjualanBulanIni)
            ->join('transaksi', function ($join) {
                $join->whereRaw("transaksi.nomor_transaksi = CONCAT('TR-', penjualan.penjualan_id)");
            })
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->join('produk', 'produk.produk_id', '=', 'transaksi_detail.produk_id')
            ->selectRaw('penjualan.outlet_id, produk.nama_produk, SUM(transaksi_detail.jumlah) as total_terjual')
            ->groupBy('penjualan.outlet_id', 'produk.nama_produk')
            ->orderByDesc('total_terjual')
            ->get()
            ->groupBy('outlet_id')
            ->map(function ($items) {
                $top = $items->first();

                return $top ? "{$top->nama_produk} ({$top->total_terjual} pcs)" : '-';
            });

        // 7. Petugas (BC / SPG / Supervisor) per outlet
        $penjualanBulanIniRows = (clone $penjualanBulanIni)
            ->select('penjualan.outlet_id', 'penjualan.karyawan_id', 'penjualan.bc_karyawan_id', 'penjualan.spg_karyawan_id')
            ->distinct()
            ->get();

        $allKaryawanIds = $penjualanBulanIniRows->pluck('karyawan_id')
            ->merge($penjualanBulanIniRows->pluck('bc_karyawan_id'))
            ->merge($penjualanBulanIniRows->pluck('spg_karyawan_id'))
            ->filter()
            ->unique();

        $karyawanMap = Karyawan::with('role')->whereIn('karyawan_id', $allKaryawanIds)->get()->keyBy('karyawan_id');

        $petugasPerOutlet = $penjualanBulanIniRows->groupBy('outlet_id')->map(function ($items) use ($karyawanMap) {
            $petugasList = collect();
            foreach ($items as $item) {
                if ($item->bc_karyawan_id && isset($karyawanMap[$item->bc_karyawan_id])) {
                    $k = $karyawanMap[$item->bc_karyawan_id];
                    $petugasList->push("BC: {$k->nama_karyawan}");
                }
                if ($item->spg_karyawan_id && isset($karyawanMap[$item->spg_karyawan_id])) {
                    $k = $karyawanMap[$item->spg_karyawan_id];
                    $petugasList->push("SPG: {$k->nama_karyawan}");
                }
                if (! $item->bc_karyawan_id && ! $item->spg_karyawan_id && $item->karyawan_id && isset($karyawanMap[$item->karyawan_id])) {
                    $k = $karyawanMap[$item->karyawan_id];
                    $shortRole = match (strtolower($k->role?->nama_role ?? $k->jabatan ?? '')) {
                        'beauty consultant', 'bc' => 'BC',
                        'sales promotion girl', 'spg' => 'SPG',
                        'supervisor' => 'Supervisor',
                        default => 'PIC',
                    };
                    $petugasList->push("{$shortRole}: {$k->nama_karyawan}");
                }
            }

            return $petugasList->unique()->implode(', ');
        });

        // Rekap data penjualan per outlet periode ini (tanpa filter, agar identik dengan data yang masuk ke K-Means)
        $outletsDataRaw = (clone $penjualanBulanIni)
            ->join('outlet', 'outlet.outlet_id', '=', 'penjualan.outlet_id')
            ->leftJoin('wilayah', 'wilayah.wilayah_id', '=', 'outlet.wilayah_id')
            ->selectRaw('penjualan.outlet_id, outlet.nama_outlet, outlet.tipe_outlet, wilayah.nama_wilayah, SUM(penjualan.total_harga) as omzet, COUNT(penjualan.penjualan_id) as frekuensi')
            ->groupBy('penjualan.outlet_id', 'outlet.nama_outlet', 'outlet.tipe_outlet', 'wilayah.nama_wilayah')
            ->get();

        // Hasil K-Means asli dari sistem (FastAPI) - sama dengan halaman Analisis Performa.
        // Di-cache sebentar supaya membuka dashboard tidak memicu proses clustering berulang.
        $userId = Auth::id() ?? 0;
        $hasilKmeans = Cache::remember("dashboard.kmeans.{$selectedPeriode}.{$userId}", 300, function () use ($selectedPeriode) {
            return app(AnalisisPerformaController::class)->ambilHasilClustering($selectedPeriode);
        });
        if (! $hasilKmeans) {
            Cache::forget("dashboard.kmeans.{$selectedPeriode}.{$userId}");
        }
        $kmeansPerOutlet = collect($hasilKmeans['data']['data'] ?? [])->keyBy('id_outlet');
        $kmeansTersedia = $kmeansPerOutlet->isNotEmpty();

        // filter ketika dipilih di Select Button
        if (! empty($selectedWilayah)) {
            $outletsDataRaw = $outletsDataRaw->where('nama_wilayah', $selectedWilayah);
        }

        // filter ketika user input keyword di search
        if (! empty($searchQuery)) {
            $outletsDataRaw = $outletsDataRaw->filter(fn ($o) => str_contains(strtolower($o->nama_outlet), strtolower($searchQuery)));
        }

        $outletsDataRaw = $outletsDataRaw->sortByDesc('omzet')->values();

        $totalOutletAktif = $outletsDataRaw->count();
        $totalOmzetBaseline = $totalOmzet > 0 ? $totalOmzet : 1.0;

        // Cek data analisis K-Means jika sudah pernah dilakukan untuk periode ini
        $analisisInfo = AnalisisPerforma::where('periode_analisis', $selectedPeriode)->first();

        // 9. Susun Peringkat & Kinerja Outlet (status performa = hasil K-Means sistem)
        $peringkatOutlet = $outletsDataRaw->values()->map(function ($item, $index) use (
            $totalOmzetBaseline,
            $omzetBulanLaluPerOutlet,
            $jumlahProdukPerOutlet,
            $topProdukPerOutlet,
            $petugasPerOutlet,
            $kmeansPerOutlet,
            $kmeansTersedia
        ) {
            $omzetSekarang = (float) $item->omzet;
            $omzetLalu = (float) ($omzetBulanLaluPerOutlet[$item->outlet_id] ?? 0);

            // Pertumbuhan penjualan
            if ($omzetLalu > 0) {
                $growth = (($omzetSekarang - $omzetLalu) / $omzetLalu) * 100;
            } else {
                $growth = $omzetSekarang > 0 ? 100.0 : 0.0;
            }

            // Tingkat pencapaian terhadap total penjualan perusahaan (Kontribusi %)
            $kontribusi = ($omzetSekarang / $totalOmzetBaseline) * 100;

            // Status performa dari hasil K-Means (bukan dari peringkat omzet)
            $hasil = $kmeansPerOutlet->get($item->outlet_id);
            if ($hasil) {
                $status = trim($hasil['status']);
                $clusterCode = 'C'.$hasil['cluster'];
                $cluster = 'Cluster '.$hasil['cluster'].' ('.$status.')';
                $rekomendasi = $hasil['rekomendasi_promosi'];
            } else {
                $status = 'Belum dianalisis';
                $clusterCode = null;
                $cluster = '-';
                $rekomendasi = $kmeansTersedia
                    ? 'Outlet ini tidak termasuk dalam hasil analisis.'
                    : 'Jalankan Analisis Performa (pastikan FastAPI aktif) untuk periode ini.';
            }

            return (object) [
                'rank' => $index + 1,
                'outlet_id' => $item->outlet_id,
                'nama_outlet' => $item->nama_outlet,
                'tipe_outlet' => $item->tipe_outlet ?? 'Retail',
                'nama_wilayah' => $item->nama_wilayah ?? '-',
                'omzet' => $omzetSekarang,
                'omzet_lalu' => $omzetLalu,
                'kontribusi' => round($kontribusi, 2),
                'growth' => round($growth, 2),
                'frekuensi' => (int) $item->frekuensi,
                'jumlah_produk' => (int) ($jumlahProdukPerOutlet[$item->outlet_id] ?? 0),
                'produk_terlaris' => $topProdukPerOutlet->get($item->outlet_id, '-'),
                'petugas' => $petugasPerOutlet->get($item->outlet_id, '-'),
                'cluster' => $cluster,
                'cluster_code' => $clusterCode,
                'status' => $status,
                'rekomendasi' => $rekomendasi,
            ];
        });

        // filter status dipilih di select button
        if (! empty($selectedStatus)) {
            $peringkatOutlet = $peringkatOutlet->filter(fn ($o) => str_starts_with($o->status, $selectedStatus))->values();
        }

        switch ($sortBy) {
            case 'frekuensi_desc':
                $peringkatOutlet = $peringkatOutlet->sortByDesc('frekuensi')->values();
                break;

            case 'growth_desc':
                $peringkatOutlet = $peringkatOutlet->sortByDesc('growth')->values();
                break;

            case 'produk_desc':
                $peringkatOutlet = $peringkatOutlet->sortByDesc('jumlah_produk')->values();
                break;

            case 'kontribusi_desc':
                $peringkatOutlet = $peringkatOutlet->sortByDesc('kontribusi')->values();
                break;

            case 'omzet_desc':
            default:
                $peringkatOutlet = $peringkatOutlet->sortByDesc('omzet')->values();
                break;
        }

        $peringkatOutlet = $peringkatOutlet->map(function ($item, $index) {
            $item->rank = $index + 1;

            return $item;
        });

        // Outlet dengan penjualan tertinggi dan terendah
        $outletTertinggi = $peringkatOutlet->first();
        $outletTerendah = $peringkatOutlet->last();

        // 10. Top 8 Produk Paling Laku Terjual Keseluruhan
        $produkTerlarisOverall = (clone $penjualanBulanIni)
            ->join('transaksi', function ($join) {
                $join->whereRaw("transaksi.nomor_transaksi = CONCAT('TR-', penjualan.penjualan_id)");
            })
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->join('produk', 'produk.produk_id', '=', 'transaksi_detail.produk_id')
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'produk.kategori_id')
            ->selectRaw('produk.produk_id, produk.nama_produk, kategori.nama_kategori, SUM(transaksi_detail.jumlah) as total_terjual, SUM(transaksi_detail.subtotal) as total_omzet_produk')
            ->groupBy('produk.produk_id', 'produk.nama_produk', 'kategori.nama_kategori')
            ->orderByDesc('total_terjual')
            ->take(8)
            ->get();

        // 11. Tren Penjualan 6-12 Bulan Terakhir untuk Visualisasi Grafik Tren
        $trenBulanan = (clone $penjualanBase)
            ->where('penjualan.tanggal_penjualan', '<=', $periodeObj->copy()->endOfMonth()->toDateString())
            ->where('penjualan.tanggal_penjualan', '>=', $periodeObj->copy()->subMonths(11)->startOfMonth()->toDateString())
            ->selectRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m") as periode, SUM(penjualan.total_harga) as omzet, COUNT(penjualan.penjualan_id) as frekuensi, COUNT(DISTINCT penjualan.outlet_id) as outlet_aktif')
            ->groupByRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")')
            ->orderBy('periode', 'asc')
            ->get()
            ->map(function ($row) {
                $row->nama_bulan = Carbon::parse($row->periode.'-01')->translatedFormat('M Y');

                return $row;
            });

        // 12. Distribusi Pengelompokan / Cluster Outlet
        $countTinggi = $peringkatOutlet->filter(function ($item) {
            $st = strtolower($item->status);
            return str_contains($st, 'tinggi');
        })->count();
        $countSedang = $peringkatOutlet->filter(function ($item) {
            $st = strtolower($item->status);
            return str_contains($st, 'sedang');
        })->count();
        $countRendah = $peringkatOutlet->filter(function ($item) {
            $st = strtolower($item->status);
            return str_contains($st, 'rendah') || str_contains($st, 'sepi');
        })->count();

        $distribusiCluster = [
            'tinggi' => $countTinggi,
            'sedang' => $countSedang,
            'rendah' => $countRendah,
            'persen_tinggi' => $totalOutletAktif > 0 ? round(($countTinggi / $totalOutletAktif) * 100, 1) : 0,
            'persen_sedang' => $totalOutletAktif > 0 ? round(($countSedang / $totalOutletAktif) * 100, 1) : 0,
            'persen_rendah' => $totalOutletAktif > 0 ? round(($countRendah / $totalOutletAktif) * 100, 1) : 0,
        ];

        // 13. Rata-rata Penjualan & Transaksi per Outlet
        $rataRataOmzetPerOutlet = $totalOutletAktif > 0 ? $totalOmzet / $totalOutletAktif : 0;
        $rataRataTransaksiPerOutlet = $totalOutletAktif > 0 ? round($totalTransaksi / $totalOutletAktif, 1) : 0;

        return view('dashboard', compact(
            'availablePeriods',
            'selectedPeriode',
            'totalOmzet',
            'growthOmzetPerusahaan',
            'totalTransaksi',
            'growthTransaksiPerusahaan',
            'totalProdukTerjual',
            'totalOutletAktif',
            'totalOutletTerdaftar',
            'totalProdukTerdaftar',
            'rataRataOmzetPerOutlet',
            'rataRataTransaksiPerOutlet',
            'outletTertinggi',
            'outletTerendah',
            'peringkatOutlet',
            'produkTerlarisOverall',
            'trenBulanan',
            'distribusiCluster',
            'analisisInfo',
            'daftarWilayah',
            'selectedWilayah',
            'sortBy',
        ));
    }
}
