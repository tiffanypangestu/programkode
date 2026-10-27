@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-indigo-600">Penjualan</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">Detail Transaksi</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola rincian produk pada setiap transaksi penjualan.
                </p>
            </div>

            <a href="{{ route('transaksi-detail.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                + Tambah Detail
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Detail</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $transaksiDetail->total() }}</p>
                <p class="mt-1 text-xs text-gray-400">Seluruh baris detail transaksi</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Detail pada Halaman Ini</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $transaksiDetail->count() }}</p>
                <p class="mt-1 text-xs text-gray-400">Baris yang sedang ditampilkan</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Daftar Produk Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">Rincian jumlah dan nilai setiap produk.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">No.</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Transaksi</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Outlet</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Karyawan</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Produk</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Jumlah</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Harga Satuan</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Subtotal</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($transaksiDetail as $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                    {{ $transaksiDetail->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 text-sm font-semibold text-indigo-600">
                                    {{ $item->nomor_transaksi ?? ($item->transaksi?->nomor_transaksi ?: 'TR-' . $item->transaksi_id) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-700">
                                    {{ $item->nama_outlet ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm font-medium text-gray-700">
                                    <div class="font-medium text-gray-900">{{ $item->nama_petugas ?? '-' }}</div>
                                    @if (isset($item->jabatan_petugas))
                                        <div class="text-xs text-gray-500">{{ $item->jabatan_petugas }}</div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm font-medium text-gray-900">
                                    {{ $item->nama_produk ?? ($item->produk?->nama_produk ?? '-') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-700">
                                    {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-700">
                                    Rp{{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900">
                                    Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('transaksi-detail.show', $item->transaksi_detail_id) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-800">
                                            Lihat
                                        </a>

                                        <a href="{{ route('transaksi-detail.edit', $item->transaksi_detail_id) }}"
                                            class="font-medium text-amber-600 hover:text-amber-800">
                                            Edit
                                        </a>

                                        <form action="{{ route('transaksi-detail.destroy', $item->transaksi_detail_id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus detail transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:text-red-800">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-14 text-center">
                                    <h3 class="font-semibold text-gray-900">Belum ada detail transaksi</h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Tambahkan produk pada transaksi yang sudah tersedia.
                                    </p>
                                    <a href="{{ route('transaksi-detail.create') }}"
                                        class="mt-4 inline-block font-semibold text-indigo-600 hover:text-indigo-800">
                                        + Tambah Detail Transaksi
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transaksiDetail->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $transaksiDetail->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
