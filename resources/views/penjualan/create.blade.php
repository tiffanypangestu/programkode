@extends('layouts.app')

@section('title', 'Tambah Penjualan')

@section('content')
    <div class="min-h-screen bg-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('penjualan.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-green-700">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Data Penjualan
                </a>

                {{-- Badge Role Pengguna --}}
                <div class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                    @if (in_array(strtolower($namaRole ?? ''), ['spg', 'sales promotion girl']))
                        bg-pink-100 text-pink-800 border border-pink-200
                    @elseif (in_array(strtolower($namaRole ?? ''), ['bc', 'beauty consultant']))
                        bg-purple-100 text-purple-800 border border-purple-200
                    @elseif (in_array(strtolower($namaRole ?? ''), ['supervisor']))
                        bg-blue-100 text-blue-800 border border-blue-200
                    @else
                        bg-gray-100 text-gray-800 border border-gray-200
                    @endif">
                    <span class="h-2 w-2 rounded-full
                        @if (in_array(strtolower($namaRole ?? ''), ['spg', 'sales promotion girl'])) bg-pink-500
                        @elseif (in_array(strtolower($namaRole ?? ''), ['bc', 'beauty consultant'])) bg-purple-500
                        @elseif (in_array(strtolower($namaRole ?? ''), ['supervisor'])) bg-blue-500
                        @else bg-gray-500 @endif"></span>
                    <span>Role: {{ auth()->user()->role->nama_role ?? ucfirst($namaRole ?? 'User') }}</span>
                </div>
            </div>

            <div class="mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Input Faktur Penjualan</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih outlet bertugas dan produk untuk mencatat faktur penjualan baru. Sisa stok dihitung otomatis berdasarkan faktur penjualan yang tercatat.
                </p>
            </div>

            <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan" class="space-y-6">
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

                {{-- Alert Peringatan Melebihi Stok Real-Time --}}
                <div id="alert-stok-kurang" class="hidden rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⚠️</span>
                        <span id="pesan-stok-kurang" class="font-medium"></span>
                    </div>
                </div>

                {{-- Card 1: Informasi Utama --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-3 gap-2">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Utama</h2>
                        @if (in_array(strtolower($namaRole ?? ''), ['spg', 'sales promotion girl']))
                            <span class="text-xs text-pink-700 bg-pink-50 border border-pink-200 px-2.5 py-1 rounded-lg">
                                👩‍💼 Mode SPG: Menampilkan outlet tempat Anda bertugas hari ini.
                            </span>
                        @elseif (in_array(strtolower($namaRole ?? ''), ['bc', 'beauty consultant']))
                            <span class="text-xs text-purple-700 bg-purple-50 border border-purple-200 px-2.5 py-1 rounded-lg">
                                💄 Mode BC: Anda dapat memilih outlet counter yang Anda tangani.
                            </span>
                        @elseif (in_array(strtolower($namaRole ?? ''), ['supervisor']))
                            <span class="text-xs text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg">
                                📋 Mode Supervisor: Akses supervisi stok & penjualan seluruh outlet.
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="tanggal_penjualan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Penjualan <span class="text-red-600">*</span>
                            </label>
                            <input type="date" id="tanggal_penjualan" name="tanggal_penjualan"
                                value="{{ old('tanggal_penjualan', date('Y-m-d')) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>

                        <div>
                            <label for="outlet_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Outlet Tempat Bertugas
                                @if (!empty($outletTugasKhusus))
                                    <span class="ml-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        Outlet Tugas Anda
                                    </span>
                                @endif
                                <span class="text-red-600">*</span>
                            </label>
                            <select name="outlet_id" id="outlet_id" required onchange="gantiOutlet()"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:outline-none focus:ring-green-600">
                                @if ($outlet->count() > 1 && !old('outlet_id') && empty($defaultOutletId))
                                    <option value="">--- Pilih Outlet Tempat Bertugas ---</option>
                                @endif

                                @foreach ($outlet as $out)
                                    <option value="{{ $out->outlet_id }}"
                                        data-alamat="{{ $out->alamat_outlet ?? 'Alamat Tidak Tersedia' }}"
                                        data-wilayah="{{ $out->wilayah_id }}"
                                        @selected((string) old('outlet_id', $defaultOutletId) === (string) $out->outlet_id)>
                                        {{ $out->nama_outlet }}
                                        ({{ $out->wilayah->nama_wilayah ?? 'Wilayah ' . $out->wilayah_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tampilan Alamat Otomatis --}}
                    <div id="box-alamat" class="hidden rounded-xl bg-gray-50 p-4 border border-gray-200">
                        <div class="flex items-center gap-2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs font-semibold uppercase tracking-wider">Alamat Outlet:</span>
                        </div>
                        <p id="teks_alamat" class="text-xs font-medium text-gray-700 mt-1 pl-6">
                            -
                        </p>
                    </div>
                </div>

                {{-- Card 2: Status Stok & Rekap Penjualan Outlet Saat Ini --}}
                <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 bg-emerald-50/60 px-6 py-4 gap-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white font-bold text-sm shadow">
                                📦
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">
                                    Status Stok Produk di Outlet Terpilih
                                </h2>
                                <p class="text-xs text-gray-500">
                                    Dihitung berdasarkan Faktur Penjualan: <span class="font-semibold text-emerald-800">Sisa Stok = Stok Masuk - Total Terjual</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span id="label-loading-stok" class="hidden text-xs text-emerald-600 animate-pulse font-medium">
                                ⏳ Mengambil data stok...
                            </span>
                            <button type="button" onclick="toggleTabelStok()" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 shadow-sm transition">
                                <span id="btn-toggle-stok-icon">👁️</span>
                                <span id="btn-toggle-stok-text">Lihat Tabel Stok</span>
                            </button>
                        </div>
                    </div>

                    <div id="container-tabel-stok" class="hidden p-6 border-t border-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-gray-200 text-gray-500 uppercase bg-gray-50/70">
                                        <th class="py-2.5 px-3">Nama Produk</th>
                                        <th class="py-2.5 px-3 text-center">Stok Masuk</th>
                                        <th class="py-2.5 px-3 text-center">Terjual (Faktur)</th>
                                        <th class="py-2.5 px-3 text-center font-bold text-gray-800">Sisa Stok</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-stok-outlet" class="divide-y divide-gray-100">
                                    {{-- Diisi secara dinamis oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Rincian Produk Faktur Penjualan --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Rincian Faktur Penjualan</h2>
                            <p class="text-xs text-gray-500">Jumlah kuantitas dibatasi maksimal sesuai sisa stok yang tersedia di outlet.</p>
                        </div>
                        <button type="button" onclick="tambahBarisProduk()"
                            class="rounded-xl bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                            + Tambah Produk
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[650px] w-full text-left border-collapse" id="tabel_produk">
                            <thead>
                                <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                                    <th class="py-2 px-2 w-5/12">Produk & Sisa Stok</th>
                                    <th class="py-2 px-2 w-2/12">Jumlah (Pcs)</th>
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
                                                <option value="{{ $p->produk_id }}" data-nama="{{ $p->nama_produk }}">
                                                    {{ $p->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number" name="jumlah[]" min="1" value="1" required
                                            oninput="hitungSubtotal(this)"
                                            class="jumlah-input w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <span class="info-stok-baris block text-[11px] text-gray-500 mt-1 font-medium">Sisa: -</span>
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

                    {{-- Subtotal & Grand Total --}}
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <div class="text-right">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Nilai Faktur</span>
                            <div id="grand_total" class="text-2xl font-bold text-emerald-600">Rp 0</div>
                            <input type="hidden" name="total_harga" id="input_total_harga" value="0">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('penjualan.index') }}"
                            class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" id="btn-submit-penjualan"
                            class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Faktur Penjualan
                        </button>
                    </div>
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

        // Inisialisasi data stok awal dari controller untuk outlet default
        let currentStokMap = @json($initialStok ?? []);

        function toggleTabelStok() {
            const container = document.getElementById('container-tabel-stok');
            const btnText = document.getElementById('btn-toggle-stok-text');
            const btnIcon = document.getElementById('btn-toggle-stok-icon');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                btnText.textContent = 'Sembunyikan Tabel';
                if (btnIcon) btnIcon.textContent = '✖️';
            } else {
                container.classList.add('hidden');
                btnText.textContent = 'Lihat Tabel Stok';
                if (btnIcon) btnIcon.textContent = '👁️';
            }
        }

        function ambilWilayahOutlet() {
            const select = document.getElementById('outlet_id');
            if (!select) return null;
            const outletId = Number(select.value);

            const outlet = daftarOutlet.find(
                item => Number(item.outlet_id) === outletId
            );

            return outlet ? Number(outlet.wilayah_id) : null;
        }

        function ambilHargaProduk(produkId, optionEl) {
            const wilayahId = ambilWilayahOutlet();
            const tanggal = document.getElementById('tanggal_penjualan').value;

            if (!produkId) {
                return 0;
            }

            let hargaItem = null;
            if (wilayahId && tanggal) {
                hargaItem = daftarHarga.find(item =>
                    Number(item.produk_id) === Number(produkId) &&
                    Number(item.wilayah_id) === wilayahId &&
                    (!item.tanggal_mulai || item.tanggal_mulai <= tanggal) &&
                    (!item.tanggal_berakhir || item.tanggal_berakhir >= tanggal)
                );
            }

            if (hargaItem && Number(hargaItem.harga_produk) > 0) {
                return Number(hargaItem.harga_produk);
            }

            if (optionEl && optionEl.dataset && optionEl.dataset.basePrice) {
                const bp = Number(optionEl.dataset.basePrice);
                if (bp > 0) return bp;
            }

            return 0;
        }

        function RupiahFormat(angka) {
            return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
        }

        // Render Tabel Ringkasan Stok Outlet
        function renderTabelStok() {
            const tbody = document.getElementById('tbody-stok-outlet');
            tbody.innerHTML = '';

            const listProduk = Object.values(currentStokMap);
            if (listProduk.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-gray-500">Pilih outlet untuk menampilkan stok</td></tr>`;
                return;
            }

            listProduk.forEach(item => {
                let badgeStatus = '';
                if (item.status === 'habis') {
                    badgeStatus = '<span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">Habis (0 pcs)</span>';
                } else if (item.status === 'menipis') {
                    badgeStatus = `<span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 text-[11px] font-semibold text-yellow-800">Menipis (${item.sisa_stok} pcs)</span>`;
                } else {
                    badgeStatus = `<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Tersedia (${item.sisa_stok} pcs)</span>`;
                }

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="py-2.5 px-3">
                        <div class="font-semibold text-gray-800">${item.nama_produk}</div>
                        <div class="text-[10px] text-gray-400">${item.kategori}</div>
                    </td>
                    <td class="py-2.5 px-3 text-center text-gray-600 font-medium">${item.stok_masuk} pcs</td>
                    <td class="py-2.5 px-3 text-center text-gray-600 font-medium">${item.total_terjual} pcs</td>
                    <td class="py-2.5 px-3 text-center font-bold ${item.sisa_stok <= 0 ? 'text-red-600' : 'text-emerald-700'}">${item.sisa_stok} pcs</td>
                    <td class="py-2.5 px-3 text-center">${badgeStatus}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Ambil Data Stok Outlet via AJAX saat dropdown outlet berubah
        async function gantiOutlet() {
            updateAlamatOutlet();

            const select = document.getElementById('outlet_id');
            const outletId = select.value;
            const loadingLabel = document.getElementById('label-loading-stok');

            if (!outletId) {
                currentStokMap = {};
                renderTabelStok();
                perbaruiDropdownProdukDanStok();
                return;
            }

            loadingLabel.classList.remove('hidden');

            try {
                const response = await fetch(`/penjualan/stok-outlet/${outletId}`);
                if (response.ok) {
                    const data = await response.json();
                    currentStokMap = data.stok || {};
                } else {
                    console.error('Gagal mengambil data stok');
                }
            } catch (err) {
                console.error('Error saat fetch stok:', err);
            } finally {
                loadingLabel.classList.add('hidden');
                renderTabelStok();
                perbaruiDropdownProdukDanStok();
            }
        }

        // Update Alamat Outlet
        function updateAlamatOutlet() {
            const select = document.getElementById('outlet_id');
            const option = select.options[select.selectedIndex];
            const kotak = document.getElementById('box-alamat');
            const teks = document.getElementById('teks_alamat');

            if (select.value && option) {
                teks.textContent = option.dataset.alamat || 'Alamat Tidak Tersedia!';
                kotak.classList.remove('hidden');
            } else {
                kotak.classList.add('hidden');
                teks.textContent = '-';
            }
        }

        // Perbarui teks dropdown dan info sisa stok produk di baris formulir
        function perbaruiDropdownProdukDanStok() {
            const wilayahId = ambilWilayahOutlet();

            document.querySelectorAll('.produk-select').forEach(sel => {
                const currentVal = sel.value;
                Array.from(sel.options).forEach(opt => {
                    if (!opt.value) return;
                    const pId = Number(opt.value);
                    const h = ambilHargaProduk(pId, opt);
                    const rawNama = opt.dataset.nama || opt.text.split(' — ')[0];
                    const stokItem = currentStokMap[pId] || null;
                    const sisa = stokItem ? Number(stokItem.sisa_stok) : 0;

                    let labelStok = '';
                    if (stokItem) {
                        if (sisa <= 0) {
                            labelStok = ' [STOK HABIS (0 pcs)]';
                            opt.disabled = true;
                        } else {
                            labelStok = ` [Sisa Stok: ${sisa} pcs]`;
                            opt.disabled = false;
                        }
                    }

                    if (h > 0 && wilayahId) {
                        opt.text = `${rawNama} — ${RupiahFormat(h)}${labelStok}`;
                    } else {
                        opt.text = `${rawNama}${labelStok}`;
                    }
                });

                // Jika produk terpilih sebelumnya ternyata stoknya 0, reset
                if (currentVal && currentStokMap[currentVal] && currentStokMap[currentVal].sisa_stok <= 0) {
                    sel.value = '';
                }
            });

            // Hitung ulang semua subtotal baris
            document.querySelectorAll('#container_produk .item-row').forEach(row => {
                hitungSubtotal(row.querySelector('.produk-select'));
            });
        }

        // Hitung Subtotal per Baris & Validasi Batas Sisa Stok
        function hitungSubtotal(element) {
            const row = element.closest('.item-row');
            if (!row) return;

            const selectProduk = row.querySelector('.produk-select');
            const inputJumlah = row.querySelector('.jumlah-input');
            const inputHarga = row.querySelector('.harga-input');
            const teksSubtotal = row.querySelector('.subtotal-text');
            const infoStokBaris = row.querySelector('.info-stok-baris');

            const produkId = selectProduk.value;
            const wilayahId = ambilWilayahOutlet();
            const selectedOpt = selectProduk.options[selectProduk.selectedIndex];

            // Tentukan sisa stok untuk produk ini
            let sisaStok = 0;
            if (produkId && currentStokMap[produkId]) {
                sisaStok = Number(currentStokMap[produkId].sisa_stok || 0);
                inputJumlah.max = sisaStok;
                if (sisaStok <= 0) {
                    infoStokBaris.innerHTML = `<span class="text-red-600 font-bold">Stok Habis (0 pcs)</span>`;
                } else if (sisaStok <= 5) {
                    infoStokBaris.innerHTML = `Sisa: <span class="text-yellow-600 font-bold">${sisaStok} pcs (Menipis)</span>`;
                } else {
                    infoStokBaris.innerHTML = `Sisa: <span class="text-emerald-600 font-bold">${sisaStok} pcs</span>`;
                }
            } else {
                infoStokBaris.textContent = 'Sisa: -';
                inputJumlah.removeAttribute('max');
            }

            // Jika produk dipilih tapi outlet belum dipilih
            if (produkId && !wilayahId) {
                inputHarga.value = 0;
                teksSubtotal.textContent = '⚠ Pilih outlet dulu';
                teksSubtotal.classList.add('text-red-500');
                teksSubtotal.classList.remove('text-emerald-600', 'text-orange-500');
                validasiSemuaBaris();
                hitungHargaTotal();
                return;
            }

            const harga = ambilHargaProduk(produkId, selectedOpt);
            const jumlah = Math.max(0, Number(inputJumlah.value || 0));
            const subtotal = harga * jumlah;

            inputHarga.value = harga;

            if (produkId && wilayahId && harga === 0) {
                teksSubtotal.textContent = '⚠ Harga belum ada';
                teksSubtotal.classList.add('text-orange-500');
                teksSubtotal.classList.remove('text-emerald-600', 'text-red-500');
            } else {
                teksSubtotal.textContent = RupiahFormat(subtotal);
                teksSubtotal.classList.remove('text-red-500', 'text-orange-500');
                teksSubtotal.classList.add('text-emerald-600');
            }

            validasiSemuaBaris();
            hitungHargaTotal();
        }

        // Validasi Apakah Total Kuantitas per Produk Melebihi Sisa Stok di Outlet
        function validasiSemuaBaris() {
            const akumulasi = {};
            let adaKelebihan = false;
            let pesanError = '';

            document.querySelectorAll('#container_produk .item-row').forEach(row => {
                const selectProduk = row.querySelector('.produk-select');
                const inputJumlah = row.querySelector('.jumlah-input');
                const pId = selectProduk.value;
                const qty = Number(inputJumlah.value || 0);

                if (pId) {
                    akumulasi[pId] = (akumulasi[pId] || 0) + qty;
                }
            });

            document.querySelectorAll('#container_produk .item-row').forEach(row => {
                const selectProduk = row.querySelector('.produk-select');
                const inputJumlah = row.querySelector('.jumlah-input');
                const pId = selectProduk.value;

                if (pId && currentStokMap[pId]) {
                    const sisa = Number(currentStokMap[pId].sisa_stok || 0);
                    const totalMinta = akumulasi[pId] || 0;

                    if (totalMinta > sisa || sisa <= 0) {
                        adaKelebihan = true;
                        inputJumlah.classList.add('border-red-500', 'bg-red-50');
                        pesanError = `Kuantitas untuk "${currentStokMap[pId].nama_produk}" (${totalMinta} pcs) melampaui sisa stok (${sisa} pcs).`;
                    } else {
                        inputJumlah.classList.remove('border-red-500', 'bg-red-50');
                    }
                }
            });

            const alertEl = document.getElementById('alert-stok-kurang');
            const pesanEl = document.getElementById('pesan-stok-kurang');
            const submitBtn = document.getElementById('btn-submit-penjualan');

            if (adaKelebihan) {
                pesanEl.textContent = pesanError;
                alertEl.classList.remove('hidden');
                submitBtn.disabled = true;
            } else {
                alertEl.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }

        // Hitung Grand Total Seluruh Baris
        function hitungHargaTotal() {
            let total = 0;
            document.querySelectorAll('#container_produk .item-row').forEach(function(row) {
                const selectProduk = row.querySelector('.produk-select');
                const inputJumlah = row.querySelector('.jumlah-input');
                const option = selectProduk.options[selectProduk.selectedIndex];

                const harga = ambilHargaProduk(selectProduk.value, option);
                const jumlah = Math.max(0, Number(inputJumlah.value || 0));
                total += harga * jumlah;
            });

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
            const inputJumlah = barisBaru.querySelector('.jumlah-input');
            inputJumlah.value = 1;
            inputJumlah.classList.remove('border-red-500', 'bg-red-50');
            barisBaru.querySelector('.harga-input').value = 0;
            barisBaru.querySelector('.info-stok-baris').textContent = 'Sisa: -';

            const subtotalEl = barisBaru.querySelector('.subtotal-text');
            subtotalEl.textContent = RupiahFormat(0);
            subtotalEl.classList.remove('text-red-500', 'text-orange-500');
            subtotalEl.classList.add('text-emerald-600');

            container.appendChild(barisBaru);
            perbaruiDropdownProdukDanStok();
            hitungHargaTotal();
        }

        // Hapus Baris Produk
        function hapusBaris(button) {
            const container = document.getElementById('container_produk');
            const rows = container.querySelectorAll('.item-row');

            if (rows.length > 1) {
                button.closest('.item-row').remove();
                validasiSemuaBaris();
                hitungHargaTotal();
            } else {
                alert('Faktur minimal harus memiliki 1 produk');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateAlamatOutlet();
            renderTabelStok();
            perbaruiDropdownProdukDanStok();

            document.getElementById('tanggal_penjualan')
                .addEventListener('change', function() {
                    perbaruiDropdownProdukDanStok();
                });

            hitungHargaTotal();
        });
    </script>
@endsection
