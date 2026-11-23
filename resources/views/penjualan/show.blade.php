@extends('layouts.app')

@section('title', in_array(strtolower(auth()->user()->role->nama_role ?? ''), ['admin']) ? 'Detail Faktur Penjualan' : 'Detail Transaksi Penjualan')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <a href="{{ route('penjualan.index') }}" class="text-sm font-medium text-gray-500 hover:text-emerald-600">
                ← Kembali ke {{ in_array(strtolower(auth()->user()->role->nama_role ?? ''), ['admin']) ? 'Data Faktur Penjualan' : 'Riwayat Transaksi' }}
            </a>

            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-6">
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ in_array(strtolower(auth()->user()->role->nama_role ?? ''), ['admin']) ? 'Faktur Penjualan' : 'Bukti Transaksi Penjualan' }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">Tanggal: {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d-m-Y') }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ $penjualan->status_penjualan ?? 'Selesai' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Outlet</p>
                        <p class="font-bold text-gray-900">{{ $penjualan->outlet->nama_outlet ?? '-' }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">
                            {{ $penjualan->outlet->alamat_outlet ?? 'Alamat tidak dicantumkan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Petugas Lapangan / PIC</p>
                        @if ($penjualan->bcKaryawan || $penjualan->spgKaryawan)
                            <div class="mt-1 space-y-1.5">
                                @if ($penjualan->bcKaryawan)
                                    <div class="flex items-center gap-2">
                                        <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800">BC</span>
                                        <span class="font-medium text-gray-800">{{ $penjualan->bcKaryawan->nama_karyawan }}</span>
                                        <span class="text-xs text-gray-500">({{ $penjualan->bcKaryawan->user->email ?? '-' }})</span>
                                    </div>
                                @endif
                                @if ($penjualan->spgKaryawan)
                                    <div class="flex items-center gap-2">
                                        <span class="rounded bg-pink-100 px-2 py-0.5 text-xs font-bold text-pink-800">SPG</span>
                                        <span class="font-medium text-gray-800">{{ $penjualan->spgKaryawan->nama_karyawan }}</span>
                                        <span class="text-xs text-gray-500">({{ $penjualan->spgKaryawan->user->email ?? '-' }})</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="font-medium text-gray-800">{{ $penjualan->karyawan->nama_karyawan ?? '-' }}</p>
                        @endif
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Item Produk</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Nama Produk</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-500">Jumlah</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-500">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-500">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($transaksiDetails as $detail)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $detail->nama_produk ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $detail->jumlah }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">Rp
                                            {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">Rp
                                            {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-500">Total Keseluruhan</span>
                    <span class="text-2xl font-bold text-emerald-600">Rp
                        {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
