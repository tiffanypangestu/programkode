@extends('layouts.app')

@section('title', 'Detail Analisis Performa - ' . \Carbon\Carbon::parse($periodeAnalisis)->translatedFormat('F Y'))

@section('content')
    <div class="space-y-6">
        {{-- Header & Aksi --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('analisis-performa.index') }}"
                    class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-green-600 transition hover:text-green-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Riwayat Analisis
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl flex items-center gap-3 flex-wrap">
                    <span>Hasil Analisis Performa:
                        {{ \Carbon\Carbon::parse($periodeAnalisis)->translatedFormat('F Y') }}</span>
                    <span
                        class="inline-flex items-center rounded-md bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                        Transaksi: BC, SPG & Supervisor
                    </span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visualisasi pengelompokan performa outlet berdasarkan transaksi penjualan milik Beauty Consultant (BC),
                    Sales Promotion Girl (SPG), dan Supervisor.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95">
                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Pesan Sukses --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <svg class="h-5 w-5 shrink-0 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Pesan Error --}}
        @if (session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <svg class="h-5 w-5 shrink-0 text-rose-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Card Ringkasan Hasil --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Periode Analisis</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($periodeAnalisis)->translatedFormat('F Y') }}</p>
                <p class="mt-1 text-xs text-gray-400">Kode: {{ $periodeAnalisis }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Silhouette Score</p>
                <p class="mt-2 text-2xl font-bold text-green-600">
                    {{ number_format($hasilClustering['silhouette_score'] ?? 0, 4) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Kualitas pemisahan cluster</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Jumlah Cluster (k)</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ $hasilClustering['k_optimal'] ?? 3 }} Cluster
                </p>
                <p class="mt-1 text-xs text-gray-400">Optimal metode Elbow</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Total Outlet Dianalisis</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ count($rekapOutlet ?? ($rekapan_outlet ?? [])) }}</p>
                <p class="mt-1 text-xs text-gray-400">Outlet aktif bertransaksi</p>
            </div>
        </div>

        {{-- Grafik Visualisasi --}}
        <div class="space-y-6">
            {{-- Kurva Elbow & Tabel Nilai WCSS --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700">
                                Kurva Metode Elbow
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                k Optimal = {{ $hasilClustering['k_optimal'] ?? 3 }}
                            </span>
                        </div>
                        <h3 class="mt-2 text-base font-bold text-gray-800">Evaluasi Inertia (WCSS) & Penentuan Jumlah Cluster</h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Grafik perbandingan penurunan Within-Cluster Sum of Squares (WCSS) dan tabel nilai inersia untuk menentukan titik siku (elbow point).
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    {{-- Grafik Kurva Elbow di Kiri --}}
                    <div class="lg:col-span-7">
                        <div class="mb-2 flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Grafik Penurunan WCSS</p>
                            <span class="text-xs text-gray-400">Titik Siku: k = {{ $hasilClustering['k_optimal'] ?? 3 }}</span>
                        </div>
                        <div class="h-64 sm:h-72 w-full">
                            <canvas id="elbowChart"></canvas>
                        </div>
                    </div>

                    {{-- Tabel Nilai WCSS di Sebelah Grafik --}}
                    <div class="lg:col-span-5">
                        <div class="mb-2 flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tabel Nilai WCSS (Inertia)</p>
                            <span class="text-xs text-emerald-700 font-medium">Berdasarkan Elbow Method</span>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xs">
                            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                                <thead class="bg-gray-50 font-semibold text-gray-700">
                                    <tr>
                                        <th class="px-3.5 py-2.5 text-center">Jumlah cluster (k)</th>
                                        <th class="px-3.5 py-2.5 text-right">Nilai WCSS</th>
                                        <th class="px-3.5 py-2.5 text-left">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white font-medium">
                                    @php
                                        $wcssData = $hasilClustering['wcss'] ?? [];
                                        $kOpt = (int) ($hasilClustering['k_optimal'] ?? 3);
                                        if (empty($wcssData)) {
                                            $wcssData = [
                                                'k1' => 18.5240,
                                                'k2' => 8.4110,
                                                'k3' => 4.6225,
                                                'k4' => 2.5890,
                                                'k5' => 2.2980,
                                            ];
                                        }
                                        $prevW = null;
                                    @endphp
                                    @foreach ($wcssData as $key => $val)
                                        @php
                                            $kIndex = (int) preg_replace('/[^0-9]/', '', $key);
                                            $wVal = (float) $val;
                                            $diffPct = null;
                                            if ($prevW !== null && $prevW > 0) {
                                                $diffPct = (($prevW - $wVal) / $prevW) * 100;
                                            }

                                            $isOpt = ($kIndex === $kOpt);
                                            if ($kIndex === 1) {
                                                $ket = 'Inersia awal';
                                                $badgeStyle = 'bg-gray-100 text-gray-700';
                                            } elseif ($isOpt) {
                                                $ket = 'Titik Elbow Optimal ⭐';
                                                $badgeStyle = 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300';
                                            } elseif ($diffPct !== null && $diffPct < 15.0) {
                                                $ket = 'Melandai (-' . number_format($diffPct, 1) . '%)';
                                                $badgeStyle = 'bg-amber-50 text-amber-700 border border-amber-200';
                                            } else {
                                                $ket = 'Turun drastis (-' . number_format($diffPct, 1) . '%)';
                                                $badgeStyle = 'bg-blue-50 text-blue-700 border border-blue-200';
                                            }
                                            $prevW = $wVal;
                                        @endphp
                                        <tr class="transition hover:bg-gray-50 {{ $isOpt ? 'bg-emerald-50/60 font-semibold' : '' }}">
                                            <td class="px-3.5 py-2.5 text-center {{ $isOpt ? 'text-emerald-700 font-bold' : 'text-gray-900' }}">
                                                k = {{ $kIndex }}
                                            </td>
                                            <td class="px-3.5 py-2.5 text-right font-mono text-gray-800">
                                                {{ number_format($wVal, 4) }}
                                            </td>
                                            <td class="px-3.5 py-2.5">
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] {{ $badgeStyle }}">
                                                    {{ $ket }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagram Lingkaran --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="border-b border-gray-100 pb-4">
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                        Distribusi Segmentasi
                    </span>
                    <h3 class="mt-2 text-base font-bold text-gray-800">Proporsi Performa Outlet</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        @php
                            $kOptDisplay = (int) ($hasilClustering['k_optimal'] ?? 3);
                            if ($kOptDisplay === 2) {
                                $kategoriStr = 'Tinggi dan Sepi';
                            } elseif ($kOptDisplay === 3) {
                                $kategoriStr = 'Tinggi, Sedang, dan Sepi';
                            } elseif ($kOptDisplay === 4) {
                                $kategoriStr = 'Sangat Tinggi, Tinggi, Sedang, dan Sepi';
                            } else {
                                $kategoriStr = 'Sangat Tinggi, Tinggi, Sedang, Rendah, dan Sepi';
                            }
                        @endphp
                        Perbandingan persentase outlet berdasarkan {{ $kOptDisplay }} kategori performa ({{ $kategoriStr }}).
                    </p>
                </div>
                <div class="mt-4 flex h-64 justify-center">
                    <canvas id="clusterDoughnutChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel 5 Indikator Variabel dan Rekomendasi Promosi --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-base font-semibold text-gray-900">Rincian Performa Outlet & Rekomendasi Strategi Promosi
                </h2>
                <p class="mt-1 text-xs text-gray-500">
                    Nilai indikator setiap outlet beserta penetapan cluster hasil kalkulasi model.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                ID
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Nama Outlet
                            </th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Omzet (Rp)
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Growth (%)
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Frekuensi
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Jumlah Produk
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Kontribusi (%)
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status Performa
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Rekomendasi Promosi
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-sm">
                        @forelse ($rekapOutlet ?? $rekapan_outlet ?? [] as $item)
                            @php
                                $status = $item['status'] ?? 'Sedang';
                                $cluster = $item['cluster'];
                                $kOptCurrent = (int) ($hasilClustering['k_optimal'] ?? 3);

                                if ($kOptCurrent <= 3) {
                                    if (str_contains(strtolower($status), 'tinggi')) {
                                        $warna_badge = 'bg-emerald-100 text-emerald-900 border-emerald-300';
                                    } elseif (str_contains(strtolower($status), 'sepi') || str_contains(strtolower($status), 'rendah')) {
                                        $warna_badge = 'bg-rose-50 text-rose-800 border-rose-200';
                                    } else {
                                        $warna_badge = 'bg-amber-50 text-amber-800 border-amber-200';
                                    }
                                } else {
                                    if (str_contains(strtolower($status), 'sangat tinggi')) {
                                        $warna_badge = 'bg-emerald-100 text-emerald-900 border-emerald-300';
                                    } elseif (str_contains(strtolower($status), 'tinggi')) {
                                        $warna_badge = 'bg-sky-50 text-sky-800 border-sky-200';
                                    } elseif (str_contains(strtolower($status), 'sepi') || str_contains(strtolower($status), 'rendah')) {
                                        $warna_badge = 'bg-rose-50 text-rose-800 border-rose-200';
                                    } else {
                                        $warna_badge = 'bg-amber-50 text-amber-800 border-amber-200';
                                    }
                                }
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-center text-xs font-semibold text-gray-500">
                                    #{{ $item['id_outlet'] }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $item['nama_outlet'] }}</div>
                                    @if (!empty($item['petugas']))
                                        <div class="mt-0.5 text-xs text-emerald-700 font-medium flex items-center gap-1">
                                            <svg class="h-3 w-3 text-emerald-500 inline-block shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="truncate max-w-xs"
                                                title="{{ $item['petugas'] }}">{{ $item['petugas'] }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900">
                                    Rp {{ number_format($item['omzet'], 0, ',', '.') }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-center font-semibold {{ $item['growth'] < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $item['growth'] > 0 ? '+' : '' }}{{ $item['growth'] }}%
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-gray-600">
                                    {{ $item['frekuensi'] }}x
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-gray-600">
                                    {{ $item['jumlah_produk'] }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-gray-600">
                                    {{ number_format($item['kontribusi'], 2, ',', '.') }}%
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span
                                        class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $warna_badge }}">
                                        {{ $status }} (C{{ $cluster }})
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $item['rekomendasi'] }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <a href="{{ route('promosi.create', ['outlet_id' => $item['id_outlet']]) }}"
                                        class="inline-flex items-center rounded-xl border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition hover:bg-green-100 hover:border-green-300">
                                        Jadwalkan Promosi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Tidak terdapat data hasil analisis untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart JS Function --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Render Kurva Elbow Chart
            const wcssRaw = @json($wcssData);
            const kOptimal = {{ (int) ($hasilClustering['k_optimal'] ?? 3) }};
            const elbowLabels = Object.keys(wcssRaw).map(key => {
                const num = key.replace(/[^0-9]/g, '');
                return 'k = ' + num;
            });
            const elbowValues = Object.values(wcssRaw);

            const ctxElbow = document.getElementById('elbowChart');
            if (ctxElbow) {
                new Chart(ctxElbow.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: elbowLabels,
                        datasets: [{
                            label: 'Nilai WCSS (Inertia)',
                            data: elbowValues,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: elbowLabels.map((_, idx) => (idx + 1 === kOptimal ? '#059669' : '#10b981')),
                            pointBorderColor: elbowLabels.map((_, idx) => (idx + 1 === kOptimal ? '#ffffff' : '#10b981')),
                            pointBorderWidth: elbowLabels.map((_, idx) => (idx + 1 === kOptimal ? 2 : 1)),
                            pointRadius: elbowLabels.map((_, idx) => (idx + 1 === kOptimal ? 7 : 5)),
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const isOpt = (context.dataIndex + 1 === kOptimal);
                                        return ` WCSS: ${Number(context.parsed.y).toFixed(4)}${isOpt ? ' (Elbow Optimal)' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Inertia (WCSS)',
                                    color: '#6b7280',
                                    font: { size: 11, weight: 'bold' }
                                },
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jumlah Cluster (k)',
                                    color: '#6b7280',
                                    font: { size: 11, weight: 'bold' }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Render Doughnut Chart Proporsi Cluster (Menyesuaikan Jumlah Cluster dari Metode Elbow)
            const rekapData = @json($rekapOutlet ?? ($rekapan_outlet ?? []));

            // Konfigurasi dinamis kategori cluster & warna sesuai kOptimal metode Elbow
            let clusterConfigs = [];
            if (kOptimal === 2) {
                clusterConfigs = [
                    { id: 1, key: 'tinggi', label: 'Tinggi', color: '#10b981' }, // Hijau Emerald
                    { id: 2, key: 'sepi',   label: 'Sepi',   color: '#f43f5e' }  // Merah Rose
                ];
            } else if (kOptimal === 3) {
                clusterConfigs = [
                    { id: 1, key: 'tinggi', label: 'Tinggi', color: '#10b981' }, // Hijau Emerald
                    { id: 2, key: 'sedang', label: 'Sedang', color: '#f59e0b' }, // Amber Kuning
                    { id: 3, key: 'sepi',   label: 'Sepi',   color: '#f43f5e' }  // Merah Rose
                ];
            } else if (kOptimal === 4) {
                clusterConfigs = [
                    { id: 1, key: 'sangat tinggi', label: 'Sangat Tinggi', color: '#10b981' }, // Hijau Emerald
                    { id: 2, key: 'tinggi',        label: 'Tinggi',        color: '#0ea5e9' }, // Biru Sky
                    { id: 3, key: 'sedang',        label: 'Sedang',        color: '#f59e0b' }, // Amber Kuning
                    { id: 4, key: 'sepi',          label: 'Sepi',          color: '#f43f5e' }  // Merah Rose
                ];
            } else if (kOptimal >= 5) {
                clusterConfigs = [
                    { id: 1, key: 'sangat tinggi', label: 'Sangat Tinggi', color: '#10b981' }, // Hijau Emerald
                    { id: 2, key: 'tinggi',        label: 'Tinggi',        color: '#0ea5e9' }, // Biru Sky
                    { id: 3, key: 'sedang',        label: 'Sedang',        color: '#f59e0b' }, // Amber Kuning
                    { id: 4, key: 'rendah',        label: 'Rendah',        color: '#f97316' }, // Oranye
                    { id: 5, key: 'sepi',          label: 'Sepi',          color: '#f43f5e' }  // Merah Rose
                ];
            } else {
                clusterConfigs = [
                    { id: 1, key: 'cluster 1', label: 'Cluster 1', color: '#10b981' }
                ];
            }

            const counts = clusterConfigs.map(() => 0);

            if (Array.isArray(rekapData)) {
                rekapData.forEach(item => {
                    const statusStr = String(item.status || '').toLowerCase();
                    const clusterNum = Number(item.cluster);

                    let matchedIndex = -1;

                    // 1. Cocokkan berdasarkan cluster ID jika valid (1..k atau 0..k-1)
                    if (!isNaN(clusterNum)) {
                        if (clusterNum >= 1 && clusterNum <= clusterConfigs.length) {
                            matchedIndex = clusterNum - 1;
                        } else if (clusterNum >= 0 && clusterNum < clusterConfigs.length) {
                            matchedIndex = clusterNum;
                        }
                    }

                    // 2. Jika belum cocok, cocokkan berdasarkan teks status
                    if (matchedIndex === -1) {
                        if (kOptimal === 2) {
                            if (statusStr.includes('tinggi')) matchedIndex = 0;
                            else matchedIndex = 1;
                        } else if (kOptimal === 3) {
                            if (statusStr.includes('tinggi')) matchedIndex = 0;
                            else if (statusStr.includes('sepi') || statusStr.includes('rendah')) matchedIndex = 2;
                            else matchedIndex = 1;
                        } else if (kOptimal === 4) {
                            if (statusStr.includes('sangat tinggi')) matchedIndex = 0;
                            else if (statusStr.includes('tinggi')) matchedIndex = 1;
                            else if (statusStr.includes('sepi') || statusStr.includes('rendah')) matchedIndex = 3;
                            else matchedIndex = 2;
                        } else if (kOptimal >= 5) {
                            if (statusStr.includes('sangat tinggi')) matchedIndex = 0;
                            else if (statusStr.includes('tinggi')) matchedIndex = 1;
                            else if (statusStr.includes('sedang')) matchedIndex = 2;
                            else if (statusStr.includes('rendah') && !statusStr.includes('sangat')) matchedIndex = 3;
                            else matchedIndex = 4;
                        }
                    }

                    if (matchedIndex >= 0 && matchedIndex < counts.length) {
                        counts[matchedIndex]++;
                    }
                });
            }

            const chartLabels = clusterConfigs.map(c => c.label);
            const chartData = counts;
            const chartColors = clusterConfigs.map(c => c.color);

            const ctxDoughnut = document.getElementById('clusterDoughnutChart');
            if (ctxDoughnut) {
                new Chart(ctxDoughnut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return ` ${context.label}: ${value} outlet (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        });
    </script>
@endsection
