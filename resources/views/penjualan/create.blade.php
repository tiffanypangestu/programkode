@extends('layouts.app')

@section('title', 'Tambah Penjualan')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4-py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <a href="{{ route('penjualan.index') }}" class="text-sm font-medium text-black hover:text-blue-600">
                Kembali ke Data Penjualan
            </a>
            <div class="mb-6 mt-4">
                <h1 class="text-3xl font-bold text-gray-900">Input Faktur Penjualan</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih outlet dan produk
                </p>
            </div>

            <form action="{{ route('penjualan.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Tampilkan error validasi --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">
                        <p class="text-sm font-semibold text-red-700 mb-1">Terjadi kesalahan, penjualan gagal disimpan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">Informasi Utama</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="tanggal_penjualan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Penjualan <span class="text-red-600">*</span>
                            </label>
                            <input type="date" id="tanggal_penjualan" name="tanggal_penjualan"
                                value="{{ date('Y-m-d') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>

                        <div>
                            <label for="outlet_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Outlet <span
                                    class="text-red-600">*</span></label>
                            <select name="outlet_id" id="outlet_id" required onchange="updateAlamatOutlet()"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:outline-none focus:ring-green-600">
                                <option value="">---Pilih Outlet---</option>

                                @foreach ($outlet as $out)
                                    <option value="{{ $out->outlet_id }}"
                                        data-alamat="{{ $out->alamat_outlet ?? 'Alamat Tidak Tersedia' }}">
                                        {{ $out->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tampilan Alamat Otomatis --}}
                    <div id="box-alamat" class="hidden rounded-xl bg-gray-100 p-4 border border-gray-300">
                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Alamat Outlet
                        </p>
                        <p id="teks_alamat" class="text-xs font-medium text-gray-700 mt-0.5">
                            -
                        </p>
                    </div>
                </div>

                {{-- Rincian Detail Produk --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-semibold text-gray-900">Rincian Produk</h2>
                        <button type="button" onclick="tambahBarisProduk()"
                            class="rounded-xl bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                            + Tambah Produk
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="tabel_produk">
                            <thead>
                                <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                                    <th class="py-2 px-2 w-5/12">Produk</th>
                                    <th class="py-2 px-2 w-2/12">Jumlah</th>
                                    <th class="py-2 px-2 w-2/12">Harga Satuan</th>
                                    <th class="py-2 px-2 w-2/12 text-right">Subtotal</th>
                                    <th class="py-2 px-2 w-1/12 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="container_produk" class="divide-y divide-gray-100">
                                {{-- Baris pertama bawaan --}}
                                <tr class="item-row">
                                    <td class="py-3 px-2">
                                        <select name="produk_id[]" required onchange="hitungSubtotal(this)"
                                            class="produk-select w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($produk as $p)
                                                <option value="{{ $p->produk_id }}">
                                                    {{ $p->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number" name="jumlah[]" min="1" value="1" required
                                            oninput="hitungSubtotal(this)"
                                            class="jumlah-input w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number" name="harga_satuan[]" readonly value="0"
                                            class="harga-input w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-600">
                                    </td>
                                    <td class="py-3 px-2 text-right font-semibold text-emerald-600 subtotal-text">
                                        Rp 0
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <button type="button" onclick="hapusBaris(this)"
                                            class="text-red-500 hover:text-red-700 font-bold text-xl leading-none">&times;</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Subtotal --}}
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <div class="text-right">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Total Harga</span>
                            <div id="grand_total" class="text-2xl font-bold text-emerald-600">Rp 0</div>
                            <input type="hidden" name="total_harga" id="input_total_harga" value="0">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Simpan
                            Penjualan</button>
                    </div>
            </form>
        </div>
    </div>

    <script>
        const daftarHarga = @json($daftarHarga);
        const daftarOutlet = @json(
            $outlet->map(function ($item) {
                    return [
                        'outlet_id' => $item->outlet_id,
                        'wilayah_id' => $item->wilayah_id,
                    ];
                })->values());

        // Debug: tampilkan data di console (bisa dihapus setelah beres)
        console.log('[Debug] daftarHarga:', daftarHarga);
        console.log('[Debug] daftarOutlet:', daftarOutlet);


        function ambilWilayahOutlet() {
            const outletId = Number(document.getElementById('outlet_id').value);

            const outlet = daftarOutlet.find(
                item => Number(item.outlet_id) === outletId
            );

            return outlet ? Number(outlet.wilayah_id) : null;
        }

        function ambilHargaProduk(produkId) {
            const wilayahId = ambilWilayahOutlet();
            const tanggal = document.getElementById('tanggal_penjualan').value;

            if (!wilayahId || !produkId || !tanggal) {
                return 0;
            }

            const harga = daftarHarga.find(item =>
                Number(item.produk_id) === Number(produkId) &&
                Number(item.wilayah_id) === wilayahId &&
                (!item.tanggal_mulai || item.tanggal_mulai <= tanggal) &&
                (!item.tanggal_berakhir || item.tanggal_berakhir >= tanggal)
            );

            console.log('[Debug] Cari harga: produk_id=' + produkId + ', wilayah_id=' + wilayahId + ', tanggal=' + tanggal + ' => ' + (harga ? harga.harga_produk : 'tidak ditemukan'));

            return harga ? Number(harga.harga_produk) : 0;
        }


        function RupiahFormat(angka) {
            return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
        }

        // Update Alamat Outlet

        function updateAlamatOutlet() {
            const select = document.getElementById('outlet_id');
            const option = select.options[select.selectedIndex];
            const kotak = document.getElementById('box-alamat');
            const teks = document.getElementById('teks_alamat');

            if (select.value) {
                teks.textContent = option.dataset.alamat || 'Alamat Tidak Tersedia!';
                kotak.classList.remove('hidden');
            } else {
                kotak.classList.add('hidden');
                teks.textContent = '-';
            }

            document.querySelectorAll('#container_produk .item-row')
                .forEach(row => {
                    hitungSubtotal(row.querySelector('.produk-select'));
                });
        }


        // Hitung Subtotal per Baris
        function hitungSubtotal(element) {
            const row = element.closest('.item-row');
            if (!row) return;

            const selectProduk = row.querySelector('.produk-select');
            const inputJumlah = row.querySelector('.jumlah-input');
            const inputHarga = row.querySelector('.harga-input');
            const teksSubtotal = row.querySelector('.subtotal-text');

            const produkId = selectProduk.value;
            const wilayahId = ambilWilayahOutlet();

            // Jika produk dipilih tapi outlet belum dipilih
            if (produkId && !wilayahId) {
                inputHarga.value = 0;
                teksSubtotal.textContent = '⚠ Pilih outlet dulu';
                teksSubtotal.classList.add('text-red-500');
                teksSubtotal.classList.remove('text-emerald-600');
                hitungHargaTotal();
                return;
            }

            const harga = ambilHargaProduk(produkId);
            const jumlah = Math.max(0, Number(inputJumlah.value || 0));
            const subtotal = harga * jumlah;

            inputHarga.value = harga;

            // Jika produk & outlet dipilih tapi harga tidak ada
            if (produkId && wilayahId && harga === 0) {
                teksSubtotal.textContent = '⚠ Harga belum ada';
                teksSubtotal.classList.add('text-orange-500');
                teksSubtotal.classList.remove('text-emerald-600', 'text-red-500');
            } else {
                teksSubtotal.textContent = RupiahFormat(subtotal);
                teksSubtotal.classList.remove('text-red-500', 'text-orange-500');
                teksSubtotal.classList.add('text-emerald-600');
            }

            hitungHargaTotal();
        }

        // Hitung Grand Total Seluruh Baris
        function hitungHargaTotal() {
            let total = 0;
            document.querySelectorAll('#container_produk .item-row').forEach(function(row) {
                const selectProduk = row.querySelector('.produk-select');
                const inputJumlah = row.querySelector('.jumlah-input');
                const option = selectProduk.options[selectProduk.selectedIndex];

                const harga = ambilHargaProduk(selectProduk.value);
                const jumlah = Math.max(0, Number(inputJumlah.value || 0));
                total += harga * jumlah;
            });

            // Diperbaiki: ID disamakan dengan HTML (grand_total)
            const grandTotalElem = document.getElementById('grand_total');
            if (grandTotalElem) {
                grandTotalElem.textContent = RupiahFormat(total);
            }

            const inputTotalHarga = document.getElementById('input_total_harga');
            if (inputTotalHarga) {
                inputTotalHarga.value = total;
            }
        }

        // Tambah Baris Produk Baru
        function tambahBarisProduk() {
            const container = document.getElementById('container_produk');
            const barisPertama = container.querySelector('.item-row');
            if (!barisPertama) return;

            const barisBaru = barisPertama.cloneNode(true);

            // Reset Nilai pada Baris Baru
            barisBaru.querySelector('.produk-select').selectedIndex = 0;
            barisBaru.querySelector('.jumlah-input').value = 1;
            barisBaru.querySelector('.harga-input').value = 0;

            const subtotalEl = barisBaru.querySelector('.subtotal-text');
            subtotalEl.textContent = RupiahFormat(0);
            subtotalEl.classList.remove('text-red-500', 'text-orange-500');
            subtotalEl.classList.add('text-emerald-600');

            container.appendChild(barisBaru);
            hitungHargaTotal();
        }

        // Hapus Baris Produk
        function hapusBaris(button) {
            const container = document.getElementById('container_produk');
            const rows = container.querySelectorAll('.item-row');

            if (rows.length > 1) {
                button.closest('.item-row').remove();
                hitungHargaTotal();
            } else {
                alert('Faktur minimal harus memiliki 1 produk');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateAlamatOutlet();

            document.getElementById('tanggal_penjualan')
                .addEventListener('change', function() {
                    document.querySelectorAll('#container_produk .item-row')
                        .forEach(row => {
                            hitungSubtotal(row.querySelector('.produk-select'));
                        });
                });

            hitungHargaTotal();
        });
    </script>
@endsection

