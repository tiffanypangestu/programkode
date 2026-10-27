@extends('auth.layouts.app')

@section('title', 'Lupa Password')

@section('content')
    <div class="mb-7">
        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#f8f1df] text-[#a78024]">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="m3 7 9 6 9-6" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">Lupa Password?</h1>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Masukkan email terdaftar
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

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">
                Email Terdaftar
            </label>

            <input type="email" name="email" id="email" value="{{ old('email') }}"
                placeholder="nama@perusahaan.com" autocomplete="email" required autofocus
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-[#a78024] px-4 py-3 font-semibold text-white shadow-md shadow-[#a78024]/20 transition hover:bg-[#89691d] focus:outline-none focus:ring-2 focus:ring-[#a78024] focus:ring-offset-2">
            Kirim Link Reset
        </button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-[#9b7624] hover:underline">
            &larr; Kembali ke Login
        </a>
    </p>
@endsection
