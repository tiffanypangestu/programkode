@extends('layouts.app')

@section('title', 'Edit Penjualan')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <a href="{{ route('penjualan.index') }}"
                class="text-sm font-medium text-gray-500 hover:text-emerald-600 transition">
                ← Kembali ke Data Penjualan
            </a>

            <div class="mb-6 mt-4 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Faktur Penjualan</h1>
                    <p class="text-sm text-gray-500 mt-1">Ubah rincian transaksi dan informasi item faktur ini.</p>
                </div>
            </div>

            <form action="{{ route('penjualan.update', $penjualan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Section Informasi Header Faktur --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Penjualan</label>
                            <input type="date" name="tanggal_penjualan"
                                value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan) }}" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Outlet</label>
                            <select name="outlet_id" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                                <option value="">-- Pilih Outlet --</option>
                                @foreach ($outlet as $o)
                                    <option value="{{ $o->outlet_id }}" @selected(old('outlet_id', $penjualan->outlet_id) == $o->outlet_id)>
                                        {{ $o->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section Detail Produk --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h2 class="text-lg font-bold text-gray-800">Detail Item Transaksi</h2>
                        <button type="button" id="btn-tambah-row"
                            class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                            + Tambah Produk
                        </button>
                    </div>

                    <div id="wrapper-produk" class="space-y-3">
                        @php
                            $details = $penjualan->details ?? ($penjualan->detailPenjualan ?? []);
                        @endphp

                        @forelse ($details as $detail)
                            <div
                                class="item-row grid grid-cols-12 gap-3 items-end border-b border-gray-100 pb-3 sm:border-0 sm:pb-0">
                                <div class="col-span-12 sm:col-span-4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1 sm:hidden">Nama
                                        Produk</label>
                                    <select name="produk_id[]" required onchange="updateHarga(this)"
                                        class="produk-select w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($produk as $p)
                                            <option value="{{ $p->produk_id }}" @selected($detail->produk_id == $p->produk_id)>
                                                {{ $p->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-4 sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1 sm:hidden">Jumlah</label>
                                    <input type="number" name="jumlah[]" min="1" value="{{ $detail->jumlah ?? 1 }}"
                                        required oninput="hitungSubtotalRow(this)" placeholder="Qty"
                                        class="input-jumlah w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div class="col-span-4 sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1 sm:hidden">Harga
                                        Satuan</label>
                                    <input type="number" name="harga_satuan[]" value="{{ $detail->harga_satuan ?? 0 }}"
                                        readonly
                                        class="input-harga w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600 outline-none">
                                </div>

                                <div class="col-span-3 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1 sm:hidden">Subtotal</label>
                                    <input type="text" readonly
                                        class="input-subtotal-display w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-emerald-600 outline-none"
                                        value="Rp 0">
                                </div>

                                <div class="col-span-1 flex justify-end">
                                    <button type="button" onclick="hapusRow(this)"
                                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 transition">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="item-row grid grid-cols-12 gap-3 items-end">
                                <div class="col-span-12 sm:col-span-4">
                                    <select name="produk_id[]" required onchange="updateHarga(this)"
                                        class="produk-select w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($produk as $p)
                                            <option value="{{ $p->produk_id }}">{{ $p->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input type="number" name="jumlah[]" min="1" value="1" required
                                        oninput="hitungSubtotalRow(this)" placeholder="Qty"
                                        class="input-jumlah w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input type="number" name="harga_satuan[]" value="0" readonly
                                        class="input-harga w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600 outline-none">
                                </div>
                                <div class="col-span-3 sm:col-span-3">
                                    <input type="text" readonly
                                        class="input-subtotal-display w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-emerald-600 outline-none"
                                        value="Rp 0">
                                </div>
                                <div class="col-span-1 flex justify-end">
                                    <button type="button" onclick="hapusRow(this)"
                                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 transition">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Ringkasan Total Harga --}}
                    <div class="mt-6 border-t pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-500">
                            *Total harga diperbarui otomatis berdasarkan item produk & jumlah unit.
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Total Harga
                                Faktur</span>
                            <div id="grand_total_display" class="text-3xl font-extrabold text-emerald-600">Rp 0</div>
                            <input type="hidden" name="total_harga" id="input_total_harga" value="0">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('penjualan.index') }}"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script JavaScript untuk Mengelola UI dan Kalkulasi Real-Time --}}
    <script>
        // Mengambil pemetaan harga produk yang dikirim dari Controller
        const mapHargaProduk = {!! $harga_produk ?? '{}' !!};

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function updateHarga(selectElement) {
            const row = selectElement.closest('.item-row');
            const produkId = selectElement.value;
            const harga = mapHargaProduk[produkId] || 0;

            row.querySelector('.input-harga').value = harga;
            hitungSubtotalRow(selectElement);
        }

        function hitungSubtotalRow(element) {
            const row = element.closest('.item-row');
            const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
            const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
            const subtotal = harga * jumlah;

            row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);
            hitungGrandTotal();
        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
                const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
                total += (harga * jumlah);
            });

            document.getElementById('grand_total_display').innerText = formatRupiah(total);
            document.getElementById('input_total_harga').value = total;
        }

        document.getElementById('btn-tambah-row').addEventListener('click', function() {
            const wrapper = document.getElementById('wrapper-produk');
            const firstRow = wrapper.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.produk-select').selectedIndex = 0;
            newRow.querySelector('.input-jumlah').value = 1;
            newRow.querySelector('.input-harga').value = 0;
            newRow.querySelector('.input-subtotal-display').value = 'Rp 0';

            wrapper.appendChild(newRow);
            hitungGrandTotal();
        });

        function hapusRow(button) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                button.closest('.item-row').remove();
                hitungGrandTotal();
            } else {
                alert('Faktur penjualan minimal harus memiliki 1 item produk.');
            }
        }

        // Jalankan kalkulasi harga otomatis saat halaman pertama kali dibuka
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.produk-select').forEach(select => {
                if (select.value) {
                    updateHarga(select);
                }
            });
        });
    </script>
@endsection
