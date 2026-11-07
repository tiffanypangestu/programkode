<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'UD Medan')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-100 antialiased">
    {{-- Overlay backdrop untuk mobile saat sidebar terbuka --}}
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/50 transition-opacity duration-300 hidden lg:hidden"></div>

    <div class="flex h-screen overflow-hidden">
        {{-- Left Sidebar --}}
        @include('layouts.sidebar')

        {{-- Konten Kanan --}}
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            {{-- Header khusus layar mobile / tablet --}}
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:hidden shrink-0">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle-btn" type="button" aria-label="Buka Menu"
                        class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-green-700">UD Medan</h1>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script untuk buka/tutup sidebar di layar mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');

            function openSidebar() {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 1024) {
                            closeSidebar();
                        }
                    });
                });
            }

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) {
                    if (backdrop) backdrop.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
