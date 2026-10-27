@extends('layouts.app')

@section('title', 'Tambah Wilayah')

@section('content')
    <div class="min-h-screen bg-gray-50/70">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

            <a href="{{ route('wilayah.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Wilayah
            </a>

            <div class="mb-8 mt-6">
                <p class="text-sm font-medium text-indigo-600">Data Master</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                    Tambah Wilayah
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Lengkapi informasi wilayah distribusi UD Medan.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('wilayah.store') }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
                    <h2 class="font-semibold text-gray-900">Informasi Wilayah</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Kolom bertanda <span class="text-red-500">*</span> wajib diisi.
                    </p>
                </div>

                <div class="space-y-6 px-6 py-6 sm:px-8">
                    <div>
                        <label for="nama_wilayah" class="mb-2 block text-sm font-medium text-gray-700">
                            Nama Wilayah <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_wilayah" name="nama_wilayah" value="{{ old('nama_wilayah') }}"
                            required maxlength="255" placeholder="Contoh: Dalam Kota"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        @error('nama_wilayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi_wilayah" class="mb-2 block text-sm font-medium text-gray-700">
                            Deskripsi Wilayah
                        </label>
                        <textarea id="deskripsi_wilayah" name="deskripsi_wilayah" rows="4"
                            placeholder="Jelaskan cakupan wilayah distribusi..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('deskripsi_wilayah') }}</textarea>
                        @error('deskripsi_wilayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50/70 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
                    <a href="{{ route('wilayah.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Simpan Wilayah
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
