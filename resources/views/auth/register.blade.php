@extends('auth.layouts.app')
@section('title', 'Register')

@section('content')
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-gray-900">Buat Akun</h1>
        <p class="mt-2 text-sm leading-6 text-gray-500">
            Daftarkan akun untuk menggunakan sistem BRC Gold Cosmetics.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="nama_lengkap" class="mb-2 block text-sm font-semibold text-gray-700">
                Nama Lengkap
            </label>

            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                placeholder="Masukkan Nama Lengkap" autocomplete="name" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                Nama Pengguna
            </label>

            <input type="text" name="name" id="name" value="{{ old('name') }}"
                placeholder="Masukkan nama pengguna" autocomplete="username" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">

        </div>

        <div>
            <label for="jabatan" class="mb-2 block text-sm font-semibold text-gray-700">
                Jabatan
            </label>

            <select name="jabatan" id="jabatan" required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                <option value="">Pilih jabatan</option>

                <option value="Admin" {{ old('jabatan') === 'Admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="Supervisor" {{ old('jabatan') === 'Supervisor' ? 'selected' : '' }}>
                    Supervisor
                </option>

                <option value="Beauty Consultant" {{ in_array(old('jabatan'), ['Beauty Consultant', 'BC']) ? 'selected' : '' }}>
                    BC (Beauty Consultant)
                </option>

                <option value="Sales Promotion Girl" {{ in_array(old('jabatan'), ['Sales Promotion Girl', 'SPG']) ? 'selected' : '' }}>
                    SPG (Sales Promotion Girl)
                </option>
            </select>

            @error('jabatan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>


        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">
                Email
            </label>

            <input type="email" name="email" id="email" value="{{ old('email') }}"
                placeholder="nama@perusahaan.com" autocomplete="email" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">
                Password
            </label>

            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                autocomplete="new-password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">

        </div>

        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">
                Konfirmasi Password
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password"
                autocomplete="new-password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-[#a78024] px-4 py-3 font-semibold text-white shadow-md shadow-[#a78024]/20 transition hover:bg-[#89691d] focus:outline-none focus:ring-2 focus:ring-[#a78024] focus:ring-offset-2">
            Daftar Akun
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-[#9b7624] hover:underline">
            Masuk di sini
        </a>
    </p>
@endsection
