@extends('layouts.app')

@section('title', 'Set Harga Produk')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('harga-produk.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Harga Produk
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Edit Harga: {{ $produk->nama_produk }}</h1>
                <p class="mt-2 text-sm text-gray-500">Perbarui nominal harga untuk setiap wilayah.</p>
            </div>

            <form action="{{ route('harga-produk.save-set', $produk) }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-gray-100 px-6 py-5">
                    <h2 class="font-semibold text-gray-900">Harga Per Wilayah</h2>
                    <p class="mt-1 text-sm text-gray-500">Pastikan nominal harga setiap wilayah sudah tepat.</p>
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

                    {{-- Produk (readonly) --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Produk</label>
                        <div
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">
                            {{ $produk->nama_produk }}
                        </div>
                    </div>

                    {{-- Input harga per wilayah --}}
                    @foreach ($wilayah as $w)
                        @php
                            $existing = $hargaByWilayah[$w->wilayah_id] ?? null;
                            $currentHarga = $existing ? (int) $existing->harga_produk : '';
                            $fieldKey = "harga.{$w->wilayah_id}";
                        @endphp
                        <div>
                            <label for="harga_{{ $w->wilayah_id }}" class="mb-2 block text-sm font-medium text-gray-700">
                                Harga {{ $w->nama_wilayah }} (Rp)
                                <span class="text-red-500">*</span>
                                @if ($existing)
                                    <span class="ml-1 text-xs font-normal text-green-600">✓ Sudah ada</span>
                                @else
                                    <span class="ml-1 text-xs font-normal text-amber-500">Belum diset</span>
                                @endif
                            </label>
                            <input type="number" id="harga_{{ $w->wilayah_id }}" name="harga[{{ $w->wilayah_id }}]"
                                value="{{ old($fieldKey, $currentHarga) }}" min="0" step="100"
                                placeholder="Masukkan harga..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            @error("harga.{$w->wilayah_id}")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div
                    class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row">
                    <a href="{{ route('harga-produk.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Simpan Semua Harga
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
