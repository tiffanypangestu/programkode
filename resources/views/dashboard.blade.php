@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            {{-- Header Dashboard --}}
            <div class="mb-8">
                <p class="text-sm font-medium text-indigo-600">Selamat Datang</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Dashboard Utama</h1>
                <p class="mt-2 text-sm text-gray-500">Ringkasan performa dan data operasional UD Medan.</p>
            </div>

            {{-- Metric Cards --}}
            <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl">
                            🏪
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Outlet</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $jumlahOutlet }}</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl">
                            📦
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Produk</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $jumlahProduk }}</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl">
                            📄
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $jumlahTransaksi }}</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl">
                            🧠
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Analisis</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $jumlahAnalisis }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Analisis Terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">Analisis Performa Terbaru</h2>
                        <p class="mt-1 text-sm text-gray-500">5 pengujian K-Means teranyar.</p>
                    </div>
                    <a href="{{ route('analisis-performa.index') }}"
                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        Lihat Semua →
                    </a>
                </div>

                @if ($new_analisis->isEmpty())
                    <div class="px-6 py-12 text-center text-sm text-gray-500">
                        Belum ada data analisis performa.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Periode</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Metode</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Cluster</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Silhouette Score</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($new_analisis as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->periode_analisis }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->metode }}</td>
                                        <td class="px-6 py-4 text-center text-sm font-semibold text-indigo-600">
                                            {{ $item->jumlah_cluster }} Cluster</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                                {{ $item->silhouette_score !== null ? number_format($item->silhouette_score, 4) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                {{ $item->status_analisis }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
