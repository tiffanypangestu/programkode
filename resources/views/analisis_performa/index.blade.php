@extends('layouts.app')

@section('title', 'Analisis Performa Outlet')

@section('content')
    <div class="space-y-6">
        {{-- Header & Form Jalankan Analisis --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-md bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">
                        K-Means Clustering
                    </span>
                    <span class="text-xs text-gray-500">5 Indikator Variabel</span>
                </div>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Analisis Performa Outlet
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pengelompokan performa outlet berdasarkan Omzet, Growth, Kontribusi, Frekuensi, dan Volume Produk.
                </p>
            </div>

            {{-- Form Periode & Button --}}
            <form action="{{ route('analisis-performa.proses') }}" method="POST" class="flex flex-wrap items-center gap-2.5">
                @csrf
                <div class="relative flex items-center">
                    <input type="month" name="periode" value="{{ request('periode', date('Y-m')) }}" required
                        class="rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-400 focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Jalankan Analisis
                </button>
            </form>
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
            <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <svg class="h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-2xl">
                    📊
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Histori Analisis</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ count($riwayatAnalisis) }}</p>
                    <p class="text-xs text-gray-400">Riwayat Analisis Tersimpan</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-2xl">
                    📈
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Silhouette Score Terakhir</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ number_format($analisisTerakhir->silhouette_score ?? 0, 4) }}
                    </p>
                    <p class="text-xs text-gray-400">Evaluasi Pemisahan Cluster</p>
                </div>
            </div>

            <div
                class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-2xl">
                    🎯
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Metode & Indikator</p>
                    <p class="mt-1 text-lg font-bold text-gray-900">K-Means (5 Variabel)</p>
                    <p class="text-xs text-gray-400">Omzet, Growth, Frek, Vol, Kontribusi</p>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Periode Penjualan --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Riwayat Analisis Performa Outlet</h2>
                        <p class="text-xs text-gray-500">
                            Daftar hasil pengelompokan K-Means berdasarkan periode data penjualan
                        </p>
                    </div>
                    @if (count($riwayatAnalisis) > 0)
                        <span
                            class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                            Total: {{ count($riwayatAnalisis) }} Periode
                        </span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Nomor
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Periode Penjualan
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Jumlah Outlet
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Silhouette Score
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal Diproses
                            </th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($riwayatAnalisis as $index => $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $item->periode }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span
                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $item->jumlah_outlet ?? 0 }} Outlet
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @if (isset($item->silhouette_score) && $item->silhouette_score !== null)
                                        <span
                                            class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">
                                            {{ number_format($item->silhouette_score, 4) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <a href="{{ route('analisis-performa.show', $item->periode) }}"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-xs font-semibold text-green-700 transition hover:bg-green-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Hasil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-2xl text-green-600 ring-8 ring-green-50/50">
                                        <svg class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-base font-semibold text-gray-800">Belum Ada Riwayat Analisis</h3>
                                    <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500">
                                        Pilih periode bulan pada form di atas dan klik tombol <span
                                            class="font-medium text-gray-700">"Jalankan Analisis"</span> untuk memproses
                                        data performa outlet.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
