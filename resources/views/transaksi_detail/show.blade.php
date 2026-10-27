@extends('layouts.app')

@section('title', 'Detail Produk Transaksi')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('transaksi-detail.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Detail Transaksi
            </a>

            <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Rincian Penjualan</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $transaksiDetail->produk?->nama_produk ?? 'Detail Produk' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Transaksi:
                        {{ $transaksiDetail->transaksi?->nomor_transaksi ?: 'TRX-' . $transaksiDetail->transaksi_id }}
                    </p>
                </div>

                <a href="{{ route('transaksi-detail.edit', $transaksiDetail->transaksi_detail_id) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Detail
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Subtotal Produk</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">
                Rp{{ number_format((float) $transaksiDetail->subtotal, 0, ',', '.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ number_format($transaksiDetail->jumlah, 0, ',', '.') }} unit
                × Rp{{ number_format((float) $transaksiDetail->harga_satuan, 0, ',', '.') }}
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Detail Produk</h2>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Nomor Transaksi</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $transaksiDetail->transaksi?->nomor_transaksi ?: 'TRX-' . $transaksiDetail->transaksi_id }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Tanggal Transaksi</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksiDetail->transaksi?->tanggal ? \Illuminate\Support\Carbon::parse($transaksiDetail->transaksi->tanggal)->format('d/m/Y') : '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Produk</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $transaksiDetail->produk?->nama_produk ?? '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Referensi Harga</p>
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $transaksiDetail->hargaProduk?->wilayah?->nama_wilayah ?? '-' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Harga master:
                            Rp{{ number_format((float) ($transaksiDetail->hargaProduk?->harga_produk ?? 0), 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Jumlah</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ number_format($transaksiDetail->jumlah, 0, ',', '.') }} unit
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Harga Satuan</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        Rp{{ number_format((float) $transaksiDetail->harga_satuan, 0, ',', '.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Subtotal</p>
                    <p class="text-sm font-bold text-gray-900 sm:col-span-2">
                        Rp{{ number_format((float) $transaksiDetail->subtotal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">ID Detail</p>
                    <p class="text-sm text-gray-500 sm:col-span-2">
                        {{ $transaksiDetail->transaksi_detail_id }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('transaksi-detail.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Kembali
                </a>
                <a href="{{ route('transaksi-detail.edit', $transaksiDetail->transaksi_detail_id) }}"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Detail
                </a>
            </div>
        </div>
    </div>
@endsection
