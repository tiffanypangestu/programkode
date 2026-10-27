@extends('layouts.app')

@section('title', 'Edit Harga Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('harga-produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Harga Produk
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Edit Harga Produk</h1>
                <p class="mt-2 text-sm text-gray-500">Perbarui nominal harga per wilayah.</p>
            </div>

            <form action="{{ route('harga-produk.update', $hargaProduk) }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Informasi Harga</h2>
                    <p class="mt-1 text-sm text-gray-500">Pastikan kombinasi produk dan wilayah sudah tepat.</p>
                </div>

                <div class="space-y-5 p-6">
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="produk_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <select id="produk_id" name="produk_id" required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->produk_id }}" @selected(old('produk_id', $hargaProduk->produk_id) == $item->produk_id)>
                                    {{ $item->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                        @error('produk_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="wilayah_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Wilayah <span class="text-red-500">*</span>
                        </label>
                        <select id="wilayah_id" name="wilayah_id" required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach ($wilayah as $item)
                                <option value="{{ $item->wilayah_id }}" @selected(old('wilayah_id', $hargaProduk->wilayah_id) == $item->wilayah_id)>
                                    {{ $item->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga" class="mb-2 block text-sm font-medium text-gray-700">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="harga" name="harga" value="{{ old('harga', $hargaProduk->harga) }}"
                            required min="0" step="100"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        @error('harga')
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
