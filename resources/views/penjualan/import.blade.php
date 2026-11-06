@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Judul --}}
    <div class="mb-6">
        <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
            &larr; Kembali ke Transaksi
        </a>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-gray-900">Import Penjualan Historis</h1>
        <p class="mt-2 text-sm text-gray-600">
            Unggah file Excel untuk memasukkan transaksi penjualan dan detail produk ke dalam sistem.
        </p>
    </div>

    {{-- Alert sukses --}}
    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Alert error session --}}
    @if (session('error'))
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Alert validasi --}}
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="mb-2 font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                Import belum berhasil. Periksa hal berikut:
            </p>
            <ul class="list-inside list-disc space-y-1 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <form action="{{ route('penjualan.import.store') }}" method="POST" enctype="multipart/form-data"
              id="importForm" class="space-y-6">
            @csrf

            {{-- Hidden karyawan_id --}}
            @if(!$karyawan->isEmpty())
                <input type="hidden" name="karyawan_id" value="{{ $karyawan->first()->karyawan_id }}">
            @endif

            {{-- Upload Zone --}}
            <div>
                <label for="file" class="mb-2 block text-sm font-semibold text-gray-700">
                    File Excel Penjualan
                </label>

                {{-- Drop zone --}}
                <div id="dropZone"
                     class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer"
                     onclick="document.getElementById('file').click()">
                    <div id="dropIcon" class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100">
                        <svg class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700" id="dropLabel">Drag & drop file Excel di sini</p>
                    <p class="mt-1 text-xs text-gray-500">atau klik untuk memilih file</p>
                    <div id="fileChosen" class="mt-3 hidden items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                        <svg class="h-4 w-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="fileChosenName" class="font-medium truncate max-w-xs"></span>
                        <span id="fileChosenSize" class="text-green-500 shrink-0"></span>
                    </div>
                </div>

                <input type="file" id="file" name="file" accept=".xlsx,.csv" required class="hidden">
                <p class="mt-2 text-xs text-gray-500">
                    Format yang didukung: XLSX dan CSV. Maksimal 10 MB.
                </p>
            </div>

            {{-- Download Template --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Unduh Contoh Template Excel
                </label>
                <a href="{{ route('penjualan.download-template') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-green-600 shadow-sm transition hover:bg-green-50 hover:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download Template
                </a>
            </div>

            {{-- Format Info --}}
            <div class="rounded-lg bg-gray-50 p-4">
                <h2 class="text-sm font-semibold text-gray-800">Format kolom Excel</h2>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Gunakan nama kolom:
                    <code class="rounded bg-gray-200 px-1 py-0.5 text-xs">
                        tanggal, nomor_transaksi, kode_outlet, nama_produk, jumlah, harga_satuan
                    </code>
                </p>
                <p class="mt-2 text-xs leading-5 text-gray-500">
                    Beberapa baris dengan nomor transaksi yang sama akan digabung menjadi satu transaksi.
                    Outlet, produk, harga historis, dan karyawan harus sudah tersedia sesuai referensi database.
                </p>
            </div>

            {{-- ═══════════════════════════════════════════════
                 PREVIEW SECTION — tampil setelah file dipilih
            ════════════════════════════════════════════════ --}}
            <div id="previewSection" class="hidden">
                <div class="border-t border-gray-100 pt-5">

                    {{-- Header preview --}}
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                                </svg>
                                Preview Data Excel
                            </h2>
                            <p class="mt-0.5 text-xs text-gray-500">
                                Menampilkan
                                <span id="previewCount" class="font-semibold text-gray-700">0</span>
                                baris •
                                <span id="previewTrx" class="font-semibold text-gray-700">0</span>
                                transaksi
                            </p>
                        </div>

                        {{-- Stat badges --}}
                        <div class="flex gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                                </svg>
                                <span id="statOutlet">0 Outlet</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span id="statTotal">Rp 0</span>
                            </span>
                        </div>
                    </div>

                    {{-- Search --}}
                    <div class="mb-3 relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                               placeholder="Cari produk, outlet, nomor transaksi..."
                               oninput="filterPreview()"
                               class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    {{-- Table --}}
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <div class="overflow-x-auto max-h-80 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="sticky top-0 z-10 bg-gray-50">
                                    <tr>
                                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No. Transaksi</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Outlet</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Harga Satuan</th>
                                        <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="previewBody" class="divide-y divide-gray-100 bg-white">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination bar --}}
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                        <span id="paginationInfo">-</span>
                        <div class="flex items-center gap-1" id="paginationBtns"></div>
                    </div>

                </div>
            </div>

            {{-- Error preview --}}
            <div id="previewError" class="hidden rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold mb-1">⚠ File tidak dapat dibaca</p>
                <p id="previewErrorMsg"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('transaksi.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        @disabled($karyawan->isEmpty())
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Import Penjualan
                </button>
            </div>

        </form>
    </div>
</div>

{{-- SheetJS --}}
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>

<script>
// ── State ──
let allRows = [];      // semua baris data (array of object)
let filtered = [];     // setelah search
let currentPage = 1;
const perPage = 20;

// ── Kolom yang diharapkan (lowercase) ──
const EXPECTED_COLS = ['tanggal','nomor_transaksi','kode_outlet','nama_produk','jumlah','harga_satuan'];

// ── Helpers ──
const $ = id => document.getElementById(id);
function fmt(n) {
    if (n == null || n === '') return '-';
    const num = parseFloat(String(n).replace(/[^0-9.\-]/g,''));
    if (isNaN(num)) return n;
    return 'Rp ' + num.toLocaleString('id-ID');
}
function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}
function formatFileSize(b) {
    if (b < 1024) return b + ' B';
    if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
    return (b/1048576).toFixed(1) + ' MB';
}

// ── Drag & Drop ──
const dropZone = $('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-indigo-400','bg-indigo-50'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-indigo-400','bg-indigo-50'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('border-indigo-400','bg-indigo-50');
    const f = e.dataTransfer.files[0];
    if (f) {
        $('file').files = e.dataTransfer.files;
        processFile(f);
    }
});

// ── File input change ──
$('file').addEventListener('change', function() {
    if (this.files[0]) processFile(this.files[0]);
});

// ── Proses File ──
function processFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['xlsx','xls','csv'].includes(ext)) {
        showError('Format tidak didukung. Gunakan .xlsx, .xls, atau .csv');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        showError('Ukuran file melebihi 10 MB.');
        return;
    }

    // Update UI: file chosen
    $('fileChosen').classList.remove('hidden');
    $('fileChosen').classList.add('flex');
    $('fileChosenName').textContent = file.name;
    $('fileChosenSize').textContent = '(' + formatFileSize(file.size) + ')';
    $('dropLabel').textContent = file.name;

    // Hide error
    $('previewError').classList.add('hidden');
    $('previewSection').classList.add('hidden');

    const reader = new FileReader();
    reader.onload = e => {
        try {
            const data = new Uint8Array(e.target.result);
            const wb = XLSX.read(data, { type: 'array', cellDates: true });
            const ws = wb.Sheets[wb.SheetNames[0]];
            const json = XLSX.utils.sheet_to_json(ws, { defval: '', raw: false });

            if (!json.length) { showError('Sheet pertama tidak memiliki data.'); return; }

            // Normalize keys to lowercase
            allRows = json.map(r => {
                const o = {};
                for (const k in r) o[k.toLowerCase().trim().replace(/\s+/g,'_')] = r[k];
                return o;
            });

            // Filter out empty rows (all expected cols empty)
            allRows = allRows.filter(r =>
                EXPECTED_COLS.some(c => r[c] !== undefined && String(r[c]).trim() !== '')
            );

            if (!allRows.length) { showError('Tidak ada data yang dapat dibaca. Pastikan header kolom sudah benar.'); return; }

            filtered = [...allRows];
            currentPage = 1;
            buildPreview();
        } catch(err) {
            showError('Gagal membaca file: ' + err.message);
        }
    };
    reader.readAsArrayBuffer(file);
}

// ── Build Preview ──
function buildPreview() {
    // Hitung stats
    const uniqueTrx    = new Set(allRows.map(r => r['nomor_transaksi']));
    const uniqueOutlet = new Set(allRows.map(r => r['kode_outlet']));
    let grandTotal = 0;
    allRows.forEach(r => {
        const j = parseFloat(r['jumlah']) || 0;
        const h = parseFloat(r['harga_satuan']) || 0;
        grandTotal += j * h;
    });

    $('previewCount').textContent = allRows.length.toLocaleString('id-ID');
    $('previewTrx').textContent = uniqueTrx.size.toLocaleString('id-ID');
    $('statOutlet').textContent = uniqueOutlet.size + ' Outlet';
    $('statTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

    renderTable();
    $('previewSection').classList.remove('hidden');
    // scroll ke preview
    $('previewSection').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Render table ──
function renderTable() {
    const start = (currentPage - 1) * perPage;
    const end   = Math.min(start + perPage, filtered.length);
    const page  = filtered.slice(start, end);
    const search = ($('searchInput').value || '').toLowerCase();

    function hl(val) {
        const s = escHtml(String(val ?? ''));
        if (!search) return s;
        const re = new RegExp('(' + search.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return s.replace(re, '<mark class="bg-yellow-200 rounded px-0.5">$1</mark>');
    }

    let html = '';
    if (!page.length) {
        html = `<tr><td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">
            <svg class="mx-auto mb-2 h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            Tidak ada data yang sesuai pencarian
        </td></tr>`;
    } else {
        // Group by nomor_transaksi untuk warna alternating per transaksi
        let trxColors = {};
        let colorIdx  = 0;
        filtered.forEach(r => {
            const k = r['nomor_transaksi'];
            if (k && trxColors[k] === undefined) trxColors[k] = colorIdx++ % 2;
        });

        page.forEach((row, i) => {
            const rowNum = start + i + 1;
            const j = parseFloat(row['jumlah']) || 0;
            const h = parseFloat(row['harga_satuan']) || 0;
            const sub = j * h;
            const bgClass = trxColors[row['nomor_transaksi']] === 0
                ? 'bg-white hover:bg-indigo-50'
                : 'bg-gray-50 hover:bg-indigo-50';

            html += `<tr class="${bgClass} transition-colors">
                <td class="whitespace-nowrap px-4 py-2.5 text-xs text-gray-400">${rowNum}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm font-medium text-gray-700">${hl(row['tanggal'])}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm text-indigo-700 font-mono">${hl(row['nomor_transaksi'])}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-700">${hl(row['kode_outlet'])}</td>
                <td class="px-4 py-2.5 text-sm text-gray-700 max-w-[200px] truncate" title="${escHtml(row['nama_produk'])}">${hl(row['nama_produk'])}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm text-right text-gray-700">${escHtml(row['jumlah'])}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm text-right text-gray-700">${fmt(row['harga_satuan'])}</td>
                <td class="whitespace-nowrap px-4 py-2.5 text-sm text-right font-semibold text-gray-800">${fmt(sub || '')}</td>
            </tr>`;
        });
    }
    $('previewBody').innerHTML = html;

    // Pagination info
    const total = filtered.length;
    $('paginationInfo').textContent = total > 0
        ? `Menampilkan ${start + 1}–${end} dari ${total.toLocaleString('id-ID')} baris`
        : 'Tidak ada data';

    // Pagination buttons
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    let btnHtml = '';
    const btnBase = 'inline-flex items-center justify-center rounded border px-2.5 py-1 text-xs font-medium transition-colors';
    const btnActive = 'border-indigo-500 bg-indigo-500 text-white';
    const btnNormal = 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50';
    const btnDisabled = 'border-gray-200 bg-gray-100 text-gray-300 cursor-not-allowed';

    btnHtml += `<button onclick="goPage(1)" ${currentPage===1?'disabled':''} class="${btnBase} ${currentPage===1?btnDisabled:btnNormal}">«</button>`;
    btnHtml += `<button onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled':''} class="${btnBase} ${currentPage===1?btnDisabled:btnNormal}">‹</button>`;

    // Page numbers (max 5 visible)
    let pStart = Math.max(1, currentPage - 2);
    let pEnd   = Math.min(totalPages, pStart + 4);
    pStart     = Math.max(1, pEnd - 4);
    for (let p = pStart; p <= pEnd; p++) {
        btnHtml += `<button onclick="goPage(${p})" class="${btnBase} ${p===currentPage?btnActive:btnNormal}">${p}</button>`;
    }

    btnHtml += `<button onclick="goPage(${currentPage+1})" ${currentPage===totalPages?'disabled':''} class="${btnBase} ${currentPage===totalPages?btnDisabled:btnNormal}">›</button>`;
    btnHtml += `<button onclick="goPage(${totalPages})" ${currentPage===totalPages?'disabled':''} class="${btnBase} ${currentPage===totalPages?btnDisabled:btnNormal}">»</button>`;
    $('paginationBtns').innerHTML = btnHtml;
}

function goPage(p) {
    const totalPages = Math.ceil(filtered.length / perPage);
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    renderTable();
}

// ── Filter / Search ──
function filterPreview() {
    const term = ($('searchInput').value || '').toLowerCase().trim();
    if (!term) {
        filtered = [...allRows];
    } else {
        filtered = allRows.filter(r =>
            EXPECTED_COLS.some(c => String(r[c] ?? '').toLowerCase().includes(term))
        );
    }
    currentPage = 1;
    renderTable();
}

// ── Show error ──
function showError(msg) {
    $('previewSection').classList.add('hidden');
    $('previewErrorMsg').textContent = msg;
    $('previewError').classList.remove('hidden');
}
</script>
@endsection
