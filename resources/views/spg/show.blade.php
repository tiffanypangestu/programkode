@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('spg.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data SPG
            </a>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Detail SDM</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ $spg->nama_spg }}</h1>
                    <p class="mt-2 text-sm text-gray-500">Informasi lengkap Sales Promotion Girl dalam sistem.</p>
                </div>
                <a href="{{ route('spg.edit', $spg) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Edit SPG
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center gap-4 border-b border-gray-100 bg-gray-50 px-6 py-6">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-2xl font-bold text-indigo-700">
                    {{ strtoupper(substr($spg->nama_spg, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $spg->nama_spg }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Sales Promotion Girl (SPG)</p>
                </div>
            </div>

            <dl class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">ID SPG</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $spg->spg_id }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Nama SPG</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">{{ $spg->nama_spg }}</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Email Akun</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">
                        {{ $spg->user->email ?? '-' }}
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Nomor Telepon</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">
                        {{ $spg->nomor_telepon ?? $spg->user->nomor_telepon ?? '-' }}
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 px-6 py-5 sm:grid-cols-3">
                    <dt class="text-sm text-gray-500">Status Akun</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:col-span-2">
                        @if ($spg->user)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                Terhubung (User ID: {{ $spg->user->user_id }})
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                Belum Terhubung
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
