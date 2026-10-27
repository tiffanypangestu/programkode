@extends('layouts.app')


@section('content')
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Kembali ke Transaksi
            </a>

            <h1 class="mt-3 text-2xl font-bold tracking-tight text-gray-900">
                Import Penjualan Historis
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                Unggah file Excel untuk memasukkan transaksi penjualan
                dan detail produk ke dalam sistem.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="mb-2 font-semibold">
                    Import belum berhasil. Periksa hal berikut:
                </p>

                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">

            <form action="{{ route('penjualan.import.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">

                @csrf

                <div>
                    <label for="file" class="mb-2 block text-sm font-semibold text-gray-700">
                        File Excel Penjualan
                    </label>

                    <input type="file" id="file" name="file" accept=".xlsx,.csv" required
                        class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-3 file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                    <p class="mt-2 text-xs text-gray-500">
                        Format yang didukung: XLSX dan CSV. Maksimal 10 MB.
                    </p>
                </div>

                {{-- Download Template Excel --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Unduh Contoh Template Excel
                    </label>
                    <a href="{{ route('penjualan.download-template') }}"
                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-green-600 shadow-sm transition hover:bg-green-50 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Template
                    </a>
                </div>

                <div>
                    <label for="karyawan_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Karyawan Penanggung Jawab
                    </label>

                    <select id="karyawan_id" name="karyawan_id" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih karyawan --</option>

                        @foreach ($karyawan as $item)
                            <option value="{{ $item->karyawan_id }}" @selected(old('karyawan_id') == $item->karyawan_id)>
                                {{ $item->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>

                    @if ($karyawan->isEmpty())
                        <p class="mt-2 text-sm text-red-600">
                            Belum ada data karyawan. Tambahkan karyawan melalui
                            menu Karyawan sebelum melakukan import.
                        </p>
                    @endif
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <h2 class="text-sm font-semibold text-gray-800">
                        Format kolom Excel
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        Gunakan nama kolom:
                        <code class="rounded bg-gray-200 px-1 py-0.5 text-xs">
                            tanggal, nomor_transaksi, kode_outlet, nama_produk,
                            jumlah, harga_satuan
                        </code>
                    </p>

                    <p class="mt-2 text-xs leading-5 text-gray-500">
                        Beberapa baris dengan nomor transaksi yang sama
                        akan digabung menjadi satu transaksi. Outlet, produk,
                        harga historis, dan karyawan harus sudah tersedia
                        sesuai referensi database.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300
                          px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit" @disabled($karyawan->isEmpty())
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                        Import Penjualan
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
