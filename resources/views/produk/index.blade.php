@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Data Master</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Data Produk</h1>
                    <p class="mt-2 text-sm text-gray-500">Kelola produk yang dijual oleh UD Medan.</p>
                </div>
                <a href="{{ route('produk.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    <span class="text-lg">+</span> Tambah Produk
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
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $total_produk }}</p>
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
                    <p class="mt-1 text-xs text-gray-400">Pada halaman ini (dari {{ $produk->total() }} hasil)</p>
                </div>
            </div>

            {{-- Filter dan Search Produk --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <form action="{{ route('produk.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">
                            Cari Nama Produk
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                            placeholder="Masukkan nama produk ....">
                    </div>

                    {{-- Filter Dropdown --}}
                    <div>
                        <label for="kategori_id" class="block text-xs font-semibold text-gray-700 mb-1">
                            Kategori Produk
                        </label>
                        <select name="kategori_id" id="kategori_id"
                            class="w-full rounded-xl border border-gray-400 px-4 py-3 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori_list as $kategori)
                                <option value="{{ $kategori->kategori_id }}"
                                    {{ (string) request('kategori_id') === (string) $kategori->kategori_id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter and Reset --}}
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">&nbsp;</label>
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
                                Filter
                            </button>

                            @if (request('search') || request('kategori_id'))
                                <a href="{{ route('produk.index') }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 whitespace-nowrap">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Daftar Produk</h2>
                    <p class="mt-1 text-sm text-gray-500">Informasi produk dan kategori produk.</p>
                </div>

                @if ($produk->isEmpty())
                    <div class="px-6 py-16 text-center">
                        @if (request('search') || request('kategori_id'))
                            <div class="text-4xl">🔍</div>
                            <h3 class="mt-4 font-semibold text-gray-900">Tidak ada produk yang cocok</h3>
                            <p class="mt-2 text-sm text-gray-500">Coba ubah kata kunci pencarian atau kategori filter.</p>
                            <a href="{{ route('produk.index') }}"
                                class="mt-5 inline-flex rounded-xl bg-gray-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                                Reset Filter
                            </a>
                        @else
                            <div class="text-4xl">📦</div>
                            <h3 class="mt-4 font-semibold text-gray-900">Belum ada data produk</h3>
                            <p class="mt-2 text-sm text-gray-500">Tambahkan produk untuk mulai mengelola data penjualan.</p>
                            <a href="{{ route('produk.create') }}"
                                class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                Tambah Produk
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        No.</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nama Produk</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Deskripsi</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aksi</th>
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
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                {{ $item->kategori->nama_kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="max-w-sm px-6 py-4 text-sm text-gray-600">
                                            {{ $item->deskripsi_produk ?: '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('produk.show', $item) }}"
                                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50">Detail</a>
                                                <a href="{{ route('produk.edit', $item) }}"
                                                    class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Edit</a>
                                                <form action="{{ route('produk.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $produk->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
