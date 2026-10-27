@extends('layouts.app')

@section('title', 'Detail Harga Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('harga-produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Harga Produk
            </a>

            <div class="mb-6 mt-5">
                <p class="text-sm font-medium text-indigo-600">Data Master</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">Detail Harga Produk</h1>
                <p class="mt-2 text-sm text-gray-500">Rincian penetapan harga untuk produk tertentu.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-gray-100 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        💵
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $hargaProduk->produk->nama_produk ?? '-' }}</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Wilayah: {{ $hargaProduk->wilayah->nama_wilayah ?? '-' }}
                        </p>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100">
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Nama Produk</dt>
                        <dd class="text-sm font-semibold text-gray-900 sm:col-span-2">
                            {{ $hargaProduk->produk->nama_produk ?? '-' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Wilayah</dt>
                        <dd class="sm:col-span-2">
                            <span
                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                {{ $hargaProduk->wilayah->nama_wilayah ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
                        <dd class="text-lg font-bold text-emerald-600 sm:col-span-2">
                            Rp {{ number_format($hargaProduk->harga, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('harga-produk.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ route('harga-produk.edit', $hargaProduk) }}"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit Harga
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
