@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Produk
            </a>

            <div class="mb-6 mt-5">
                <p class="text-sm font-medium text-indigo-600">Data Master</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">Detail Produk</h1>
                <p class="mt-2 text-sm text-gray-500">Informasi lengkap produk yang terdaftar.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-gray-100 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $produk->nama_produk }}</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $produk->kategori->nama_kategori ?? 'Kategori belum ditentukan' }}
                        </p>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100">
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">ID Produk</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $produk->produk_id }}</dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Nama Produk</dt>
                        <dd class="text-sm font-semibold text-gray-900 sm:col-span-2">{{ $produk->nama_produk }}</dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                        <dd class="sm:col-span-2">
                            <span
                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                {{ $produk->kategori->nama_kategori ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="whitespace-pre-line text-sm text-gray-700 sm:col-span-2">
                            {{ $produk->deskripsi_produk ?: 'Tidak ada deskripsi.' }}
                        </dd>
                    </div>
                </dl>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('produk.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ route('produk.edit', $produk) }}"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit Produk
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
