 @extends('layouts.app')

 @section('content')
     <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
         {{-- Header --}}
         <div class="mb-8">
             <a href="{{ route('karyawan.index') }}"
                 class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-indigo-600">
                 <span>&larr;</span>
                 Kembali ke Data Karyawan
             </a>

             <div class="mt-5">
                 <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                     Tambah Karyawan
                 </h1>
                 <p class="mt-2 text-sm text-gray-500">
                     Lengkapi informasi karyawan untuk menambahkannya ke dalam sistem UD Medan.
                 </p>
             </div>
         </div>

         {{-- Validation errors --}}
         @if ($errors->any())
             <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                 <div class="flex gap-3">
                     <div class="text-red-600">!</div>
                     <div>
                         <h2 class="font-semibold text-red-800">
                             Data belum dapat disimpan
                         </h2>
                         <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                             @foreach ($errors->all() as $error)
                                 <li>{{ $error }}</li>
                             @endforeach
                         </ul>
                     </div>
                 </div>
             </div>
         @endif

         {{-- Form --}}
         <form action="{{ route('karyawan.store') }}" method="POST"
             class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
             @csrf

             <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
                 <h2 class="text-lg font-semibold text-gray-900">
                     Informasi Karyawan
                 </h2>
                 <p class="mt-1 text-sm text-gray-500">
                     Kolom bertanda <span class="text-red-500">*</span> wajib diisi.
                 </p>
             </div>

             <div class="grid grid-cols-1 gap-6 px-6 py-7 sm:grid-cols-2 sm:px-8">

                 {{-- Nama --}}
                 <div class="sm:col-span-2">
                     <label for="nama_karyawan" class="mb-2 block text-sm font-medium text-gray-700">
                         Nama Karyawan <span class="text-red-500">*</span>
                     </label>
                     <input type="text" id="nama_karyawan" name="nama_karyawan" value="{{ old('nama_karyawan') }}"
                         required maxlength="255" placeholder="Masukkan nama lengkap karyawan"
                         class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                     @error('nama_karyawan')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

                 {{-- Role --}}
                 <div>
                     <label for="role_id" class="mb-2 block text-sm font-medium text-gray-700">
                         Jabatan <span class="text-red-500">*</span>
                     </label>
                     <select id="role_id" name="role_id" required
                         class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                         <option value="">-- Pilih Role --</option>
                         @foreach ($role as $item)
                             <option value="{{ $item->role_id }}"
                                 {{ (string) old('role_id') === (string) $item->role_id ? 'selected' : '' }}>
                                 {{ $item->nama_role }}
                             </option>
                         @endforeach
                     </select>
                     @error('role_id')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

                 {{-- User --}}
                 <div>
                     <label for="user_id" class="mb-2 block text-sm font-medium text-gray-700">
                         Akun Pengguna
                     </label>
                     <select id="user_id" name="user_id"
                         class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                         <option value="">-- Tanpa akun pengguna --</option>
                         @foreach ($users as $user)
                             <option value="{{ $user->user_id }}"
                                 {{ (string) old('user_id') === (string) $user->user_id ? 'selected' : '' }}>
                                 {{ $user->name }}
                             </option>
                         @endforeach
                     </select>
                     <p class="mt-1.5 text-xs text-gray-500">
                         Opsional. Pilih akun yang belum terhubung dengan karyawan lain.
                     </p>
                     @error('user_id')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

                 {{-- Nomor telepon --}}
                 <div>
                     <label for="nomor_telepon" class="mb-2 block text-sm font-medium text-gray-700">
                         Nomor Telepon
                     </label>
                     <input type="tel" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                         maxlength="20" placeholder="Contoh: 081234567890"
                         class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                     @error('nomor_telepon')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

                 {{-- Tahun masuk --}}
                 <div>
                     <label for="tahun_masuk" class="mb-2 block text-sm font-medium text-gray-700">
                         Tahun Masuk
                     </label>
                     <input type="number" id="tahun_masuk" name="tahun_masuk" value="{{ old('tahun_masuk') }}"
                         min="1900" max="{{ date('Y') + 1 }}" placeholder="Contoh: {{ date('Y') }}"
                         class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                     @error('tahun_masuk')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

             </div>

             {{-- Actions --}}
             <div
                 class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
                 <a href="{{ route('karyawan.index') }}"
                     class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                     Batal
                 </a>

                 <button type="submit"
                     class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                     Simpan Karyawan
                 </button>
             </div>
         </form>
     </div>
 @endsection
