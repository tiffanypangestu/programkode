@extends('auth.layouts.app')

@section('title', 'Login')
@section('content')
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
        <p class="mt-2 text-sm leading-6 text-gray-500">
            Masuk ke akun untuk mengakses
        </p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
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

    <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">
                Email
            </label>

            <input type="email" name="email" id="email" value="{{ old('email') }}"
                placeholder="nama@perusahaan.com" autocomplete="email" required autofocus
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-3">
                <label for="password" class="text-sm font-semibold text-gray-700">
                    Password
                </label>

                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-gray-800 hover:text-gray-600">
                    Lupa Password?
                </a>
            </div>

            <input type="password" name="password" id="password" placeholder="Masukkan password"
                autocomplete="current-password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-700 accent-[#a78024]">
            Ingat Saya
        </label>

        <button type="submit"
            class="w-full rounded-lg bg-[#a78024] px-4 py-3 font-semibold text-white shadow-md shadow-[#a78024]/20 transition hover:bg-[#89691d] focus:outline-none focus:ring-2 focus:ring-[#a78024] focus:ring-offset-2">
            Login
        </button>
    </form>

    <p class="text-center text-sm text-gray-600">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="font-semibold text-[#9b7624] hover:underline">
            Daftar Sekarang
        </a>
    </p>
@endsection
