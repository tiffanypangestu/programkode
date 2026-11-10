<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-64 flex-col border-r border-gray-300 bg-white shadow-xl lg:shadow-none transition-transform duration-300 ease-in-out -translate-x-full lg:static lg:translate-x-0 shrink-0">
    <div class="flex h-16 items-center justify-between border-b border-gray-100 px-6">
        <h1 class="text-xl font-bold text-green-700">UD Medan</h1>
        <button id="sidebar-close-btn" type="button" aria-label="Tutup Menu"
            class="lg:hidden rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto p-4">
        {{-- Group Atas --}}
        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Utama</p>
            <div class="space-y-1">
                {{-- Dashboard --}}
                @if (in_array(auth()->user()->role->nama_role ?? '', [
                        'Admin',
                        'Supervisor',
                        'Beauty Consultant',
                        'Sales Promotion Girl',
                    ]))
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('dashboard*') ? 'bg-green-600 text-white shadow-md shadow-green-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endif

                {{-- Penjualan --}}
                @if (in_array(auth()->user()->role->nama_role ?? '', [
                        'Admin',
                        'Supervisor',
                        'Beauty Consultant',
                        'Sales Promotion Girl',
                    ]))
                    <a href="{{ route('penjualan.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('penjualan.*') && !request()->routeIs('penjualan.import*') ? 'bg-green-600 text-white shadow-md shadow-green-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span>Penjualan</span>
                    </a>
                @endif

                {{-- Analisis --}}
                @if (in_array(auth()->user()->role->nama_role ?? '', [
                        'Admin',
                        'Supervisor',
                        'Beauty Consultant',
                        'Sales Promotion Girl',
                    ]))
                    <a href="{{ route('analisis-performa.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('analisis-performa*') ? 'bg-green-600 text-white shadow-md shadow-green-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        <span>Analisis</span>
                    </a>
                @endif

                @if (in_array(auth()->user()->role->nama_role ?? '', ['Admin', 'Supervisor']))
                    {{-- Import Excel --}}
                    <a href="{{ route('penjualan.import.form') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('penjualan.import*') ? 'bg-green-600 text-white shadow-md shadow-green-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Import Excel</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Group Tengah: Master Data --}}
        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Master Data</p>

                <div class="space-y-1">
                    <a href="{{ route('outlet.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('outlet*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Outlet</span>
                    </a>
                    <a href="{{ route('wilayah.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('wilayah*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Wilayah</span>
                    </a>
                    <a href="{{ route('kategori.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('kategori*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori Produk</span>
                    </a>
                    <a href="{{ route('produk.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('produk*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Produk</span>
                    </a>
                    <a href="{{ route('harga-produk.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('harga-produk*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Harga Wilayah</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Group Manajemen SDM: Karyawan, BC, SPG --}}
        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Manajemen Karyawan</p>
                <div class="space-y-1">
                    <a href="{{ route('karyawan.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('karyawan*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Karyawan</span>
                    </a>
                    <a href="{{ route('bc.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('bc*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Beauty Consultant</span>
                    </a>
                    <a href="{{ route('spg.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('spg*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Sales Promotion Girl</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Group Promosi --}}
        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Promosi</p>
                <div class="space-y-1">
                    <a href="{{ route('promosi.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('promosi*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                        <span>Promosi</span>
                    </a>
                    <a href="{{ route('jadwal-promosi.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('jadwal-promosi*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Jadwal Promosi</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Group Transaksi --}}
        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Transaksi</p>
                <div class="space-y-1">
                    <a href="{{ route('transaksi-detail.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('transaksi-detail*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Detail Transaksi</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    <div class="border-t border-gray-100 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="group flex items-center gap-3 w-full rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 bg-blue-50 text-blue-700 hover:bg-red-600 hover:text-white">
                <svg class="h-5 w-5 shrink-0 transition-colors duration-150" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
