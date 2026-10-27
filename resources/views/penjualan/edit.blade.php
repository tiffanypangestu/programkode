@extends('layouts.app')

@section('title', 'Edit Penjualan')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <a href="{{ route('penjualan.index') }}" class="text-sm font-medium text-gray-500 hover:text-emerald-600">←
                Kembali ke Data Penjualan</a>

            <div class="mb-6 mt-4">
                <h1 class="text-3xl font-bold text-gray-900">Edit Faktur Penjualan</h1>
            </div>

            <form action="{{ route('penjualan.update', $penjualan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penjualan</label>
                            <input type="date" name="tanggal_penjualan"
                                value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan) }}" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet</label>
                            <select name="outlet_id" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach ($outlets as $o)
                                    <option value="{{ $o->outlet_id }}" @selected($penjualan->outlet_id == $o->outlet_id)>
                                        {{ $o->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                            <select name="karyawan_id"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->karyawan_id }}" @selected($penjualan->karyawan_id == $k->karyawan_id)>
                                        {{ $k->nama_karyawan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('penjualan.index') }}"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700">Batal</a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
