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
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Hasil Analisis Performa: {{ \Carbon\Carbon::parse($periodeAnalisis)->translatedFormat('F Y') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visualisasi pengelompokan performa outlet dan rekomendasi strategi promosi berbasis K-Means.
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
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Kurva Elbow --}}
            <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700">
                            Kurva Metode Elbow
                        </span>
                        <span class="text-xs font-semibold text-gray-400">
                            k Optimal = {{ $hasilClustering['k_optimal'] ?? 3 }}
                        </span>
                    </div>
                    <h3 class="mt-2 text-base font-bold text-gray-800">Evaluasi Inertia (WCSS)</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Penentuan titik siku (elbow point).
                    </p>
                </div>
                <div class="mt-4 h-64">
                    <canvas id="elbowChart"></canvas>
                </div>
            </div>

            {{-- Diagram Lingkaran --}}
            <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                        Distribusi Segmentasi
                    </span>
                    <h3 class="mt-2 text-base font-bold text-gray-800">Proporsi Performa Outlet</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Perbandingan persentase outlet pada cluster Ramai, Sedang, dan Sepi.
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

                                if (str_contains($status, 'Ramai') || $cluster === 0) {
                                    $warna_badge = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                } elseif (str_contains($status, 'Sepi') || $cluster === 2) {
                                    $warna_badge = 'bg-rose-50 text-rose-800 border-rose-200';
                                } else {
                                    $warna_badge = 'bg-amber-50 text-amber-800 border-amber-200';
                                }
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-center text-xs font-semibold text-gray-500">
                                    #{{ $item['id_outlet'] }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">
                                    {{ $item['nama_outlet'] }}
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
            const wcssRaw = @json($hasilClustering['wcss'] ?? []);
            const elbowLabels = Object.keys(wcssRaw).map(key => key.replace('k_', 'Cluster '));
            const elbowValues = Object.values(wcssRaw);

            const ctxElbow = document.getElementById('elbowChart');
            if (ctxElbow) {
                new Chart(ctxElbow.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: elbowLabels.length ? elbowLabels : ['Cluster 1', 'Cluster 2', 'Cluster 3',
                            'Cluster 4', 'Cluster 5'
                        ],
                        datasets: [{
                            label: 'WCSS (Inertia)',
                            data: elbowValues.length ? elbowValues : [100, 45, 18, 12, 8],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 5,
                            pointHoverRadius: 7
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
                                        return ' WCSS: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Render Doughnut Chart Proporsi Cluster
            const rekapData = @json($rekapOutlet ?? ($rekapan_outlet ?? []));
            let countRamai = 0,
                countSedang = 0,
                countSepi = 0;

            if (Array.isArray(rekapData)) {
                rekapData.forEach(item => {
                    const statusStr = String(item.status || '');
                    if (statusStr.includes('Ramai') || item.cluster === 0) {
                        countRamai++;
                    } else if (statusStr.includes('Sepi') || item.cluster === 2) {
                        countSepi++;
                    } else {
                        countSedang++;
                    }
                });
            }

            const ctxDoughnut = document.getElementById('clusterDoughnutChart');
            if (ctxDoughnut) {
                new Chart(ctxDoughnut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Ramai (Tinggi)', 'Sedang', 'Sepi (Rendah)'],
                        datasets: [{
                            data: [countRamai, countSedang, countSepi],
                            backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
