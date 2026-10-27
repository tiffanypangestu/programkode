@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('kategori.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Kategori
            </a>

            <div class="mb-6 mt-5">
                <p class="text-sm font-medium text-indigo-600">Data Master</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">Detail Kategori</h1>
                <p class="mt-2 text-sm text-gray-500">Informasi lengkap kategori terdaftar.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-gray-100 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        🏷️
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $kategori->nama_kategori }}</h2>
                        <p class="mt-1 text-sm text-gray-500">Kategori Produk</p>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100">
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">ID Kategori</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $kategori->kategori_id }}</dd>
                    </div>
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Nama Kategori</dt>
                        <dd class="text-sm font-semibold text-gray-900 sm:col-span-2">{{ $kategori->nama_kategori }}</dd>
                    </div>
                </dl>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('kategori.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ route('kategori.edit', $kategori) }}"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
