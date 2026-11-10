@extends('layouts.app')

@section('title', 'Detail Wilayah')

@section('content')
    <div class="min-h-screen bg-gray-50/70">
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

            <a href="{{ route('wilayah.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">
                ← Kembali ke Data Wilayah
            </a>

            <div class="mb-8 mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Data Master</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                        Detail Wilayah
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Informasi lengkap wilayah distribusi UD Medan.
                    </p>
                </div>

                <a href="{{ route('wilayah.edit', $wilayah) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Edit Wilayah
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                {{ $wilayah->nama_wilayah }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                ID Wilayah: {{ $wilayah->wilayah_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:px-8">
                        <p class="text-sm font-medium text-gray-500">Nama Wilayah</p>
                        <p class="text-sm font-semibold text-gray-900 sm:col-span-2">
                            {{ $wilayah->nama_wilayah }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:px-8">
                        <p class="text-sm font-medium text-gray-500">Deskripsi Wilayah</p>
                        <p class="whitespace-pre-line text-sm leading-6 text-gray-700 sm:col-span-2">
                            {{ $wilayah->deskripsi_wilayah ?: 'Belum ada deskripsi wilayah.' }}
                        </p>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50/70 px-6 py-5 sm:flex-row sm:justify-between sm:px-8">
                    <a href="{{ route('wilayah.index') }}"
                        class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>

                    <a href="{{ route('wilayah.edit', $wilayah) }}"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit Wilayah
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
