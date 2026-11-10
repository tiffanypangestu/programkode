@extends('layouts.app')

@section('title', 'Data Penjualan')

@push('styles')
    {{-- Flatpickr CSS & MonthSelect Plugin CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <style>
        .flatpickr-calendar.hasMonthSelect {
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            font-family: inherit;
        }
        .flatpickr-calendar .flatpickr-current-month {
            font-size: 110%;
            padding: 8px 0;
        }
        .flatpickr-monthSelect-month.selected {
            background-color: #059669 !important;
            border-color: #059669 !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 0.5rem;
        }
        .flatpickr-monthSelect-month:hover {
            background-color: #d1fae5;
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            {{-- Header Menu & Tombol Aksi --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Data Penjualan
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Tempat mengelola dan memantau riwayat faktur penjualan UD Medan
                    </p>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    @if (in_array(auth()->user()->role->nama_role ?? '', ['Admin', 'Supervisor']))
                        <a href="{{ route('penjualan.import.form') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-100 px-4 py-2.5 text-sm font-semibold text-red-800 hover:bg-red-200 shadow-sm transition active:scale-95">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <span>Import Excel</span>
                        </a>
                    @endif

                    <a href="{{ route('penjualan.create') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-md transition active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Penjualan</span>
                    </a>
                </div>
            </div>

            {{-- Pesan Notifikasi Sukses --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card Filter Periode (Bulan dan Tahun - Flatpickr) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <form action="{{ route('penjualan.index') }}" method="GET" id="form-filter-penjualan" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <label for="periode_picker" class="text-sm font-semibold text-gray-700 whitespace-nowrap flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Filter Bulan & Tahun:</span>
                        </label>

                        <div class="relative w-full sm:w-64">
                            <input type="text" id="periode_picker" name="periode"
                                value="{{ $periode }}"
                                placeholder="Pilih Bulan & Tahun..."
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-800 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                        </div>

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">
                            Terapkan
                        </button>
                    </div>

                    {{-- Tombol Pintas Filter --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400">Pintas:</span>
                        <a href="{{ route('penjualan.index') }}"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition border {{ empty($tampilkanSemua) && $periode === date('Y-m') ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                            Bulan Ini
                        </a>
                        <a href="{{ route('penjualan.index', ['semua' => 1]) }}"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition border {{ !empty($tampilkanSemua) ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                            Semua Riwayat
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel Data Penjualan --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-md">
                @if ($penjualan->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">
                            Tidak Ada Data Penjualan
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if (!empty($periode))
                                Belum ada faktur penjualan tercatat pada periode <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</span>.
                            @else
                                Belum ada faktur penjualan yang tersimpan di sistem.
                            @endif
                        </p>
                        <div class="mt-4 flex justify-center gap-3">
                            <a href="{{ route('penjualan.index', ['semua' => 1]) }}"
                                class="rounded-xl border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                Lihat Semua Riwayat
                            </a>
                            <a href="{{ route('penjualan.create') }}"
                                class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                Tambah Penjualan Baru
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 w-16">
                                        Nomor
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Outlet
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Produk
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Total Harga
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($penjualan as $item)
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                            {{ $penjualan->firstItem() + $loop->index }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                            {{ $item->outlet->nama_outlet ?? '-' }}
                                            @if ($item->karyawan)
                                                <span class="block text-xs font-normal text-gray-400">
                                                    PIC: {{ $item->karyawan->nama_karyawan }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if ($item->transaksiDetail && $item->transaksiDetail->isNotEmpty())
                                                <span class="font-semibold text-gray-900">
                                                    {{ $item->transaksiDetail->first()->produk->nama_produk ?? 'Produk' }}
                                                </span>
                                                @if ($item->transaksiDetail->count() > 1)
                                                    <span class="text-xs text-gray-500 block">
                                                        +{{ $item->transaksiDetail->count() - 1 }} produk lainnya
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @if (in_array(auth()->user()->role->nama_role ?? auth()->user()->role, ['Admin', 'admin']))
                                                    <a href="{{ route('penjualan.pdf', $item->penjualan_id) }}"
                                                        target="_blank" title="Cetak Faktur PDF"
                                                        class="rounded-lg border border-emerald-200 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                                        PDF
                                                    </a>
                                                @endif
                                                <a href="{{ route('penjualan.show', $item) }}"
                                                    class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                                    Detail
                                                </a>
                                                <a href="{{ route('penjualan.edit', $item) }}"
                                                    class="rounded-lg border border-indigo-200 px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">
                                                    Edit
                                                </a>
                                                <form action="{{ route('penjualan.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Hapus faktur penjualan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-100 px-5 py-4">
                        {{ $penjualan->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Flatpickr JS & MonthSelect Plugin JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pickerInput = document.getElementById('periode_picker');
            if (!pickerInput) return;

            if (typeof flatpickr !== 'undefined') {
                const plugins = [];
                if (typeof monthSelectPlugin !== 'undefined') {
                    plugins.push(new monthSelectPlugin({
                        shorthand: false,
                        dateFormat: "Y-m",
                        altFormat: "F Y",
                        theme: "light"
                    }));
                }

                const fp = flatpickr(pickerInput, {
                    locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
                    plugins: plugins,
                    altInput: true,
                    altFormat: "F Y",
                    dateFormat: "Y-m",
                    defaultDate: "{{ $periode ?: date('Y-m') }}",
                    disableMobile: true,
                    onChange: function (selectedDates, dateStr, instance) {
                        if (dateStr) {
                            document.getElementById('form-filter-penjualan').submit();
                        }
                    }
                });
            } else {
                // Fallback jika CDN tidak aktif: ubah jadi native HTML5 month picker
                pickerInput.type = 'month';
                pickerInput.addEventListener('change', function () {
                    document.getElementById('form-filter-penjualan').submit();
                });
            }
        });
    </script>
@endpush
