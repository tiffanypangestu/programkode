@extends('layouts.app')

@section('title', 'Data Wilayah')

@section('content')
    <div class="min-h-screen bg-gray-50/70">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Data Master</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                        Data Wilayah
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Kelola wilayah distribusi penjualan UD Medan.
                    </p>
                </div>

                <a href="{{ route('wilayah.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    <span class="text-lg">+</span> Tambah Wilayah
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
                    <p class="text-sm text-gray-500">Total Wilayah</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $wilayah->total() }}</p>
                    <p class="mt-1 text-xs text-gray-400">Seluruh wilayah terdaftar</p>
                </div>
            </div>

            {{-- Filter Wilayah --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <form action="{{ route('wilayah.index') }}" method="GET"
                    class="flex flex-col gap-4 sm:items-center sm:justify-between">
                    <div class="w-full sm:max-w-md">
                        <label for="search" class="sr-only">Cari Wilayah</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Cari nama wilayah atau Deskripsi"
                                class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20">
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">Filter</button>
                            @if (request('search'))
                                <a href="{{ route('wilayah.index') }}"
                                    class="inline-flex items-center gap-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Daftar Wilayah</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi wilayah distribusi yang terdaftar dalam sistem.
                    </p>
                </div>

                @if ($wilayah->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                            📍
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">Belum ada data wilayah</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Tambahkan wilayah untuk mulai mengelola distribusi outlet.
                        </p>
                        <a href="{{ route('wilayah.create') }}"
                            class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                            Tambah Wilayah
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
                                        Nama Wilayah
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Deskripsi
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($wilayah as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $wilayah->firstItem() + $loop->index }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <p class="font-semibold text-gray-900">
                                                {{ $item->nama_wilayah }}
                                            </p>
                                        </td>

                                        <td class="max-w-md px-6 py-4 text-sm text-gray-600">
                                            {{ $item->deskripsi_wilayah ?: '-' }}
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('wilayah.show', $item) }}"
                                                    class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                                    Detail
                                                </a>

                                                <a href="{{ route('wilayah.edit', $item) }}"
                                                    class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">
                                                    Edit
                                                </a>

                                                <form action="{{ route('wilayah.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus wilayah ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $wilayah->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
