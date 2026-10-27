@extends('layouts.app')

@section('title', 'Data Harga Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Data Master</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Data Harga Produk</h1>
                    <p class="mt-2 text-sm text-gray-500">Kelola harga produk berdasarkan wilayah penjualan.</p>
                </div>
                <a href="{{ route('harga-produk.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    <span class="text-lg">+</span> Tambah / Atur Harga
                </a>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $produk->total() }}</p>
                    <p class="mt-1 text-xs text-gray-400">Seluruh produk terdaftar</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Halaman Saat Ini</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $produk->currentPage() }}</p>
                    <p class="mt-1 text-xs text-gray-400">Dari {{ $produk->lastPage() }} halaman</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Produk Ditampilkan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $produk->count() }}</p>
                    <p class="mt-1 text-xs text-gray-400">Pada halaman ini</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Matriks Harga Produk Per Wilayah</h2>
                    <p class="mt-1 text-sm text-gray-500">Daftar produk beserta penetapan harga di masing-masing wilayah.
                    </p>
                </div>

                @if ($produk->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="text-4xl">🏷️</div>
                        <h3 class="mt-4 font-semibold text-gray-900">Belum ada data produk</h3>
                        <p class="mt-2 text-sm text-gray-500">Tambahkan produk terlebih dahulu untuk mengelola harga.</p>
                        <a href="{{ route('produk.create') }}"
                            class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                            Tambah Produk
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        No.
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nama Produk
                                    </th>

                                    {{-- Header Dinamis untuk 3 Wilayah --}}
                                    @foreach ($wilayah as $w)
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Harga: {{ $w->nama_wilayah }}
                                        </th>
                                    @endforeach

                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($produk as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $produk->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900">{{ $item->nama_produk }}</p>
                                            <span class="inline-block text-xs font-medium text-green-600">
                                                @if ($item->harga_rata_rata > 0)
                                                    Harga Rata-Rata: Rp
                                                    {{ number_format($item->harga_rata_rata, 0, ',', '.') }}
                                                @else
                                                    <span class="text-gray-500">Belum ada harga</span>
                                                @endif
                                            </span>
                                        </td>

                                        {{-- Loop Mengecek Harga Per Wilayah --}}
                                        @foreach ($wilayah as $w)
                                            @php
                                                // Mencari data harga berdasarkan wilayah_id
                                                $dataHarga = $item->hargaProduk->firstWhere(
                                                    'wilayah_id',
                                                    $w->wilayah_id,
                                                );
                                                $nominal = $dataHarga
                                                    ? $dataHarga->harga_produk ?? $dataHarga->harga_satuan
                                                    : 0;
                                            @endphp
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-center text-sm font-semibold {{ $nominal > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                                Rp {{ number_format($nominal, 0, ',', '.') }}
                                            </td>
                                        @endforeach

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('harga-produk.create', ['produk_id' => $item->produk_id]) }}"
                                                    class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">
                                                    Set / Edit Harga
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $produk->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
