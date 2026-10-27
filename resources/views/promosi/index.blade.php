@extends('layouts.app')

@section('title', 'Data Promosi')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Pemasaran</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Data Promosi</h1>
                    <p class="mt-2 text-sm text-gray-500">Kelola daftar program promosi dan diskon.</p>
                </div>
                <a href="{{ route('promosi.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    <span class="text-lg">+</span> Tambah Promosi
                </a>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Daftar Promosi</h2>
                    <p class="mt-1 text-sm text-gray-500">Program diskon dan penawaran aktif maupun mendatang.</p>
                </div>

                @if ($promosi->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="text-4xl">🎁</div>
                        <h3 class="mt-4 font-semibold text-gray-900">Belum ada data promosi</h3>
                        <p class="mt-2 text-sm text-gray-500">Buat program promosi baru untuk meningkatkan penjualan.</p>
                        <a href="{{ route('promosi.create') }}"
                            class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                            Tambah Promosi
                        </a>
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
                                        Nama Promosi</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Jenis Diskon</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nilai</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($promosi as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $promosi->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-900">
                                            {{ $item->nama_promosi }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ $item->jenis_promosi ?? 'Diskon' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-emerald-600">
                                            {{ $item->diskon ? $item->diskon . '%' : 'Rp ' . number_format($item->potongan_harga ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('promosi.show', $item) }}"
                                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50">Detail</a>
                                                <a href="{{ route('promosi.edit', $item) }}"
                                                    class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Edit</a>
                                                <form action="{{ route('promosi.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus promosi ini?')">
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
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $promosi->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
