@extends('layouts.app')

@section('title', 'Edit Jadwal Promosi')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('jadwal-promosi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">←
                Kembali</a>
            <div class="mb-6 mt-5">
                <h1 class="text-3xl font-bold text-gray-900">Edit Jadwal Promosi</h1>
            </div>

            <form action="{{ route('jadwal-promosi.update', $jadwalPromosi) }}" method="POST"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="promosi_id" class="mb-2 block text-sm font-medium text-gray-700">Program Promosi <span
                            class="text-red-500">*</span></label>
                    <select id="promosi_id" name="promosi_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Promosi --</option>
                        @foreach ($promosi ?? [] as $p)
                            <option value="{{ $p->promosi_id }}" @selected(old('promosi_id', $jadwalPromosi->promosi_id) == $p->promosi_id)>{{ $p->nama_promosi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tanggal_mulai" class="mb-2 block text-sm font-medium text-gray-700">Tanggal Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $jadwalPromosi->tanggal_mulai) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="mb-2 block text-sm font-medium text-gray-700">Tanggal Selesai
                            <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $jadwalPromosi->tanggal_selesai) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('jadwal-promosi.index') }}"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700">Batal</a>
                    <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
