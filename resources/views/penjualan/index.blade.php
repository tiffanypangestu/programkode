@extends('layouts.app')

@section('title', 'Tambah Data Penjualan')
@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="mt-1 text-2xl font-bold tracking-light text-gray-900">
                        Data Penjualan
                    </h1>

                    <p class="mt-2 text-sm text-gray-500">
                        Tempat Mengelola Data penjualan UD Medan
                    </p>
                </div>

                <div class="flex-wrap gap-2">
                    <a href="{{ route('penjualan.import.form') }}"
                        class="inline-flex items-center justify-between gap-2.5 rounded-xl bg-red-200 px-6 py-3 text-sm font-semibold text-black hover:bg-red-600 hover:text-white shadow-md transition-all duration-150 active:scale-95">
                        <span>
                            <svg class="h-5 w-5 fill-current transition-colors duration-150 hover:text-white"
                                viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="m807.186 686.592 272.864 272.864H0v112.94h1080.05l-272.864 272.978 79.736 79.849 409.296-409.183-409.296-409.184-79.736 79.736ZM1870.419 434.69l-329.221-329.11C1509.688 74.07 1465.979 56 1421.48 56H451.773v730.612h112.94V168.941h790.584v451.762h451.762v1129.405H564.714v-508.233h-112.94v621.173H1920V554.52c0-45.176-17.619-87.754-49.58-119.83Zm-402.181-242.37 315.443 315.442h-315.443V192.319Z"
                                    fill-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Import Excel</span>
                    </a>

                    <a href="{{ route('penjualan.create') }}"
                        class="inline-flex items-center justify-between gap-2.5 rounded-xl bg-blue-200 px-6 py-3 text-sm font-semibold text-black shadow:md hover:bg-blue-600 hover:text-white">
                        <span class="text-lg">
                            <svg class="h-5 w-5 fill-current transition-colors hover:text-white" viewBox="0 0 1920 1920"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="m807.186 686.592 272.864 272.864H0v112.94h1080.05l-272.864 272.978 79.736 79.849 409.296-409.183-409.296-409.184-79.736 79.736ZM1870.419 434.69l-329.221-329.11C1509.688 74.07 1465.979 56 1421.48 56H451.773v730.612h112.94V168.941h790.584v451.762h451.762v1129.405H564.714v-508.233h-112.94v621.173H1920V554.52c0-45.176-17.619-87.754-49.58-119.83Zm-402.181-242.37 315.443 315.442h-315.443V192.319Z"
                                    fill-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Tambah Penjualan</span>
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-50 bg-green-100 px-5 py-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-md">
                @if ($penjualan->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <h3 class="mt-4 font-semibold text-gray-900">
                            Belum ada Data Penjualan
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Klik tombol Tambah Penjualan
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Nomor
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Tanggal
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Outlet
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Produk
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Total Harga
                                    </th>

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide divide-gray-200">
                                @foreach ($penjualan as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $penjualan->firstItem() + $loop->index }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                            {{ $item->outlet->nama_outlet ?? '-' }}</td>

                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                            @if ($item->transaksiDetail && $item->transaksiDetail->isNotEmpty())
                                                <span class="font-semibold text-gray-900">
                                                    {{ $item->transaksiDetail->first()->produk->nama_produk ?? 'Produk' }}
                                                </span>
                                                @if ($item->transaksiDetail->count() > 1)
                                                    <span class="text-xs text-gray-500 block">
                                                        +{{ $item->transaksiDetail->count() - 1 }} produk lainnya
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>

                                        <td class="whitespace-nowrap-px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
                                                    <a href="{{ route('penjualan.pdf', $item->penjualan_id) }}"
                                                        target="_blank" title="Cetak Faktur PDF"
                                                        class="rounded-lg border border-green-200 px-3 ml-2 py-1.5 text-xs font-semibold text-green-600 hover:bg-green-50">
                                                        Cetak PDF
                                                    </a>
                                                @endif
                                                <a href="{{ route('penjualan.show', $item) }}"
                                                    class="rounded-lg border border-gray-200 px-3 py-1.5 ml-2 text-xs font-semibold text-gray-600 hover:bg-gray-50">Detail</a>
                                                <a href="{{ route('penjualan.edit', $item) }}"
                                                    class="rounded-lg border border-indigo-200 px-3 py-1.5 ml-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Edit</a>
                                                <form action="{{ route('penjualan.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Hapus faktur penjualan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 ml-2 hover:bg-red-50">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-100 px-5 py-4">
                        {{ $penjualan->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
