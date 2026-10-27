@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header halaman --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Manajemen SDM</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                    Data Karyawan
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Kelola data karyawan dan role dalam sistem UD Medan.
                </p>
            </div>

            <a href="{{ route('karyawan.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <span class="text-lg">+</span>
                Tambah Karyawan
            </a>
        </div>

        {{-- Notifikasi --}}
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

        {{-- Statistik --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Karyawan</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $karyawan->total() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Seluruh data karyawan</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Halaman Saat Ini</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $karyawan->currentPage() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    Dari {{ $karyawan->lastPage() }} halaman
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Data Ditampilkan</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $karyawan->count() }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Pada halaman ini</p>
            </div>
        </div>

        {{-- Tabel karyawan --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Daftar Karyawan</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi karyawan yang terdaftar di sistem.
                </p>
            </div>

            @if ($karyawan->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        👥
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">
                        Belum ada data karyawan
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Tambahkan karyawan untuk mulai mengelola data SDM.
                    </p>
                    <a href="{{ route('karyawan.create') }}"
                        class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Tambah Karyawan
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
                                    Karyawan
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Role
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Akun Pengguna
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($karyawan as $item)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $karyawan->firstItem() + $loop->index }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="font-semibold text-gray-900">
                                            {{ $item->nama_karyawan }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $item->nomor_telepon ?: 'Telepon belum diisi' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                            {{ $item->role->nama_role ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $item->user->name ?? 'Tidak terhubung' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('karyawan.show', $item) }}"
                                                class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                                Detail
                                            </a>

                                            <a href="{{ route('karyawan.edit', $item) }}"
                                                class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">
                                                Edit
                                            </a>

                                            <form action="{{ route('karyawan.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
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
                    {{ $karyawan->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
