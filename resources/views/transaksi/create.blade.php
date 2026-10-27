@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">
                &larr; Kembali ke Data Transaksi
            </a>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Tambah Transaksi</h1>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan informasi transaksi penjualan.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksi.store') }}" method="POST"
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @csrf

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold text-gray-900">Informasi Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Isi identitas transaksi dan pihak terkait.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                <div>
                    <label for="nomor_transaksi" class="mb-2 block text-sm font-medium text-gray-700">
                        Nomor Transaksi
                    </label>
                    <input type="text" id="nomor_transaksi" name="nomor_transaksi" value="{{ old('nomor_transaksi') }}"
                        maxlength="50" placeholder="Contoh: TRX001"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('nomor_transaksi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal" class="mb-2 block text-sm font-medium text-gray-700">
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wilayah_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Wilayah <span class="text-red-500">*</span>
                    </label>
                    <select id="wilayah_id" name="wilayah_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach ($wilayah as $item)
                            <option value="{{ $item->wilayah_id }}" @selected(old('wilayah_id') == $item->wilayah_id)>
                                {{ $item->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                    @error('wilayah_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="outlet_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Outlet <span class="text-red-500">*</span>
                    </label>
                    <select id="outlet_id" name="outlet_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Outlet --</option>
                        @foreach ($outlet as $item)
                            <option value="{{ $item->outlet_id }}" @selected(old('outlet_id') == $item->outlet_id)>
                                {{ $item->kode_outlet }} - {{ $item->nama_outlet }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="karyawan_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Karyawan Penanggung Jawab <span class="text-red-500">*</span>
                    </label>
                    <select id="karyawan_id" name="karyawan_id" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawan as $item)
                            <option value="{{ $item->karyawan_id }}" @selected(old('karyawan_id') == $item->karyawan_id)>
                                {{ $item->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_transaksi" class="mb-2 block text-sm font-medium text-gray-700">
                        Total Transaksi (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="total_transaksi" name="total_transaksi"
                        value="{{ old('total_transaksi', 0) }}" min="0" step="0.01" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('total_transaksi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Pastikan nilainya sesuai dengan total detail produk transaksi.
                    </p>
                </div>

                <div>
                    <label for="tipe_transaksi" class="mb-2 block text-sm font-medium text-gray-700">
                        Tipe Transaksi
                    </label>
                    <input type="text" id="tipe_transaksi" name="tipe_transaksi" value="{{ old('tipe_transaksi') }}"
                        maxlength="50" placeholder="Masukkan tipe transaksi jika ada"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    @error('tipe_transaksi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if ($wilayah->isEmpty() || $outlet->isEmpty() || $karyawan->isEmpty())
                <div class="mx-6 mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Data wilayah, outlet, dan karyawan harus tersedia terlebih dahulu sebelum transaksi dapat dicatat.
                </div>
            @endif

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row">
                <a href="{{ route('transaksi.index') }}"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
@endsection
