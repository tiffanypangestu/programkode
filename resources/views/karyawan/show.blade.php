@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('karyawan.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data Karyawan
            </a>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Detail SDM</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ $karyawan->nama_karyawan }}</h1>
                    <p class="mt-2 text-sm text-gray-500">Informasi lengkap karyawan yang terdaftar dalam sistem.</p>
                </div>
                <a href="{{ route('karyawan.edit', $karyawan) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit Karyawan
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center gap-4 border-b border-gray-100 bg-gray-50 px-6 py-6">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-2xl font-bold text-indigo-700">
                    {{ strtoupper(substr($karyawan->nama_karyawan, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $karyawan->nama_karyawan }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $karyawan->jabatan ?: 'Jabatan belum diisi' }}</p>
                </div>
            </div>

            <dl class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">ID Karyawan</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->karyawan_id }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Nama Karyawan</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->nama_karyawan }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Role</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->role->nama_role ?? '-' }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Akun Pengguna</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">
                        {{ $karyawan->user->name ?? 'Tidak terhubung' }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Jabatan</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->jabatan ?: '-' }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Nomor Telepon</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->nomor_telepon ?: '-' }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Tahun Masuk</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $karyawan->tahun_masuk ?: '-' }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Outlet Penugasan</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">
                        @if ($karyawan->outlets->isEmpty())
                            <span class="text-gray-400 italic">Belum ada outlet yang ditugaskan</span>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($karyawan->outlets as $o)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200">
                                        {{ $o->nama_outlet }}
                                        <span class="ml-1 text-indigo-400">({{ $o->wilayah->nama_wilayah ?? '' }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </dd>
                </div>
            </dl>

            <div class="border-t border-gray-100 bg-gray-50 px-6 py-5">
                <a href="{{ route('karyawan.index') }}"
                    class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
@endsection
