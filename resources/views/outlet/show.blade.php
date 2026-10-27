@extends('layouts.app')

@section('title', 'Detail Outlet')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('outlet.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data Outlet
            </a>

            <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Detail Master Data</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $outlet->nama_outlet }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Kode outlet: {{ $outlet->kode_outlet }}
                    </p>
                </div>

                <a href="{{ route('outlet.edit', $outlet->outlet_id) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    Edit Outlet
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                <div>
                    <h2 class="font-semibold text-gray-900">Informasi Outlet</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Data identitas dan distribusi outlet.
                    </p>
                </div>

                @if ($outlet->status_outlet === 'aktif')
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        Aktif
                    </span>
                @elseif($outlet->status_outlet === 'nonaktif')
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                        Nonaktif
                    </span>
                @else
                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                        Belum ditentukan
                    </span>
                @endif
            </div>

            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Kode Outlet</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $outlet->kode_outlet }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Nama Outlet</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $outlet->nama_outlet }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Wilayah Distribusi</p>
                    <p class="text-sm font-medium text-gray-900 sm:col-span-2">
                        {{ $outlet->wilayah?->nama_wilayah ?? 'Wilayah belum tersedia' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Alamat Outlet</p>
                    <p class="whitespace-pre-line text-sm text-gray-700 sm:col-span-2">
                        {{ $outlet->alamat_outlet ?: 'Alamat belum diisi' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Tipe Outlet</p>
                    <p class="text-sm text-gray-700 sm:col-span-2">
                        {{ $outlet->tipe_outlet ?: '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Status Outlet</p>
                    <p class="text-sm text-gray-700 sm:col-span-2">
                        {{ $outlet->status_outlet ? ucfirst($outlet->status_outlet) : 'Belum ditentukan' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">ID Outlet</p>
                    <p class="text-sm text-gray-500 sm:col-span-2">
                        {{ $outlet->outlet_id }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('outlet.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                    Kembali
                </a>
                <a href="{{ route('outlet.edit', $outlet->outlet_id) }}"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-center text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Edit Data
                </a>
            </div>
        </div>
    </div>
@endsection
