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
                </div>
            </div>

            {{-- Tampilkan error validasi jika ada --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
                    <p class="text-sm font-semibold text-red-700 mb-1">Terjadi kesalahan, perubahan gagal disimpan:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('penjualan.update', $penjualan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Section Informasi Header Faktur --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Penjualan</label>
                            <input type="date" name="tanggal_penjualan" id="tanggal_penjualan"
                                value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan ? \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('Y-m-d') : '') }}"
                                required
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Outlet</label>
                            <select name="outlet_id" id="outlet_id" required onchange="onOutletChange()"
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
                            $rowItems = [];
                            if (old('produk_id')) {
                                foreach (old('produk_id') as $idx => $pId) {
                                    $rowItems[] = (object) [
                                        'produk_id' => $pId,
                                        'jumlah' => old('jumlah')[$idx] ?? 1,
                                        'harga_satuan' => old('harga_satuan')[$idx] ?? 0,
                                    ];
                                }
                            } elseif (isset($transaksiDetails) && $transaksiDetails->isNotEmpty()) {
                                foreach ($transaksiDetails as $detail) {
                                    $rowItems[] = (object) [
                                        'produk_id' => $detail->produk_id,
                                        'jumlah' => $detail->jumlah,
                                        'harga_satuan' => $detail->harga_satuan,
                                    ];
                                }
                            } elseif (isset($penjualan->transaksiDetail) && $penjualan->transaksiDetail->isNotEmpty()) {
                                foreach ($penjualan->transaksiDetail as $detail) {
                                    $rowItems[] = (object) [
                                        'produk_id' => $detail->produk_id,
                                        'jumlah' => $detail->jumlah,
                                        'harga_satuan' => $detail->harga_satuan,
                                    ];
                                }
                            }

                            if (empty($rowItems)) {
                                $rowItems[] = (object) [
                                    'produk_id' => '',
                                    'jumlah' => 1,
                                    'harga_satuan' => 0,
                                ];
                            }
                        @endphp

                        @foreach ($rowItems as $detail)
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
                                    <input type="number" name="harga_satuan[]"
                                        value="{{ number_format((float) $detail->harga_satuan ?? 0, 0, ',', '.') }}"
                                        readonly
                                        class="input-harga w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600 outline-none">
                                </div>

                                <div class="col-span-3 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1 sm:hidden">Subtotal</label>
                                    <input type="text" readonly
                                        class="input-subtotal-display w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-emerald-600 outline-none"
                                        value="Rp {{ number_format(($detail->harga_satuan ?? 0) * ($detail->jumlah ?? 1), 0, ',', '.') }}">
                                </div>

                                <div class="col-span-1 flex justify-end">
                                    <button type="button" onclick="hapusRow(this)"
                                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 transition">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Ringkasan Total Harga --}}
                    <div class="mt-6 border-t pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-500">
                            *Total harga diperbarui otomatis berdasarkan item produk & jumlah unit.
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Total Harga
                                Faktur</span>
                            <div id="grand_total_display" class="text-3xl font-extrabold text-emerald-600">Rp
                                {{ number_format($penjualan->total_harga ?? 0, 0, ',', '.') }}</div>
                            <input type="hidden" name="total_harga" id="input_total_harga"
                                value="{{ $penjualan->total_harga ?? 0 }}">
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
        const daftarHarga = @json($daftarHarga ?? []);
        const daftarOutlet = @json(
            $outlet->map(function ($item) {
                    return [
                        'outlet_id' => $item->outlet_id,
                        'wilayah_id' => $item->wilayah_id,
                    ];
                })->values());
        const mapHargaFallback = {!! $harga_produk ?? '{}' !!};

        // 1. DIBERSIHKAN & DIFORMAT: Hapus desimal koma, gunakan titik pemisah ribuan
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(Math.round(angka));
        }

        // Helper khusus untuk input harga satuan (tanpa prefix 'Rp ')
        function formatAngkaRibuan(angka) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(Math.round(angka));
        }

        function ambilWilayahOutlet() {
            const outletSelect = document.getElementById('outlet_id');
            if (!outletSelect) return null;
            const outletId = Number(outletSelect.value);
            const outletItem = daftarOutlet.find(item => Number(item.outlet_id) === outletId);
            return outletItem ? Number(outletItem.wilayah_id) : null;
        }

        function ambilHargaProduk(produkId) {
            if (!produkId) return 0;
            const wilayahId = ambilWilayahOutlet();
            const tanggalEl = document.getElementById('tanggal_penjualan');
            const tanggal = tanggalEl ? tanggalEl.value : '';

            if (wilayahId && tanggal && Array.isArray(daftarHarga)) {
                const tanggalStr = String(tanggal).substring(0, 10);
                const harga = daftarHarga.find(item => {
                    if (Number(item.produk_id) !== Number(produkId) || Number(item.wilayah_id) !== wilayahId) {
                        return false;
                    }
                    const tglMulai = item.tanggal_mulai ? String(item.tanggal_mulai).substring(0, 10) : null;
                    const tglAkhir = item.tanggal_berakhir ? String(item.tanggal_berakhir).substring(0, 10) : null;
                    return (!tglMulai || tglMulai <= tanggalStr) && (!tglAkhir || tglAkhir >= tanggalStr);
                });
                if (harga && harga.harga_produk) {
                    return Number(harga.harga_produk);
                }
            }

            if (mapHargaFallback && mapHargaFallback[produkId]) {
                return Number(mapHargaFallback[produkId]);
            }
            return 0;
        }

        // 2. PERBAIKAN DI SINI: Set nilai input harga dengan format angka ribuan
        function updateHarga(selectElement) {
            const row = selectElement.closest('.item-row');
            const produkId = selectElement.value;
            const harga = ambilHargaProduk(produkId);

            // Mengisi nilai berformat ribuan titik (contoh: 145.500)
            row.querySelector('.input-harga').value = formatAngkaRibuan(harga);
            hitungSubtotalRow(selectElement);
        }

        // 3. PERBAIKAN HITUNG SUBTOTAL: Bersihkan titik/karakter non-angka sebelum kalkulasi
        function hitungSubtotalRow(element) {
            const row = element.closest('.item-row');

            // Ambil teks harga, hilangkan titik/karakter non-angka agar jadi integer murni
            const hargaRaw = row.querySelector('.input-harga').value;
            const harga = parseFloat(hargaRaw.replace(/[^0-9]/g, '')) || 0;

            const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
            const subtotal = harga * jumlah;

            row.querySelector('.input-subtotal-display').value = formatRupiah(subtotal);
            hitungGrandTotal();
        }

        // 4. PERBAIKAN HITUNG GRAND TOTAL
        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const hargaRaw = row.querySelector('.input-harga').value;
                const harga = parseFloat(hargaRaw.replace(/[^0-9]/g, '')) || 0;
                const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
                total += (harga * jumlah);
            });

            document.getElementById('grand_total_display').innerText = formatRupiah(total);
            document.getElementById('input_total_harga').value = total;
        }

        function onOutletChange() {
            document.querySelectorAll('.item-row').forEach(row => {
                const select = row.querySelector('.produk-select');
                if (select && select.value) {
                    updateHarga(select);
                }
            });
        }

        document.getElementById('btn-tambah-row').addEventListener('click', function() {
            const wrapper = document.getElementById('wrapper-produk');
            const firstRow = wrapper.querySelector('.item-row');
            if (!firstRow) return;

            const newRow = firstRow.cloneNode(true);

            const select = newRow.querySelector('.produk-select');
            select.selectedIndex = 0;
            select.value = '';
            select.querySelectorAll('option').forEach(opt => opt.removeAttribute('selected'));

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

        document.addEventListener('DOMContentLoaded', function() {
            const tgl = document.getElementById('tanggal_penjualan');
            if (tgl) {
                tgl.addEventListener('change', onOutletChange);
            }
            hitungGrandTotal();
        });
    </script>
@endsection
