<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Autentikasi') - BRC Gold Cosmetics</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f7f5f0] text-gray-800 antialiased">

    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <a href="{{ route('login') }}" class="inline-flex flex-col items-center">
                    <img src="{{ asset('build/assets/images/brcgold.webp') }}" alt="BRC Gold Cosmetics"
                        class="mb-4 h-20 w-auto object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">

                    <span class="hidden text-2xl font-bold tracking-[0.2em] text-[#a78024]">
                        UD MEDAN
                    </span>
                </a>

                <p class="mt-3 text-sm text-gray-500">
                    Sistem Monitoring Penjualan dan Performa Outlet
                </p>
            </div>

            <section class="rounded-2xl border border-[#e8dfc9] bg-white p-6 shadow-xl shadow-[#a78024]/5 sm:p-8">
                @yield('content')
            </section>

            <footer class="mt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} BRC Gold Cosmetics. All rights reserved.
            </footer>

        </div>
    </main>

</body>

</html>
