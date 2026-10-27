@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data Transaksi
            </a>

            <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Detail Penjualan</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $transaksi->nomor_transaksi ?: 'TRX-' . $transaksi->transaksi_id }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $transaksi->tanggal ? \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d F Y') : '-' }}
                    </p>
                </div>

                <a href="{{ route('transaksi.edit', $transaksi->transaksi_id) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Transaksi
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">
                Rp{{ number_format((float) $transaksi->total_transaksi, 0, ',', '.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
                Total nilai transaksi yang tercatat.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi transaksi dan pihak terkait.
                </p>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Nomor Transaksi</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $transaksi->nomor_transaksi ?: 'TRX-' . $transaksi->transaksi_id }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksi->tanggal ? \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') : '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Outlet</p>
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $transaksi->outlet?->nama_outlet ?? '-' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $transaksi->outlet?->kode_outlet ?? '' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Wilayah</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksi->wilayah?->nama_wilayah ?? '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Karyawan</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksi->karyawan?->nama_karyawan ?? '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Tipe Transaksi</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksi->tipe_transaksi ?: '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        Rp{{ number_format((float) $transaksi->total_transaksi, 0, ',', '.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">ID Transaksi</p>
                    <p class="text-sm text-gray-500 sm:col-span-2">
                        {{ $transaksi->transaksi_id }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('transaksi.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Kembali
                </a>
                <a href="{{ route('transaksi.edit', $transaksi->transaksi_id) }}"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Transaksi
                </a>
            </div>
        </div>
    </div>
@endsection
