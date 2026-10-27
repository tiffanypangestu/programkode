@extends('layouts.app')

@section('title', 'Data Outlet')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-indigo-600">Master Data</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">Data Outlet</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data outlet dan wilayah distribusi UD Medan.
                </p>
            </div>

            <a href="{{ route('outlet.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                + Tambah Outlet
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
                <p class="font-semibold">Terjadi kesalahan:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Outlet</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $outlet->total() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Seluruh data outlet</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Halaman Saat Ini</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">
                    {{ $outlet->count() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Data pada halaman ini</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Jumlah Halaman</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $outlet->lastPage() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Berdasarkan pagination</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-gray-900">Daftar Outlet</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi outlet berdasarkan kode dan wilayah distribusi.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No.
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Kode Outlet</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Nama Outlet</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Wilayah</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($outlet as $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $outlet->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-indigo-600">
                                    {{ $item->kode_outlet }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $item->nama_outlet }}
                                    <p class="mt-1 max-w-xs truncate text-xs font-normal text-gray-500">
                                        {{ $item->alamat_outlet ?: 'Alamat belum diisi' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $item->wilayah?->nama_wilayah ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $item->tipe_outlet ?: '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($item->status_outlet === 'aktif')
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Aktif
                                        </span>
                                    @elseif($item->status_outlet === 'nonaktif')
                                        <span
                                            class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                            Nonaktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('outlet.show', $item->outlet_id) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-800">
                                            Lihat
                                        </a>

                                        <a href="{{ route('outlet.edit', $item->outlet_id) }}"
                                            class="font-medium text-amber-600 hover:text-amber-800">
                                            Edit
                                        </a>

                                        <form action="{{ route('outlet.destroy', $item->outlet_id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus outlet ini?')">
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
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">
                                            <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 font-semibold text-gray-900">Belum ada data outlet</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Tambahkan outlet untuk mulai mengelola data distribusi.
                                        </p>
                                        <a href="{{ route('outlet.create') }}"
                                            class="mt-4 font-semibold text-indigo-600 hover:text-indigo-800">
                                            + Tambah outlet pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($outlet->hasPages())
                <div class="border-t border-gray-200 px-5 py-4 sm:px-6">
                    {{ $outlet->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
