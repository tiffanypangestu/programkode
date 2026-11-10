@extends('layouts.app')

@section('title', 'Dashboard Performa Outlet - UD Medan')

@section('content')
    <div class="space-y-10 lg:space-y-12 pb-16">
        {{-- Header & Filter Periode --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-gray-200 pb-6">
            <div>
                <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-gray-800">
                    Dashboard Performa Outlet
                </h1>
            </div>

            {{-- Filter Periode & Cetak Laporan --}}
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
                    <label for="periode-select" class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Periode:
                    </label>
                    <select id="periode-select" name="periode" onchange="this.form.submit()"
                        class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-800 shadow-sm transition hover:border-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        @foreach ($availablePeriods as $p)
                            <option value="{{ $p }}" {{ $selectedPeriode === $p ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($p . '-01')->translatedFormat('F Y') }} ({{ $p }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95">
                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Ringkasan Statistik Utama (Metric Cards) --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:border-emerald-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Omzet Penjualan
                        </p>
                        <p class="mt-2 text-xl sm:text-2xl font-bold text-gray-800 tracking-tight whitespace-nowrap">
                            Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-4 ring-emerald-50/60 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs">
                    @if ($growthOmzetPerusahaan >= 0)
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 font-medium text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            +{{ number_format($growthOmzetPerusahaan, 2) }}%
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 font-medium text-rose-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            {{ number_format($growthOmzetPerusahaan, 2) }}%
                        </span>
                    @endif
                    <span class="text-gray-400">vs bulan sebelumnya</span>
                </div>
            </div>

            {{-- Card 2: Total Frekuensi Transaksi --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Jumlah Transaksi</p>
                        <p class="mt-2 text-xl sm:text-2xl font-bold text-gray-800 tracking-tight whitespace-nowrap">
                            {{ number_format($totalTransaksi, 0, ',', '.') }} <span
                                class="text-xs font-normal text-gray-400">Kali</span>
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-4 ring-blue-50/60 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs">
                    @if ($growthTransaksiPerusahaan >= 0)
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 font-medium text-emerald-700">
                            +{{ number_format($growthTransaksiPerusahaan, 2) }}%
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 font-medium text-rose-700">
                            {{ number_format($growthTransaksiPerusahaan, 2) }}%
                        </span>
                    @endif
                    <span class="text-gray-400">Rata-rata: {{ $rataRataTransaksiPerOutlet }} transaksi per outlet</span>
                </div>
            </div>

            {{-- Card 3: Total Produk Terjual --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:border-indigo-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Jumlah Produk Terjual
                        </p>
                        <p class="mt-2 text-xl sm:text-2xl font-bold text-gray-800 tracking-tight whitespace-nowrap">
                            {{ number_format($totalProdukTerjual, 0, ',', '.') }} <span
                                class="text-xs font-normal text-gray-400">Pcs</span>
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-4 ring-indigo-50/60 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
                    <span>Rata-rata: {{ $totalTransaksi > 0 ? round($totalProdukTerjual / $totalTransaksi, 1) : 0 }} pcs
                        per transaksi</span>
                </div>
            </div>

            {{-- Card 4: Outlet Aktif Bertransaksi --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Outlet Aktif</p>
                        <p class="mt-2 text-xl sm:text-2xl font-bold text-gray-800 tracking-tight whitespace-nowrap">
                            {{ $totalOutletAktif }} <span class="text-xs font-normal text-gray-400">/
                                {{ $totalOutletTerdaftar }}</span>
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 ring-4 ring-purple-50/60 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs">
                    <div class="h-2 flex-1 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full bg-purple-600 rounded-full"
                            style="width: {{ $totalOutletTerdaftar > 0 ? round(($totalOutletAktif / $totalOutletTerdaftar) * 100) : 0 }}%">
                        </div>
                    </div>
                    <span class="font-medium text-purple-700">
                        {{ $totalOutletTerdaftar > 0 ? round(($totalOutletAktif / $totalOutletTerdaftar) * 100) : 0 }}%
                        Aktif
                    </span>
                </div>
            </div>
        </div>

        {{-- Highlight Performa Outlet: Tertinggi vs Terendah --}}
        <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-2">
            {{-- Outlet Tertinggi (Top Performer) --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-emerald-200/80 bg-linear-to-br from-emerald-50/30 via-white to-white p-7 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-4 ring-amber-50/60 shadow-xs">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 8V5a2 2 0 00-2-2H7a2 2 0 00-2 2v3a6 6 0 005.9 6h.2A6 6 0 0017 8zm0 0h2a2 2 0 012 2v1a4 4 0 01-4 4h-1m-10-7H3a2 2 0 00-2 2v1a4 4 0 004 4h1m4 4v3m-3 0h6" />
                            </svg>
                        </div>
                        <div>
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-800 shadow-2xs">
                                Peringkat #1 • Penjualan Tertinggi
                            </span>
                            <h3 class="mt-1.5 text-xl font-bold text-gray-800 tracking-tight">
                                {{ $outletTertinggi?->nama_outlet ?? '-' }}
                            </h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-normal">
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 capitalize">
                                    Tipe: {{ $outletTertinggi?->tipe_outlet ?? 'Toko' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Total Omzet</p>
                        <p class="text-lg sm:text-xl font-bold text-emerald-600 tracking-tight whitespace-nowrap">
                            Rp {{ number_format($outletTertinggi?->omzet ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- 3 Indikator Performa (Box Modern Terpisah) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4 my-6">
                    <div class="rounded-xl bg-white border border-emerald-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Kontribusi</p>
                        <p class="mt-1 text-base font-bold text-emerald-700">{{ $outletTertinggi?->kontribusi ?? 0 }}%</p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Pertumbuhan</p>
                        <p
                            class="mt-1 text-base font-bold {{ ($outletTertinggi?->growth ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ ($outletTertinggi?->growth ?? 0) >= 0 ? '+' : '' }}{{ $outletTertinggi?->growth ?? 0 }}%
                        </p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Frekuensi</p>
                        <p class="mt-1 text-base font-bold text-gray-800">{{ $outletTertinggi?->frekuensi ?? 0 }}x
                            Transaksi</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-emerald-100 text-xs">
                    <span class="text-gray-500">
                        Produk Terlaris: <strong
                            class="text-gray-800 font-semibold">{{ $outletTertinggi?->produk_terlaris ?? '-' }}</strong>
                    </span>
                    <span
                        class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-emerald-700 font-semibold text-[11px]">
                        {{ $outletTertinggi?->cluster ?? 'Cluster 1 (Ramai)' }}
                    </span>
                </div>
            </div>

            {{-- Outlet Terendah (Lowest Performer / Perlu Perhatian) --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-rose-200/80 bg-linear-to-br from-rose-50/30 via-white to-white p-7 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 ring-4 ring-rose-50/60 shadow-xs">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <span
                                class="inline-flex items-center rounded-full bg-rose-100 px-3 py-0.5 text-xs font-semibold text-rose-800 shadow-2xs">
                                Peringkat #{{ $outletTerendah?->rank ?? $totalOutletAktif }} • Penjualan Terendah
                            </span>
                            <h3 class="mt-1.5 text-xl font-bold text-gray-800 tracking-tight">
                                {{ $outletTerendah?->nama_outlet ?? '-' }}
                            </h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-normal">
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 capitalize">
                                    Tipe: {{ $outletTerendah?->tipe_outlet ?? 'Toko' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Total Omzet</p>
                        <p class="text-lg sm:text-xl font-bold text-rose-600 tracking-tight whitespace-nowrap">
                            Rp {{ number_format($outletTerendah?->omzet ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- 3 Indikator Performa (Box Modern Terpisah) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4 my-6">
                    <div class="rounded-xl bg-white border border-rose-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Kontribusi</p>
                        <p class="mt-1 text-base font-bold text-gray-700">{{ $outletTerendah?->kontribusi ?? 0 }}%</p>
                    </div>
                    <div class="rounded-xl bg-white border border-rose-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Pertumbuhan</p>
                        <p
                            class="mt-1 text-base font-bold {{ ($outletTerendah?->growth ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ ($outletTerendah?->growth ?? 0) >= 0 ? '+' : '' }}{{ $outletTerendah?->growth ?? 0 }}%
                        </p>
                    </div>
                    <div class="rounded-xl bg-white border border-rose-100 p-3.5 text-center shadow-2xs">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Frekuensi</p>
                        <p class="mt-1 text-base font-bold text-gray-800">{{ $outletTerendah?->frekuensi ?? 0 }}x
                            Transaksi</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-rose-100 text-xs">
                    <span class="text-gray-500">
                        Rekomendasi: <span
                            class="text-rose-700 font-medium">{{ $outletTerendah?->rekomendasi ?? 'Perlu evaluasi promosi & penambahan variasi stok' }}</span>
                    </span>
                    <span
                        class="rounded-full bg-rose-50 border border-rose-200 px-2.5 py-0.5 text-rose-700 font-semibold text-[11px]">
                        {{ $outletTerendah?->cluster ?? 'Cluster 3 (Sepi)' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Visualisasi Data & Grafik (Chart.js) --}}
        <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
            {{-- Grafik 1: Tren Omzet & Transaksi Bulanan (Span 2 cols) --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-sm lg:col-span-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Tren Pertumbuhan Omzet & Transaksi Perusahaan
                        </h3>
                        <p class="text-xs text-gray-500">Pergerakan total penjualan dan jumlah transaksi bulanan.</p>
                    </div>
                    <span
                        class="mt-2 sm:mt-0 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-lg">
                        12 Bulan Terakhir
                    </span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="trenBulananChart"></canvas>
                </div>
            </div>

            {{-- Grafik 2: Distribusi Pengelompokan / Cluster Outlet (Span 1 col) --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Hasil Pengelompokkan Outlet</h3>
                    <p class="text-xs text-gray-500">Proporsi outlet berdasarkan kategori klaster performa.</p>
                </div>
                <div class="relative h-56 w-full flex items-center justify-center">
                    <canvas id="clusterPieChart"></canvas>
                </div>
                <div class="mt-5 grid grid-cols-3 gap-2.5 text-center text-xs border-t border-gray-100 pt-3.5">
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-100/60">
                        <p class="font-medium text-[11px]">Tinggi (C1)</p>
                        <p class="text-base font-bold text-emerald-700">{{ $distribusiCluster['tinggi'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ $distribusiCluster['persen_tinggi'] }}%</p>
                    </div>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-100/60">
                        <p class="font-medium text-[11px]">Sedang (C2)</p>
                        <p class="text-base font-bold text-amber-700">{{ $distribusiCluster['sedang'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ $distribusiCluster['persen_sedang'] }}%</p>
                    </div>
                    <div class="p-2 rounded-xl bg-rose-50 text-rose-800 border border-rose-100/60">
                        <p class="font-medium text-[11px]">Rendah (C3)</p>
                        <p class="text-base font-bold text-rose-700">{{ $distribusiCluster['rendah'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ $distribusiCluster['persen_rendah'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik 3 & 4: Top 10 Outlet & Produk Terlaris --}}
        <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-2">
            {{-- Grafik 3: 10 Outlet dengan Omzet Tertinggi --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Top 10 Outlet Berdasarkan Omzet</h3>
                        <p class="text-xs text-gray-500">Perbandingan 10 outlet dengan nilai penjualan terbesar periode
                            ini.</p>
                    </div>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">Peringkat
                        1-10</span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="topOutletsChart"></canvas>
                </div>
            </div>

            {{-- Grafik 4: Produk Paling Laku Terjual (Overall) --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Produk Paling Laku Terjual</h3>
                        <p class="text-xs text-gray-500">Jumlah kuantitas (pcs) produk paling diminati pelanggan secara
                            keseluruhan.</p>
                    </div>
                    <span
                        class="text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">Top
                        Best-Sellers</span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel Peringkat & Kinerja Seluruh Outlet --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div
                class="border-b border-gray-100 p-6 sm:p-7 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span>Peringkat & Kinerja Seluruh Outlet</span>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                            {{ $totalOutletAktif }} Outlet Aktif
                        </span>
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Daftar urutan outlet berdasarkan performa penjualan, tingkat kontribusi terhadap total omzet
                        perusahaan, frekuensi transaksi, dan status hasil analisis.
                    </p>
                </div>

                {{-- Search & Filter Klaster --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Cari nama outlet..."
                            class="w-48 sm:w-64 rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                    </div>
                    <select id="filterCluster"
                        class="rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm focus:border-emerald-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="Tinggi">Tinggi (C1)</option>
                        <option value="Sedang">Sedang (C2)</option>
                        <option value="Rendah">Rendah (C3)</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left" id="outletTable">
                    <thead class="bg-gray-50 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3 text-center">Rank</th>
                            <th class="px-5 py-3">Nama Outlet</th>
                            <th class="px-5 py-3">Petugas Lapangan</th>
                            <th class="px-5 py-3 text-right">Omzet (Rp)</th>
                            <th class="px-5 py-3 text-center">Kontribusi (%)</th>
                            <th class="px-5 py-3 text-center">Growth (%)</th>
                            <th class="px-5 py-3 text-center">Frekuensi</th>
                            <th class="px-5 py-3 text-center">Produk Terjual</th>
                            <th class="px-5 py-3">Produk Terlaris di Outlet</th>
                            <th class="px-5 py-3 text-center">Status Performa</th>
                            <th class="px-5 py-3">Rekomendasi Strategis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse ($peringkatOutlet as $row)
                            <tr class="transition hover:bg-gray-50/80 outlet-row" data-status="{{ $row->status }}">
                                {{-- Rank --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                    @if ($row->rank == 1)
                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-400 text-white font-bold text-xs shadow-sm">1</span>
                                    @elseif ($row->rank == 2)
                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-300 text-slate-800 font-bold text-xs shadow-sm">2</span>
                                    @elseif ($row->rank == 3)
                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-700 text-white font-bold text-xs shadow-sm">3</span>
                                    @else
                                        <span class="text-gray-500 font-medium">#{{ $row->rank }}</span>
                                    @endif
                                </td>

                                {{-- Nama Outlet & Info --}}
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <div class="font-semibold text-gray-900 outlet-name">{{ $row->nama_outlet }}</div>
                                    <div class="text-[11px] text-gray-400 capitalize">
                                        {{ $row->tipe_outlet }}
                                    </div>
                                </td>

                                {{-- Petugas Lapangan --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-gray-600">
                                    @if ($row->petugas)
                                        <span
                                            class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-normal text-gray-700">
                                            {{ $row->petugas }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>

                                {{-- Omzet --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-right font-semibold text-gray-800">
                                    Rp {{ number_format($row->omzet, 0, ',', '.') }}
                                </td>

                                {{-- Kontribusi % --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <div class="h-2 w-14 rounded-full bg-gray-100 overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full"
                                                style="width: {{ min(100, $row->kontribusi * 5) }}%"></div>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $row->kontribusi }}%</span>
                                    </div>
                                </td>

                                {{-- Growth % --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                    @if ($row->growth > 0)
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 font-medium text-emerald-700">
                                            +{{ $row->growth }}%
                                        </span>
                                    @elseif ($row->growth < 0)
                                        <span
                                            class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 font-medium text-rose-700">
                                            {{ $row->growth }}%
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 font-normal text-gray-600">
                                            0.00%
                                        </span>
                                    @endif
                                </td>

                                {{-- Frekuensi --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center font-normal text-gray-700">
                                    {{ $row->frekuensi }}x
                                </td>

                                {{-- Jumlah Produk --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center font-normal text-gray-700">
                                    {{ number_format($row->jumlah_produk, 0, ',', '.') }} pcs
                                </td>

                                {{-- Produk Paling Laku --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-gray-700">
                                    <span
                                        class="font-normal text-emerald-800 bg-emerald-50/70 border border-emerald-100 px-2 py-0.5 rounded">
                                        {{ $row->produk_terlaris }}
                                    </span>
                                </td>

                                {{-- Status Performa & Cluster --}}
                                <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                    @if ($row->status === 'Tinggi')
                                        <span
                                            class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            {{ $row->cluster_code }} • Tinggi (Ramai)
                                        </span>
                                    @elseif ($row->status === 'Sedang')
                                        <span
                                            class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            {{ $row->cluster_code }} • Sedang
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-800">
                                            {{ $row->cluster_code }} • Rendah (Sepi)
                                        </span>
                                    @endif
                                </td>

                                {{-- Rekomendasi --}}
                                <td class="px-5 py-3.5 text-xs text-gray-600 min-w-50">
                                    {{ $row->rekomendasi }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center text-gray-400">
                                    Tidak ada data performa outlet untuk periode {{ $selectedPeriode }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js Script & Table Filter Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. Grafik Tren Omzet & Transaksi Bulanan ---
            const trenData = @json($trenBulanan);
            const trenLabels = trenData.map(d => d.nama_bulan || d.periode);
            const trenOmzetValues = trenData.map(d => Number(d.omzet));
            const trenFrekuensiValues = trenData.map(d => Number(d.frekuensi));

            const ctxTren = document.getElementById('trenBulananChart');
            if (ctxTren) {
                new Chart(ctxTren.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: trenLabels,
                        datasets: [{
                                label: 'Omzet Penjualan (Rp)',
                                data: trenOmzetValues,
                                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                borderColor: '#059669',
                                borderWidth: 1.5,
                                borderRadius: 6,
                                yAxisID: 'yOmzet',
                                order: 2
                            },
                            {
                                label: 'Jumlah Transaksi',
                                data: trenFrekuensiValues,
                                type: 'line',
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#2563eb',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: false,
                                tension: 0.3,
                                yAxisID: 'yFrekuensi',
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            yOmzet: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') +
                                            ' jt';
                                    }
                                },
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)'
                                }
                            },
                            yFrekuensi: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + ' tx';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.yAxisID === 'yOmzet') {
                                            return 'Omzet: Rp ' + Number(context.raw).toLocaleString(
                                                'id-ID');
                                        }
                                        return 'Transaksi: ' + Number(context.raw).toLocaleString(
                                            'id-ID') + ' kali';
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }

            // --- 2. Donut Chart Pengelompokan / Cluster Outlet ---
            const distData = @json($distribusiCluster);
            const ctxCluster = document.getElementById('clusterPieChart');
            if (ctxCluster) {
                new Chart(ctxCluster.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Tinggi / Ramai (C1)', 'Sedang (C2)', 'Rendah / Sepi (C3)'],
                        datasets: [{
                            data: [distData.tinggi, distData.sedang, distData.rendah],
                            backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                            hoverOffset: 4,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // --- 3. Top 10 Outlet Bar Chart ---
            const peringkatData = @json($peringkatOutlet);
            const top10 = peringkatData.slice(0, 10);
            const top10Labels = top10.map(d => d.nama_outlet);
            const top10Omzet = top10.map(d => d.omzet);

            const ctxTopOutlets = document.getElementById('topOutletsChart');
            if (ctxTopOutlets) {
                new Chart(ctxTopOutlets.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: top10Labels,
                        datasets: [{
                            label: 'Omzet (Rp)',
                            data: top10Omzet,
                            backgroundColor: '#059669',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + 'M';
                                    }
                                },
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // --- 4. Produk Paling Laku Terjual Chart ---
            const topProductsData = @json($produkTerlarisOverall);
            const productLabels = topProductsData.map(d => d.nama_produk);
            const productQty = topProductsData.map(d => Number(d.total_terjual));

            const ctxTopProducts = document.getElementById('topProductsChart');
            if (ctxTopProducts) {
                new Chart(ctxTopProducts.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: productLabels,
                        datasets: [{
                            label: 'Unit Terjual (Pcs)',
                            data: productQty,
                            backgroundColor: '#6366f1',
                            borderRadius: 6
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
                                        return context.raw.toLocaleString('id-ID') + ' pcs';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + ' pcs';
                                    }
                                },
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 30
                                }
                            }
                        }
                    }
                });
            }

            // --- 5. Interactive Table Filter & Search ---
            const searchInput = document.getElementById('tableSearch');
            const filterSelect = document.getElementById('filterCluster');
            const tableRows = document.querySelectorAll('#outletTable tbody tr.outlet-row');

            function filterTable() {
                const query = (searchInput?.value || '').toLowerCase().trim();
                const statusFilter = (filterSelect?.value || '').trim();

                tableRows.forEach(row => {
                    const outletName = (row.querySelector('.outlet-name')?.textContent || '').toLowerCase();
                    const rowStatus = row.getAttribute('data-status') || '';

                    const matchesQuery = outletName.includes(query);
                    const matchesStatus = !statusFilter || rowStatus === statusFilter;

                    if (matchesQuery && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (filterSelect) filterSelect.addEventListener('change', filterTable);
        });
    </script>
@endsection
