@extends('auth.layouts.app')

@section('title', 'Lupa Password')

@section('content')
    <div class="mb-7">
        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#f8f1df] text-[#a78024]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">Lupa Password?</h1>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Masukkan email terdaftar dan ketik langsung password baru Anda.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Email Terdaftar --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">
                Email Terdaftar <span class="text-red-600">*</span>
            </label>

            <input type="email" name="email" id="email" value="{{ old('email') }}"
                placeholder="nama@perusahaan.com" autocomplete="email" required autofocus
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        {{-- Password Baru (Bisa Dilihat) --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">
                Password Baru <span class="text-red-600">*</span>
            </label>

            <div class="relative">
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-11 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                
                <button type="button" onclick="togglePassword('password', 'eye-icon-1', 'eye-off-icon-1')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 focus:outline-none"
                    title="Tampilkan / Sembunyikan Password">
                    {{-- Eye open --}}
                    <svg id="eye-icon-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{-- Eye closed --}}
                    <svg id="eye-off-icon-1" class="hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                        <line x1="2" x2="22" y1="2" y2="22"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Konfirmasi Password Baru (Bisa Dilihat) --}}
        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">
                Konfirmasi Password Baru <span class="text-red-600">*</span>
            </label>

            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 pr-11 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2', 'eye-off-icon-2')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 focus:outline-none"
                    title="Tampilkan / Sembunyikan Password">
                    {{-- Eye open --}}
                    <svg id="eye-icon-2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{-- Eye closed --}}
                    <svg id="eye-off-icon-2" class="hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                        <line x1="2" x2="22" y1="2" y2="22"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-[#a78024] px-4 py-3 font-semibold text-white shadow-md shadow-[#a78024]/20 transition hover:bg-[#89691d] focus:outline-none focus:ring-2 focus:ring-[#a78024] focus:ring-offset-2">
            Simpan Password Baru
        </button>
    </form>

    <script>
        function togglePassword(inputId, openIconId, closedIconId) {
            const input = document.getElementById(inputId);
            const openIcon = document.getElementById(openIconId);
            const closedIcon = document.getElementById(closedIconId);

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (openIcon) openIcon.classList.add('hidden');
                if (closedIcon) closedIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                if (openIcon) openIcon.classList.remove('hidden');
                if (closedIcon) closedIcon.classList.add('hidden');
            }
        }
    </script>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-[#9b7624] hover:underline">
            &larr; Kembali ke Login
        </a>
    </p>
@endsection
