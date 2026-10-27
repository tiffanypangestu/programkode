@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('kategori.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Kategori
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Edit Kategori</h1>
                <p class="mt-2 text-sm text-gray-500">Perbarui informasi kategori produk.</p>
            </div>

            <form action="{{ route('kategori.update', $kategori) }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Informasi Kategori</h2>
                    <p class="mt-1 text-sm text-gray-500">Pastikan nama kategori sudah benar.</p>
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
                        <label for="nama_kategori" class="mb-2 block text-sm font-medium text-gray-700">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_kategori" name="nama_kategori"
                            value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required maxlength="255"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        @error('nama_kategori')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('kategori.index') }}"
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
