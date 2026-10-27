<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnalisisPerformaController extends Controller
{
    public function index(Request $request)
    {
        $riwayatAnalisis = DB::table('penjualan')
            ->leftJoin('analisis_performa', function ($join) {
                $join->on('analisis_performa.periode_analisis', '=', DB::raw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")'));
            })
            ->select(
                DB::raw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m") as periode'),
                DB::raw('COUNT(DISTINCT penjualan.outlet_id) as jumlah_outlet'),
                DB::raw('MAX(penjualan.created_at) as created_at'),
                DB::raw('MAX(analisis_performa.silhouette_score) as silhouette_score')
            )
            ->groupBy(DB::raw('DATE_FORMAT(penjualan.tanggal_penjualan, "%Y-%m")'))
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

        // ambil data 5 indikator
        $load_data = $this->hitungIndikator($periode_analisis);

        if (empty($load_data)) {
            return redirect()->back()->with('error', 'Tidak terdapat transaksi penjualan pada periode '.$periode_analisis);
        }

        // request ke FastAPI
        try {
            $response = Http::timeout(10)->post('http://127.0.0.1:8001/api/analisa-clustering', [
                'periode' => $periode_analisis,
                'data' => $load_data,
            ]);
            if ($response->successful()) {
                return redirect()->route('analisis-performa.show', $periode_analisis)->with('success', 'Analisis Performa Periode '.$periode_analisis.' berhasil diproses.');
            } else {
                return redirect()->back()->with('error', 'Service FastAPI mengembalikan respon gagal.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke FastAPI: '.$e->getMessage());
        }
    }

    public function show(string $periode)
    {
        $periode_analisis = $periode;
        $load_data = $this->hitungIndikator($periode_analisis);

        $hasil_clustering = null;

        try {
            $response = Http::timeout(10)->post('http://127.0.0.1:8001/api/analisa-clustering', [
                'periode' => $periode_analisis,
                'data' => $load_data,
            ]);
            if ($response->successful()) {
                $hasil_clustering = $response->json();
            }
        } catch (Exception $e) {
            session()->flash('error', 'Gagal terhubung ke FastAPI');
        }

        // gabungkan data outlet dengan hasil cluster
        $data_outlet_gabungan = DB::table('outlet')->pluck('nama_outlet', 'outlet_id');

        $rekapan_outlet = collect($load_data)->map(function ($indikator) use ($hasil_clustering, $data_outlet_gabungan) {
            $info_hasil_cluster = collect($hasil_clustering['data']['data'] ?? [])->firstWhere('id_outlet', $indikator['id_outlet']);

            return [
                'id_outlet' => $indikator['id_outlet'],
                'nama_outlet' => $data_outlet_gabungan[$indikator['id_outlet']] ?? 'Outlet#'.$indikator['id_outlet'],
                'omzet' => $indikator['omzet'],
                'growth' => $indikator['growth'],
                'frekuensi' => $indikator['frekuensi'],
                'jumlah_produk' => $indikator['jumlah_produk'],
                'kontribusi' => $indikator['kontribusi'],
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

    // function hitung indikator
    private function hitungIndikator(string $periode_analisis)
    {
        $periode_lalu = Carbon::parse($periode_analisis)->subMonth();

        $bulan_lalu = $periode_lalu->format('m');
        $bulan_sekarang = Carbon::parse($periode_analisis)->format('m');

        $tahun_lalu = $periode_lalu->format('Y');
        $tahun_sekarang = Carbon::parse($periode_analisis)->format('Y');

        // total omzet perusahaan: rumus kontribusi
        $total_omzet = DB::table('penjualan')->whereMonth('tanggal_penjualan', $bulan_sekarang)->whereYear('tanggal_penjualan', $tahun_sekarang)->sum('total_harga');

        $total_omzet = $total_omzet > 0 ? $total_omzet : 1;

        // omzet bulan lalu untuk kalkulasi growth
        $omzet_bulan_lalu_per_outlet = DB::table('penjualan')->select('outlet_id', DB::raw('SUM(total_harga) as total_omzet_bulan_lalu'))->whereMonth('tanggal_penjualan', $bulan_lalu)->whereYear('tanggal_penjualan', $tahun_lalu)->groupBy('outlet_id')->pluck('total_omzet_bulan_lalu', 'outlet_id');

        // data indikator bulan ini
        $data_outlet = DB::table('outlet')
            ->leftJoin('penjualan', function ($join) use ($bulan_sekarang, $tahun_sekarang) {
                $join->on('outlet.outlet_id', '=', 'penjualan.outlet_id')->whereMonth('penjualan.tanggal_penjualan', '=', $bulan_sekarang)->whereYear('penjualan.tanggal_penjualan', '=', $tahun_sekarang);
            })
            ->leftJoin('transaksi_detail', 'penjualan.penjualan_id', '=', 'transaksi_detail.transaksi_id')
            ->select(
                'outlet.outlet_id as id_outlet',
                'outlet.nama_outlet',
                DB::raw('COALESCE(SUM(penjualan.total_harga), 0) as omzet'),
                DB::raw('COUNT(DISTINCT penjualan.penjualan_id) as frekuensi'),
                DB::raw('COALESCE(SUM(transaksi_detail.jumlah), 0) as jumlah_produk')
            )
            ->groupBy('outlet.outlet_id', 'outlet.nama_outlet')
            ->get();

        return $data_outlet->map(function ($item) use ($omzet_bulan_lalu_per_outlet, $total_omzet) {
            $omzet_sekarang = (float) $item->omzet;
            $omzet_lalu = (float) ($omzet_bulan_lalu_per_outlet[$item->id_outlet] ?? 0);

            // perhitungan growth
            if ($omzet_lalu > 0) {
                $growth = (($omzet_sekarang - $omzet_lalu) / $omzet_lalu) * 100;
            } else {
                $growth = $omzet_sekarang > 0 ? 100.0 : 0.0;
            }

            // kontribusi outlet terhadap total penjualan perusahaan
            $kontribusi = ($omzet_sekarang / $total_omzet) * 100;

            return [
                'id_outlet' => (int) $item->id_outlet,
                'omzet' => $omzet_sekarang,
                'growth' => round($growth, 2),
                'kontribusi' => round($kontribusi, 2),
                'frekuensi' => (float) $item->frekuensi,
                'jumlah_produk' => (int) $item->jumlah_produk,
            ];
        })->toArray();
    }
}
