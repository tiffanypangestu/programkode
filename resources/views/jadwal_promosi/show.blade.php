@extends('layouts.app')

@section('title', 'Detail Jadwal Promosi')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('jadwal-promosi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">←
                Kembali</a>

            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Detail Jadwal Promosi</h1>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Nama Promosi</p>
                    <p class="text-lg font-bold text-indigo-600">{{ $jadwalPromosi->promosi->nama_promosi ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Tanggal Mulai</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $jadwalPromosi->tanggal_mulai }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Tanggal Selesai</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $jadwalPromosi->tanggal_selesai }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
