@extends('layouts.app')

@section('title', 'Tambah Outlet')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('outlet.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data Outlet
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">Tambah Outlet</h1>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan informasi outlet baru UD Medan.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali input berikut:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('outlet.store') }}" method="POST"
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @csrf

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Outlet</h2>
                <p class="mt-1 text-sm text-gray-500">Lengkapi kolom yang diperlukan.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                <div>
                    <label for="kode_outlet" class="mb-2 block text-sm font-medium text-gray-700">
                        Kode Outlet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kode_outlet" name="kode_outlet" value="{{ old('kode_outlet') }}" required
                        maxlength="255" placeholder="Contoh: OUT001"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('kode_outlet')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_outlet" class="mb-2 block text-sm font-medium text-gray-700">
                        Nama Outlet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_outlet" name="nama_outlet" value="{{ old('nama_outlet') }}" required
                        maxlength="255" placeholder="Masukkan nama outlet"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('nama_outlet')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="wilayah_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Wilayah Distribusi <span class="text-red-500">*</span>
                    </label>
                    <select id="wilayah_id" name="wilayah_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach ($wilayah as $itemWilayah)
                            <option value="{{ $itemWilayah->wilayah_id }}" @selected(old('wilayah_id') == $itemWilayah->wilayah_id)>
                                {{ $itemWilayah->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                    @error('wilayah_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($wilayah->isEmpty())
                        <p class="mt-2 text-sm text-amber-600">
                            Data wilayah belum tersedia. Tambahkan data wilayah terlebih dahulu.
                        </p>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat_outlet" class="mb-2 block text-sm font-medium text-gray-700">
                        Alamat Outlet
                    </label>
                    <textarea id="alamat_outlet" name="alamat_outlet" rows="3" maxlength="255"
                        placeholder="Masukkan alamat lengkap outlet"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('alamat_outlet') }}</textarea>
                    @error('alamat_outlet')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipe_outlet" class="mb-2 block text-sm font-medium text-gray-700">
                        Tipe Outlet
                    </label>
                    <input type="text" id="tipe_outlet" name="tipe_outlet" value="{{ old('tipe_outlet') }}"
                        maxlength="255" placeholder="Masukkan tipe outlet"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('tipe_outlet')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_outlet" class="mb-2 block text-sm font-medium text-gray-700">
                        Status Outlet
                    </label>
                    <select id="status_outlet" name="status_outlet"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" @selected(old('status_outlet', 'aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status_outlet') === 'nonaktif')>Nonaktif</option>
                    </select>
                    @error('status_outlet')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('outlet.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    Simpan Outlet
                </button>
            </div>
        </form>
    </div>
@endsection
