@extends('layouts.app')

@section('title', 'Detail Transaksi ' . ($transaksi->nomor_transaksi ?? ''))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('transaksi-detail.index') }}"
                class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
                &larr; Kembali ke Detail Transaksi
            </a>

            <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Rincian Faktur Penjualan</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $transaksi->nomor_transaksi ?: 'TRX-' . $transaksi->transaksi_id }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Tanggal:
                        {{ $transaksi->tanggal ? \Carbon\Carbon::parse($transaksi->tanggal)->format('d F Y') : '-' }}
                    </p>
                </div>

                @php
                    $penjualanId = str_replace('TR-', '', $transaksi->nomor_transaksi);
                    $isPenjualan = str_starts_with($transaksi->nomor_transaksi, 'TR-') && is_numeric($penjualanId);
                @endphp

                @if ($isPenjualan)
                    <a href="{{ route('penjualan.edit', $penjualanId) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 shadow-sm transition">
                        Edit Faktur
                    </a>
                @else
                    <a href="{{ route('transaksi.edit', $transaksi->transaksi_id) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 shadow-sm transition">
                        Edit Transaksi
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">
                    Rp{{ number_format((float) ($transaksi->total_transaksi ?: $transaksi->transaksiDetail->sum('subtotal')), 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $transaksi->transaksiDetail->count() }}
                </p>
            </div>
        </div>

        {{-- Info Outlet & Karyawan --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Transaksi</h2>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Outlet</p>
                    <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                        {{ $transaksi->outlet->nama_outlet ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-500">Alamat Outlet</p>
                    <p class="text-md text-gray-600 font-normal">({{ $transaksi->outlet->alamat_outlet ?? '-' }})</p>
                </div>

                <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                    <p class="text-sm text-gray-500">Wilayah</p>
                    <p class="text-sm text-gray-900 sm:col-span-2">
                        {{ $transaksi->wilayah->nama_wilayah ?? ($transaksi->outlet->wilayah->nama_wilayah ?? '-') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tabel Rincian Semua Produk yang Dibeli --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900">Rincian Item Produk</h2>
                </div>
                <span
                    class="inline-flex rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    {{ $transaksi->transaksiDetail->count() }} Item Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">No.</th>
                            <th class="px-5 py-3 text-left font-semibold">Nama Produk</th>
                            <th class="px-5 py-3 text-right font-semibold">Jumlah</th>
                            <th class="px-5 py-3 text-right font-semibold">Harga Satuan</th>
                            <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($transaksi->transaksiDetail as $idx => $detail)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-5 py-3 text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $detail->produk->nama_produk ?? 'ID: ' . $detail->produk_id }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                    {{ $detail->jumlah }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-600">
                                    Rp{{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right font-bold text-emerald-600">
                                    Rp{{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="px-5 py-3 font-bold text-gray-900 uppercase text-xs">Total</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900">
                                {{ number_format($transaksi->transaksiDetail->sum('jumlah'), 0, ',', '.') }} unit
                            </td>
                            <td></td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600 text-base">
                                Rp{{ number_format((float) ($transaksi->total_transaksi ?: $transaksi->transaksiDetail->sum('subtotal')), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
