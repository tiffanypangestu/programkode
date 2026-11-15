@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-indigo-600">Penjualan</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">Detail Transaksi</h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('penjualan.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm transition">
                    + Tambah Penjualan
                </a>
            </div>
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
                <p class="text-md text-gray-700">Total Transaksi</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $transaksi->total() }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Daftar Transaksi Penjualan</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">No.</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Transaksi</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Outlet</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Karyawan</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500 min-w-[280px]">
                                Rincian Produk</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Total Item</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Total Transaksi
                            </th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($transaksi as $item)
                            <tr class="transition hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                    {{ $transaksi->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm">
                                    <div class="font-bold text-indigo-600">
                                        {{ $item->nomor_transaksi ?? 'TR-' . $item->transaksi_id }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm">
                                    <div class="font-semibold text-gray-800">
                                        {{ $item->outlet->nama_outlet ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->wilayah->nama_wilayah ?? ($item->outlet->wilayah->nama_wilayah ?? '') }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ $item->karyawan->nama_karyawan ?? ($item->karyawan->user->name ?? '-') }}
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 mt-0.5">
                                        {{ $item->karyawan->role->nama_role ?? ($item->karyawan->jabatan ?? 'Karyawan') }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    @if ($firstDetail = $item->transaksiDetail->first())
                                        <div class="space-y-1.5 max-w-sm">
                                            {{-- Hanya tampilkan 1 produk utama --}}
                                            <div
                                                class="flex items-center justify-between gap-3 text-xs bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-200">
                                                <div class="font-medium text-gray-900 truncate"
                                                    title="{{ $firstDetail->produk->nama_produk ?? 'ID: ' . $firstDetail->produk_id }}">
                                                    {{ $firstDetail->produk->nama_produk ?? 'ID: ' . $firstDetail->produk_id }}
                                                </div>
                                                <div class="text-gray-500 shrink-0 font-medium text-right">
                                                    <span
                                                        class="font-bold text-gray-900">{{ $firstDetail->jumlah }}x</span>
                                                    <span class="text-gray-400 text-[10px]">@</span>
                                                    <span>Rp{{ number_format((float) $firstDetail->harga_satuan, 0, ',', '.') }}</span>
                                                </div>
                                            </div>

                                            {{-- Keterangan produk lainnya jika lebih dari 1 produk --}}
                                            @if ($item->transaksiDetail->count() > 1)
                                                @php
                                                    $sisaCount = $item->transaksiDetail->count() - 1;
                                                    $sisaProduk = $item->transaksiDetail->slice(1);
                                                @endphp
                                                <details class="group">
                                                    <summary
                                                        class="list-none [&::-webkit-details-marker]:hidden cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-800 select-none inline-flex items-center gap-1">
                                                        <span>... {{ $sisaCount }} produk lainnya</span>
                                                        <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-indigo-500"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </summary>
                                                    <div class="mt-1.5 space-y-1 pt-1 border-t border-gray-100">
                                                        @foreach ($sisaProduk as $detail)
                                                            <div
                                                                class="flex items-center justify-between gap-3 text-xs bg-gray-50/70 rounded-lg px-2.5 py-1 border border-gray-100">
                                                                <div class="font-medium text-gray-700 truncate"
                                                                    title="{{ $detail->produk->nama_produk ?? 'ID: ' . $detail->produk_id }}">
                                                                    {{ $detail->produk->nama_produk ?? 'ID: ' . $detail->produk_id }}
                                                                </div>
                                                                <div class="text-gray-500 shrink-0 text-right">
                                                                    <span
                                                                        class="font-semibold text-gray-800">{{ $detail->jumlah }}x</span>
                                                                    <span class="text-gray-400 text-[10px]">@</span>
                                                                    <span>Rp{{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Tidak ada produk</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                    <span class="text-sm font-bold text-gray-900">
                                        {{ number_format($item->transaksiDetail->sum('jumlah'), 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-400 block">unit</span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                    <span class="text-sm font-bold text-emerald-600">
                                        Rp{{ number_format((float) ($item->total_transaksi ?: $item->transaksiDetail->sum('subtotal')), 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('transaksi-detail.show', $item->transaksi_id) }}"
                                            class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                            Lihat
                                        </a>

                                        @php
                                            $penjualanId = str_replace('TR-', '', $item->nomor_transaksi);
                                            $isPenjualan =
                                                str_starts_with($item->nomor_transaksi, 'TR-') &&
                                                is_numeric($penjualanId);
                                        @endphp

                                        @if ($isPenjualan)
                                            <a href="{{ route('penjualan.edit', $penjualanId) }}"
                                                class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition">
                                                Edit
                                            </a>
                                        @else
                                            <a href="{{ route('transaksi.edit', $item->transaksi_id) }}"
                                                class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition">
                                                Edit
                                            </a>
                                        @endif

                                        <form action="{{ route('transaksi-detail.destroy', $item->transaksi_id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus seluruh transaksi {{ $item->nomor_transaksi }} ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl mb-3">
                                        📦
                                    </div>
                                    <h3 class="font-semibold text-gray-900">Belum ada transaksi penjualan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Silakan input transaksi baru untuk melihat daftar di sini.
                                    </p>
                                    <a href="{{ route('penjualan.create') }}"
                                        class="mt-4 inline-block rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                        + Input Penjualan Baru
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transaksi->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $transaksi->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
