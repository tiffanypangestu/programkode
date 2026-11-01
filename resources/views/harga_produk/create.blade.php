@extends('layouts.app')

@section('title', 'Tambah Harga Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('harga-produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Harga Produk
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">
                    @if ($selectedProduk)
                        Tambah Harga: {{ $selectedProduk->nama_produk }}
                    @else
                        Tambah Harga Produk
                    @endif
                </h1>
                <p class="mt-2 text-sm text-gray-500">Atur harga jual produk per wilayah.</p>
            </div>

            <form action="{{ route('harga-produk.store') }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Informasi Harga</h2>
                    <p class="mt-1 text-sm text-gray-500">Kolom bertanda <span class="text-red-500">*</span> wajib diisi.
                    </p>
                </div>

                <hr class="border-gray-100">

                <div class="space-y-5 p-6">
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($selectedProduk)
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Produk</label>
                            <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">
                                {{ $selectedProduk->nama_produk }}
                            </div>
                            <input type="hidden" name="produk_id" value="{{ $selectedProduk->produk_id }}">
                        </div>
                    @else
                        <div>
                            <label for="produk_id" class="mb-2 block text-sm font-medium text-gray-700">
                                Produk <span class="text-red-500">*</span>
                            </label>
                            <select id="produk_id" name="produk_id" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
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
                    @endif

                    {{-- Bagian Pilih Wilayah --}}
                    <div>
                        <label for="wilayah_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Wilayah <span class="text-red-500">*</span>
                        </label>
                        <select id="wilayah_id" name="wilayah_id" required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach ($wilayah as $item)
                                <option value="{{ $item->wilayah_id }}" @selected(old('wilayah_id') == $item->wilayah_id)>
                                    {{ $item->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga Produk --}}
                    <div>
                        <label for="harga_produk" class="mb-2 block text-sm font-medium text-gray-700">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="harga_produk" name="harga_produk" value="{{ old('harga_produk') }}"
                            required min="0" step="100" placeholder="Contoh: 15000"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        @error('harga_produk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('harga-produk.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Simpan Harga
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
