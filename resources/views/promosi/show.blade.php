@extends('layouts.app')

@section('title', 'Detail Promosi')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('promosi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Promosi
            </a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">{{ $promosi->nama_promosi }}</h1>
                <p class="mt-2 text-sm text-gray-500">Detail rincian program promosi.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Jenis Promosi</p>
                    <p class="text-base font-medium text-gray-900">{{ $promosi->jenis_promosi ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Nilai Diskon / Potongan</p>
                    <p class="text-lg font-bold text-emerald-600">
                        {{ $promosi->diskon ? $promosi->diskon . '%' : 'Rp ' . number_format($promosi->potongan_harga ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Deskripsi</p>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $promosi->deskripsi ?: 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
