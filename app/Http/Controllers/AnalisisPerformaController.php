<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnalisisPerformaController extends Controller
{
    /**
     * Transaksi penjualan milik BC, SPG, dan Supervisor
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
        $allowedRoleIds = [2, 3, 4]; // 2: Supervisor, 3: BC, 4: SPG

        $query = DB::table('penjualan')
            ->join('karyawan', 'karyawan.karyawan_id', '=', 'penjualan.karyawan_id')
            ->join('role', 'role.role_id', '=', 'karyawan.role_id')
            ->where(function ($q) use ($allowedRoles, $allowedRoleIds) {
                $q->whereIn('role.nama_role', $allowedRoles)
                    ->orWhereIn('karyawan.jabatan', $allowedRoles)
                    ->orWhereIn('role.role_id', $allowedRoleIds);
            });

        // Filter scope berdasarkan role user yang sedang login jika bukan Admin
        $user = Auth::user();
        if ($user) {
            $namaRole = strtolower($user->role?->nama_role ?? '');
            $karyawan = $user->karyawan;

            if (in_array($namaRole, ['sales promotion girl', 'spg'])) {
                if ($karyawan) {
                    $query->where('penjualan.karyawan_id', $karyawan->karyawan_id);
                }
            } elseif ($namaRole === 'supervisor') {
                if ($user->wilayah_id ?? null) {
                    $query->join('outlet as out_spv', 'out_spv.outlet_id', '=', 'penjualan.outlet_id')
                        ->where('out_spv.wilayah_id', $user->wilayah_id);
                }
            } elseif (in_array($namaRole, ['beauty consultant', 'bc'])) {
                if ($karyawan && $karyawan->outlets()->exists()) {
                    $outletIds = $karyawan->outlets()->pluck('outlet.outlet_id')->all();
                    $query->whereIn('penjualan.outlet_id', $outletIds);
                } elseif ($karyawan) {
                    $query->where('penjualan.karyawan_id', $karyawan->karyawan_id);
                }
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $penjualanBase = $this->basePenjualanQuery();

        $riwayatAnalisis = $penjualanBase
            ->leftJoin('analisis_performa', function ($join) {
                $join->whereRaw('analisis_performa.periode_analisis = DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")');
            })
            ->selectRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m") as periode, COUNT(DISTINCT penjualan.outlet_id) as jumlah_outlet, MAX(penjualan.created_at) as created_at, MAX(analisis_performa.silhouette_score) as silhouette_score')
            ->groupByRaw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")')
            ->orderBy('periode', 'desc')
            ->get();

        $analisisTerakhir = $riwayatAnalisis->first();

        return view('analisis_performa.index', [
            'riwayatAnalisis' => $riwayatAnalisis,
            'analisisTerakhir' => $analisisTerakhir,
        ]);
    }

    public function proses(Request $request)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
        ]);

        $periode_analisis = $request->input('periode');

        // ambil data 5 indikator khusus dari penjualan milik BC, SPG, dan Supervisor
        $load_data = $this->hitungIndikator($periode_analisis);

        if (empty($load_data)) {
            return redirect()->back()->with('error', 'Tidak terdapat transaksi penjualan BC/SPG/Supervisor pada periode '.$periode_analisis);
        }

        // request ke FastAPI
        $fastApiUrl = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8001'), '/');
        try {
            $response = Http::timeout(15)->post($fastApiUrl.'/api/analisa-clustering', [
                'periode' => $periode_analisis,
                'data' => $load_data,
            ]);
            if ($response->successful()) {
                return redirect()->route('analisis-performa.show', $periode_analisis)->with('success', 'Analisis Performa Periode '.$periode_analisis.' berhasil diproses.');
            } else {
                return redirect()->back()->with('error', 'Service FastAPI mengembalikan respon gagal: '.$response->body());
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke FastAPI (port 8001). Pastikan server FastAPI sudah dijalankan.');
        }
    }

    public function show(string $periode)
    {
        $periode_analisis = $periode;
        $load_data = $this->hitungIndikator($periode_analisis);

        $hasil_clustering = null;

        $fastApiUrl = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8001'), '/');
        try {
            $response = Http::timeout(15)->post($fastApiUrl.'/api/analisa-clustering', [
                'periode' => $periode_analisis,
                'data' => $load_data,
            ]);
            if ($response->successful()) {
                $hasil_clustering = $response->json();
            } else {
                session()->flash('error', 'Service FastAPI mengembalikan status error: '.$response->status());
            }
        } catch (Exception $e) {
            session()->flash('error', 'Gagal terhubung ke FastAPI (port 8001). Pastikan server FastAPI sudah aktif dijalankan.');
        }

        if (!$hasil_clustering) {
            $analisisDb = DB::table('analisis_performa')->where('periode_analisis', $periode_analisis)->first();
            if ($analisisDb) {
                $hasil_clustering = [
                    'data' => [
                        'k_optimal' => $analisisDb->jumlah_cluster ?? 3,
                        'silhouette_score' => $analisisDb->silhouette_score ?? null,
                        'wcss' => [],
                        'data' => [],
                    ]
                ];
            }
        }

        // gabungkan data outlet dengan hasil cluster
        $data_outlet_gabungan = DB::table('outlet')->pluck('nama_outlet', 'outlet_id');

        $rekapan_outlet = collect($load_data)->map(function ($indikator) use ($hasil_clustering, $data_outlet_gabungan) {
            $info_hasil_cluster = collect($hasil_clustering['data']['data'] ?? [])->firstWhere('id_outlet', $indikator['id_outlet']);

            return [
                'id_outlet' => $indikator['id_outlet'],
                'nama_outlet' => $indikator['nama_outlet'] ?? ($data_outlet_gabungan[$indikator['id_outlet']] ?? 'Outlet#'.$indikator['id_outlet']),
                'omzet' => $indikator['omzet'],
                'growth' => $indikator['growth'],
                'frekuensi' => $indikator['frekuensi'],
                'jumlah_produk' => $indikator['jumlah_produk'],
                'kontribusi' => $indikator['kontribusi'],
                'petugas' => $indikator['petugas'] ?? '',
                'cluster' => $info_hasil_cluster['cluster'] ?? '-',
                'status' => $info_hasil_cluster['status'] ?? 'Sedang',
                'rekomendasi' => $info_hasil_cluster['rekomendasi_promosi'] ?? 'Tingkatkan promosi rutin',
            ];
        });

        // Simpan / update ke tabel analisis_performa jika ada hasil
        if ($hasil_clustering && isset($hasil_clustering['data'])) {
            try {
                DB::table('analisis_performa')->updateOrInsert(
                    ['periode_analisis' => $periode_analisis],
                    [
                        'tanggal_analisis' => now()->toDateString(),
                        'metode' => 'K-Means',
                        'jumlah_cluster' => $hasil_clustering['data']['k_optimal'] ?? 3,
                        'silhouette_score' => $hasil_clustering['data']['silhouette_score'] ?? null,
                        'status_analisis' => 'selesai',
                        'updated_at' => now(),
                    ]
                );
            } catch (Exception $e) {
                // Abaikan jika tabel belum siap atau error database
            }
        }

        return view('analisis_performa.show', [
            'periodeAnalisis' => $periode_analisis,
            'hasilClustering' => $hasil_clustering['data'] ?? null,
            'rekapOutlet' => $rekapan_outlet,
            'rekapan_outlet' => $rekapan_outlet,
        ]);
    }

    // function hitung 5 indikator khusus dari penjualan milik BC, SPG, dan Supervisor
    private function hitungIndikator(string $periode_analisis)
    {
        $periode_lalu = Carbon::parse($periode_analisis)->subMonth();

        $bulan_lalu = $periode_lalu->format('m');
        $bulan_sekarang = Carbon::parse($periode_analisis)->format('m');

        $tahun_lalu = $periode_lalu->format('Y');
        $tahun_sekarang = Carbon::parse($periode_analisis)->format('Y');

        // Query penjualan bulan ini khusus milik BC, SPG, dan Supervisor
        $penjualanBulanIni = (clone $this->basePenjualanQuery())
            ->whereMonth('penjualan.tanggal_penjualan', $bulan_sekarang)
            ->whereYear('penjualan.tanggal_penjualan', $tahun_sekarang);

        // Total omzet perusahaan: rumus kontribusi (khusus transaksi BC, SPG, Supervisor)
        $total_omzet = (clone $penjualanBulanIni)->sum('penjualan.total_harga');
        $total_omzet = $total_omzet > 0 ? (float) $total_omzet : 1.0;

        // Omzet bulan lalu untuk kalkulasi growth (khusus transaksi BC, SPG, Supervisor)
        $penjualanBulanLalu = (clone $this->basePenjualanQuery())
            ->whereMonth('penjualan.tanggal_penjualan', $bulan_lalu)
            ->whereYear('penjualan.tanggal_penjualan', $tahun_lalu);

        $omzet_bulan_lalu_per_outlet = $penjualanBulanLalu
            ->selectRaw('penjualan.outlet_id, SUM(penjualan.total_harga) as total_omzet_bulan_lalu')
            ->groupBy('penjualan.outlet_id')
            ->pluck('total_omzet_bulan_lalu', 'penjualan.outlet_id');

        // Rekap omzet & frekuensi per outlet bulan ini (khusus transaksi BC, SPG, Supervisor)
        $omzetFrekuensiPerOutlet = (clone $penjualanBulanIni)
            ->selectRaw('penjualan.outlet_id, COALESCE(SUM(penjualan.total_harga), 0) as omzet, COUNT(DISTINCT penjualan.penjualan_id) as frekuensi')
            ->groupBy('penjualan.outlet_id')
            ->get()
            ->keyBy('outlet_id');

        // Hitung jumlah_produk: transaksi → transaksi_detail
        $jumlah_produk_per_outlet = (clone $penjualanBulanIni)
            ->join('transaksi', function ($join) {
                $join->whereRaw("transaksi.nomor_transaksi = CONCAT('TR-', penjualan.penjualan_id)");
            })
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->selectRaw('penjualan.outlet_id, COALESCE(SUM(transaksi_detail.jumlah), 0) as jumlah_produk')
            ->groupBy('penjualan.outlet_id')
            ->pluck('jumlah_produk', 'penjualan.outlet_id');

        // Petugas (BC / SPG / Supervisor) yang menghasilkan penjualan di outlet tersebut pada bulan ini
        $petugasPerOutlet = (clone $penjualanBulanIni)
            ->select('penjualan.outlet_id', 'karyawan.nama_karyawan', 'role.nama_role')
            ->distinct()
            ->get()
            ->groupBy('outlet_id')
            ->map(function ($items) {
                return $items->map(function ($k) {
                    $shortRole = match (strtolower($k->nama_role)) {
                        'beauty consultant', 'bc' => 'BC',
                        'sales promotion girl', 'spg' => 'SPG',
                        'supervisor' => 'Supervisor',
                        default => $k->nama_role,
                    };

                    return "{$shortRole}: {$k->nama_karyawan}";
                })->unique()->implode(', ');
            });

        // Daftar outlet yang dianalisis
        $outletQuery = DB::table('outlet');
        $user = Auth::user();
        if ($user) {
            $namaRole = strtolower($user->role?->nama_role ?? '');
            if ($namaRole === 'supervisor' && ($user->wilayah_id ?? null)) {
                $outletQuery->where('wilayah_id', $user->wilayah_id);
            } elseif (in_array($namaRole, ['beauty consultant', 'bc']) && $user->karyawan && $user->karyawan->outlets()->exists()) {
                $outletQuery->whereIn('outlet_id', $user->karyawan->outlets()->pluck('outlet.outlet_id'));
            } elseif (in_array($namaRole, ['sales promotion girl', 'spg']) && $user->karyawan) {
                $activeOutletIds = (clone $penjualanBulanIni)->pluck('penjualan.outlet_id')->unique();
                $outletQuery->whereIn('outlet_id', $activeOutletIds);
            }
        }

        $data_outlet = $outletQuery->select('outlet.outlet_id as id_outlet', 'outlet.nama_outlet')->get();

        return $data_outlet->map(function ($item) use (
            $omzetFrekuensiPerOutlet,
            $omzet_bulan_lalu_per_outlet,
            $total_omzet,
            $jumlah_produk_per_outlet,
            $petugasPerOutlet
        ) {
            $penjualanInfo = $omzetFrekuensiPerOutlet->get($item->id_outlet);
            $omzet_sekarang = (float) ($penjualanInfo->omzet ?? 0);
            $frekuensi_sekarang = (float) ($penjualanInfo->frekuensi ?? 0);
            $omzet_lalu = (float) ($omzet_bulan_lalu_per_outlet[$item->id_outlet] ?? 0);

            // perhitungan growth
            if ($omzet_lalu > 0) {
                $growth = (($omzet_sekarang - $omzet_lalu) / $omzet_lalu) * 100;
            } else {
                $growth = $omzet_sekarang > 0 ? 100.0 : 0.0;
            }

            // kontribusi outlet terhadap total omzet
            $kontribusi = ($omzet_sekarang / $total_omzet) * 100;

            return [
                'id_outlet' => (int) $item->id_outlet,
                'nama_outlet' => $item->nama_outlet,
                'omzet' => $omzet_sekarang,
                'growth' => round($growth, 2),
                'kontribusi' => round($kontribusi, 2),
                'frekuensi' => $frekuensi_sekarang,
                'jumlah_produk' => (int) ($jumlah_produk_per_outlet[$item->id_outlet] ?? 0),
                'petugas' => $petugasPerOutlet->get($item->id_outlet, ''),
            ];
        })->toArray();
    }
}
