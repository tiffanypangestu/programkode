@extends('layouts.app')

@section('title', 'Edit Promosi')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('promosi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Promosi
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Edit Promosi</h1>
                <p class="mt-2 text-sm text-gray-500">Perbarui rincian program promosi.</p>
            </div>

            <form action="{{ route('promosi.update', $promosi) }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Informasi Promosi</h2>
                </div>

                <div class="space-y-5 p-6">
                    <div>
                        <label for="nama_promosi" class="mb-2 block text-sm font-medium text-gray-700">Nama Promosi <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama_promosi" name="nama_promosi"
                            value="{{ old('nama_promosi', $promosi->nama_promosi) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="jenis_promosi" class="mb-2 block text-sm font-medium text-gray-700">Jenis
                            Promosi</label>
                        <input type="text" id="jenis_promosi" name="jenis_promosi"
                            value="{{ old('jenis_promosi', $promosi->jenis_promosi) }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="diskon" class="mb-2 block text-sm font-medium text-gray-700">Diskon (%)</label>
                            <input type="number" step="0.01" id="diskon" name="diskon"
                                value="{{ old('diskon', $promosi->diskon) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label for="potongan_harga" class="mb-2 block text-sm font-medium text-gray-700">Potongan Harga
                                (Rp)</label>
                            <input type="number" id="potongan_harga" name="potongan_harga"
                                value="{{ old('potongan_harga', $promosi->potongan_harga) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="mb-2 block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">{{ old('deskripsi', $promosi->deskripsi) }}</textarea>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('promosi.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
