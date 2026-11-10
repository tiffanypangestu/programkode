@extends('layouts.app')

@section('title', 'Data Transaksi')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-indigo-600">Penjualan</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">Data Transaksi</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola transaksi penjualan historis UD Medan.
                </p>
            </div>

            <a href="{{ route('transaksi.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                + Tambah Transaksi
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('duplicates') && count(session('duplicates')) > 0)
            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-amber-900">
                            {{ count(session('duplicates')) }} Transaksi Duplikat Dilewati (Sudah Ada di Database)
                        </h3>
                        <p class="mt-1 text-xs text-amber-700">
                            Nomor transaksi berikut sudah terdaftar di sistem sehingga dilewati dan tidak diimport ulang:
                        </p>
                        <div class="mt-3 max-h-48 overflow-y-auto rounded-xl border border-amber-200 bg-white p-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach (session('duplicates') as $dup)
                                    @php
                                        $nomorTrx = is_array($dup) ? ($dup['nomor_transaksi'] ?? '-') : $dup;
                                        $totalBaris = is_array($dup) ? ($dup['total_baris'] ?? null) : null;
                                        $tgl = is_array($dup) ? ($dup['tanggal'] ?? null) : null;
                                        $outlet = is_array($dup) ? ($dup['kode_outlet'] ?? null) : null;
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-mono font-medium text-amber-800" title="{{ $outlet ? "Outlet: $outlet | Tgl: $tgl" : '' }}">
                                        <span>{{ $nomorTrx }}</span>
                                        @if($totalBaris)
                                            <span class="text-[10px] text-amber-600 font-sans">({{ $totalBaris }} baris)</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
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
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $transaksi->total() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Seluruh data transaksi</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Data pada Halaman Ini</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">
                    {{ $transaksi->count() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Jumlah baris yang ditampilkan</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Riwayat Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Daftar transaksi berdasarkan tanggal terbaru.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">No.</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Nomor Transaksi
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Outlet</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Wilayah</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500">Karyawan</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Total</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($transaksi as $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                    {{ $transaksi->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-indigo-600">
                                    {{ $item->nomor_transaksi ?: 'TRX-' . $item->transaksi_id }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                    {{ $item->tanggal ? \Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm font-medium text-gray-900">
                                    {{ $item->outlet?->nama_outlet ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                    {{ $item->wilayah?->nama_wilayah ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $item->karyawan?->nama_karyawan ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900">
                                    Rp{{ number_format((float) $item->total_transaksi, 0, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('transaksi.show', $item->transaksi_id) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-800">
                                            Lihat
                                        </a>

                                        <a href="{{ route('transaksi.edit', $item->transaksi_id) }}"
                                            class="font-medium text-amber-600 hover:text-amber-800">
                                            Edit
                                        </a>

                                        <form action="{{ route('transaksi.destroy', $item->transaksi_id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Detail transaksi terkait juga dapat terhapus.')">
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
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <h3 class="font-semibold text-gray-900">Belum ada transaksi</h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Tambahkan transaksi atau import data historis setelah data master tersedia.
                                    </p>
                                    <a href="{{ route('transaksi.create') }}"
                                        class="mt-4 inline-block font-semibold text-indigo-600 hover:text-indigo-800">
                                        + Tambah transaksi pertama
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
