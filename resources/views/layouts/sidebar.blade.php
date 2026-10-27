<aside class="flex h-screen w-64 flex-col border-r border-gray-300 bg-white">
    <div class="flex h-16 items-center border-b border-gray-100 px-6">
        <h1 class="text-xl font-bold text-green-700">UD Medan</h1>
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
                        <span class="text-lg">📊</span>
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
                        <span class="text-lg">📈</span>
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
                        <span class="text-lg">🧠</span>
                        <span>Analisis</span>
                    </a>
                @endif

                @if (in_array(auth()->user()->role->nama_role ?? '', ['Admin', 'Supervisor']))
                    {{-- Import Excel --}}
                    <a href="{{ route('penjualan.import.form') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('penjualan.import*') ? 'bg-green-600 text-white shadow-md shadow-green-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">📥</span>
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
                        <span class="text-lg">🏪</span>
                        <span>Outlet</span>
                    </a>
                    <a href="{{ route('wilayah.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('wilayah*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">📍</span>
                        <span>Wilayah</span>
                    </a>
                    <a href="{{ route('kategori.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('kategori*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">🏷️</span>
                        <span>Kategori Produk</span>
                    </a>
                    <a href="{{ route('produk.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('produk*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">📦</span>
                        <span>Produk</span>
                    </a>
                    <a href="{{ route('harga-produk.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('harga-produk*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">💵</span>
                        <span>Harga Wilayah</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Group Manajemen SDM: Karyawan, BC, SPG --}}
        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Manajemen Karyawan</p>
                <a href="{{ route('karyawan.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('karyawan*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-black' }}">
                    <span class="text-lg">
                        <svg class="h-6 w-6 text-black hover:text-white" viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M74.8,20.3H25.2a5,5,0,0,0-5,5V59.6a5,5,0,0,0,5,5H74.7a5,5,0,0,0,5-5V25.2A4.91,4.91,0,0,0,74.8,20.3ZM72.3,55.2a2,2,0,0,1-1.9,1.9H29.6a1.9,1.9,0,0,1-1.9-1.9V29.6a2,2,0,0,1,1.9-1.9H70.3a2,2,0,0,1,2,1.9Z" />
                            <path
                                d="M41.3,72.3a5,5,0,0,0-5,5v.6a2,2,0,0,0,1.9,1.9H61.7a2,2,0,0,0,1.9-1.9v-.6a5,5,0,0,0-5-5Z" />
                            <circle cx="50.1" cy="37.6" r="5.8" />
                            <path
                                d="M50.2,44.4h-.4a8.83,8.83,0,0,0-8.7,7.5c0,.4.1,1.2,1.5,1.2H57.5c1.4,0,1.5-.9,1.5-1.2A9,9,0,0,0,50.2,44.4Z" />
                        </svg>
                    </span>
                    <span>Karyawan</span>
                </a>
                <a href="{{ route('bc.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('bc*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-black' }}">
                    <span class="text-lg">
                        <svg class="h-6 w-6 text-black hover:text-white" viewBox="0 0 32 32"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16 15.503A5.041 5.041 0 1 0 16 5.42a5.041 5.041 0 0 0 0 10.083zm0 2.215c-6.703 0-11 3.699-11 5.5v3.363h22v-3.363c0-2.178-4.068-5.5-11-5.5z" />
                        </svg>
                    </span>
                    <span>Beauty Consultant</span>
                </a>
                <a href="{{ route('spg.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('spg*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-black' }}">
                    <span class="text-lg">
                        <svg class="h-6 w-6 text-black hover:text-white" version="1.1" id="_x32_"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <path class="st0" d="M194.651,414.476c16.23,0,61.349,0,61.349,0s45.111,0,61.35,0c16.222,0,23.587-23.603,14.198-40.285
  c-7.072-12.572-18.659-26.826-37.516-34.921c-10.793,7.556-23.905,12-38.032,12c-14.143,0-27.238-4.444-38.032-12
  c-18.864,8.095-30.444,22.349-37.523,34.921C171.064,390.873,178.421,414.476,194.651,414.476z" />
                                <path class="st0" d="M256,335.476c27.714,0,50.167-22.444,50.167-50.159v-12.016c0-27.714-22.452-50.174-50.167-50.174
  c-27.714,0-50.174,22.46-50.174,50.174v12.016C205.826,313.032,228.286,335.476,256,335.476z" />
                                <path class="st0" d="M404.977,56.889h-75.834v16.254c0,31.365-25.524,56.889-56.889,56.889h-32.508
  c-31.365,0-56.889-25.524-56.889-56.889V56.889h-75.833c-25.445,0-46.072,20.627-46.072,46.071v362.969
  c0,25.444,20.627,46.071,46.072,46.071h297.952c25.444,0,46.071-20.627,46.071-46.071V102.96
  C451.048,77.516,430.421,56.889,404.977,56.889z M402.286,463.238H109.714V150.349h292.572V463.238z" />
                                <path class="st0" d="M239.746,113.778h32.508c22.406,0,40.635-18.23,40.635-40.635V40.635C312.889,18.23,294.659,0,272.254,0
  h-32.508c-22.405,0-40.635,18.23-40.635,40.635v32.508C199.111,95.547,217.341,113.778,239.746,113.778z M231.619,40.635
  c0-4.492,3.635-8.127,8.127-8.127h32.508c4.493,0,8.127,3.635,8.127,8.127v16.254c0,4.492-3.634,8.127-8.127,8.127h-32.508
  c-4.492,0-8.127-3.635-8.127-8.127V40.635z" />
                            </g>
                        </svg>
                    </span>
                    <span>Sales Promotion Girl</span>
                </a>
            </div>
        @endif

        {{-- Group Promosi --}}

        @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
            <div>
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Promosi</p>
                <div class="space-y-1">
                    <a href="{{ route('promosi.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('promosi*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">🎁</span>
                        <span>Promosi</span>
                    </a>
                    <a href="{{ route('jadwal-promosi.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('jadwal-promosi*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="text-lg">📅</span>
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
                        <span class="text-lg">📄</span>
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
                class="group flex items-center gap-3 w-full rounded-xl px-4 py-2.5 text-md text-semibold transition-all duration-100 bg-blue-100 text-black hover:bg-blue-600 hover:text-white">
                <span>
                    <svg class="h-5 w-5 text-black hover:text-white" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9.00195 7C9.01406 4.82497 9.11051 3.64706 9.87889 2.87868C10.7576 2 12.1718 2 15.0002 2L16.0002 2C18.8286 2 20.2429 2 21.1215 2.87868C22.0002 3.75736 22.0002 5.17157 22.0002 8L22.0002 16C22.0002 18.8284 22.0002 20.2426 21.1215 21.1213C20.2429 22 18.8286 22 16.0002 22H15.0002C12.1718 22 10.7576 22 9.87889 21.1213C9.11051 20.3529 9.01406 19.175 9.00195 17"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M15 12L2 12M2 12L5.5 9M2 12L5.5 15" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
