@extends('layouts.app')

@section('title', 'Tambah Detail Transaksi')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('transaksi-detail.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Detail Transaksi
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">Tambah Detail Transaksi</h1>
            <p class="mt-1 text-sm text-gray-500">
                Pilih transaksi dan produk yang akan dicatat.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksi-detail.store') }}" method="POST"
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @csrf

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Detail</h2>
                <p class="mt-1 text-sm text-gray-500">Masukkan rincian produk transaksi.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="transaksi_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select id="transaksi_id" name="transaksi_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach ($transaksi as $item)
                            <option value="{{ $item->transaksi_id }}" @selected(old('transaksi_id') == $item->transaksi_id)>
                                {{ $item->nomor_transaksi ?: 'TRX-' . $item->transaksi_id }}
                                — {{ $item->tanggal }}
                                — Rp{{ number_format((float) $item->total_transaksi, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('transaksi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="produk_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select id="produk_id" name="produk_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($produk as $item)
                            <option value="{{ $item->produk_id }}" @selected(old('produk_id') == $item->produk_id)>
                                {{ $item->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                    @error('produk_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_produk_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Referensi Harga Produk <span class="text-red-500">*</span>
                    </label>
                    <select id="harga_produk_id" name="harga_produk_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Referensi Harga --</option>
                        @foreach ($hargaProduk as $item)
                            <option value="{{ $item->harga_produk_id }}" @selected(old('harga_produk_id') == $item->harga_produk_id)>
                                {{ $item->produk?->nama_produk ?? 'Produk' }}
                                — {{ $item->wilayah?->nama_wilayah ?? 'Wilayah' }}
                                — Rp{{ number_format((float) $item->harga_produk, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('harga_produk_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Pilih referensi harga yang sesuai dengan produk dan wilayah transaksi.
                    </p>
                </div>

                <div>
                    <label for="jumlah" class="mb-2 block text-sm font-medium text-gray-700">
                        Jumlah Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                        step="1" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('jumlah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_satuan" class="mb-2 block text-sm font-medium text-gray-700">
                        Harga Satuan (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}"
                        min="0" step="0.01" required placeholder="Masukkan harga satuan"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('harga_satuan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Untuk data historis, gunakan harga yang tercatat pada transaksi aslinya.
                    </p>
                </div>
            </div>

            @if ($transaksi->isEmpty() || $produk->isEmpty() || $hargaProduk->isEmpty())
                <div class="mx-6 mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Data transaksi, produk, dan referensi harga harus tersedia terlebih dahulu.
                </div>
            @endif

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('transaksi-detail.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Simpan Detail
                </button>
            </div>
        </form>
    </div>
@endsection
