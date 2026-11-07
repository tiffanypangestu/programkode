<?php

namespace App\Http\Controllers;

use App\Exports\TemplatePenjualanExport;
use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\StokOutlet;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    /**
     * Periksa apakah role user termasuk yang diizinkan
     */
    private function authorizeRoles(array $roles): void
    {
        $user = Auth::user() ?? request()->user();
        $namaRole = strtolower($user?->role?->nama_role ?? '');

        $roleAliases = [
            'bc' => 'beauty consultant',
            'spg' => 'sales promotion girl',
            'beauty consultant' => 'bc',
            'sales promotion girl' => 'spg',
        ];

        $allowedRoles = array_map('strtolower', $roles);

        $isAllowed = in_array($namaRole, $allowedRoles, true) ||
                     (isset($roleAliases[$namaRole]) && in_array($roleAliases[$namaRole], $allowedRoles, true));

        abort_unless($isAllowed, 403, 'Anda tidak memiliki hak akses!');
    }

    /**
     * Hitung sisa stok dan total terjual per outlet untuk semua produk
     */
    public function getOutletStockCalculation(int $outletId, ?string $excludeTransaksiNomor = null): array
    {
        $produks = Produk::with('kategori')->orderBy('nama_produk')->get();

        $stokMasukMap = DB::table('stok_outlet')
            ->where('outlet_id', $outletId)
            ->pluck('stok_masuk', 'produk_id');

        $stokMinMap = DB::table('stok_outlet')
            ->where('outlet_id', $outletId)
            ->pluck('stok_minimum', 'produk_id');

        $terjualQuery = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
            ->where('transaksi.outlet_id', $outletId);

        if ($excludeTransaksiNomor) {
            $terjualQuery->where('transaksi.nomor_transaksi', '!=', $excludeTransaksiNomor);
        }

        $terjualMap = $terjualQuery
            ->select('transaksi_detail.produk_id', DB::raw('SUM(transaksi_detail.jumlah) as total_terjual'))
            ->groupBy('transaksi_detail.produk_id')
            ->pluck('total_terjual', 'produk_id');

        $result = [];
        foreach ($produks as $p) {
            $masuk = isset($stokMasukMap[$p->produk_id]) ? (int) $stokMasukMap[$p->produk_id] : 0;
            $terjual = isset($terjualMap[$p->produk_id]) ? (int) $terjualMap[$p->produk_id] : 0;
            $sisa = max(0, $masuk - $terjual);
            $min = isset($stokMinMap[$p->produk_id]) ? (int) $stokMinMap[$p->produk_id] : 5;

            $status = 'tersedia';
            if ($sisa <= 0) {
                $status = 'habis';
            } elseif ($sisa <= $min) {
                $status = 'menipis';
            }

            $result[$p->produk_id] = [
                'produk_id' => $p->produk_id,
                'nama_produk' => $p->nama_produk,
                'kategori' => $p->kategori?->nama_kategori ?? '-',
                'stok_masuk' => $masuk,
                'total_terjual' => $terjual,
                'sisa_stok' => $sisa,
                'stok_minimum' => $min,
                'status' => $status,
            ];
        }

        return $result;
    }

    /**
     * Endpoint API JSON untuk mengambil status stok outlet secara dinamis
     */
    public function getStokOutletJson(Outlet $outlet)
    {
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG', 'Beauty Consultant', 'Sales Promotion Girl']);

        $data = $this->getOutletStockCalculation($outlet->outlet_id);

        return response()->json([
            'outlet_id' => $outlet->outlet_id,
            'nama_outlet' => $outlet->nama_outlet,
            'stok' => $data,
        ]);
    }

    public function index(Request $request)
    {

        // sorting
        // $sort = in_array(strtolower($)

        $user = Auth::user();
        $query = Penjualan::with(['outlet', 'karyawan']);

        $namaRole = $user->role?->nama_role ?? '';
        $karyawan = $user->karyawan;

        if (in_array($namaRole, ['Sales Promotion Girl', 'SPG'])) {
            if ($karyawan) {
                $query->where('karyawan_id', $karyawan->karyawan_id);
            }
        } elseif ($namaRole === 'Supervisor') {
            if ($user->wilayah_id ?? null) {
                $query->whereHas('outlet', function ($q) use ($user) {
                    $q->where('wilayah_id', $user->wilayah_id);
                });
            }
        } elseif (in_array($namaRole, ['Beauty Consultant', 'BC'])) {
            if ($karyawan && $karyawan->outlets()->exists()) {
                $query->whereIn('outlet_id', $karyawan->outlets()->pluck('outlet.outlet_id'));
            } elseif ($karyawan) {
                $query->where('karyawan_id', $karyawan->karyawan_id);
            }
        }

        // Filter Periode (Bulan dan Tahun) menggunakan Flatpickr
        // Default adalah bulan saat ini (Y-m), kecuali jika user memilih menampilkan semua
        $tampilkanSemua = $request->has('semua');
        $periode = $request->input('periode');

        if (! $tampilkanSemua) {
            if (! $periode) {
                $periode = date('Y-m'); // Default bulan ini, misal '2026-11'
            }

            if (preg_match('/^\d{4}-\d{2}$/', $periode)) {
                [$tahun, $bulan] = explode('-', $periode);
                $query->whereYear('tanggal_penjualan', (int) $tahun)
                      ->whereMonth('tanggal_penjualan', (int) $bulan);
            }
        } else {
            $periode = '';
        }

        // Ringkasan omzet dan jumlah transaksi pada filter yang aktif
        $totalOmzet = (clone $query)->sum('total_harga');
        $totalTransaksi = (clone $query)->count();

        $penjualan = $query->latest('tanggal_penjualan')
                           ->latest('penjualan_id')
                           ->paginate(10)
                           ->appends($request->query());

        // Ambil rincian produk melalui nomor_transaksi di tabel transaksi
        $nomorTransaksis = $penjualan->getCollection()->map(function ($item) {
            return 'TR-'.$item->penjualan_id;
        });

        $transaksiMap = Transaksi::whereIn('nomor_transaksi', $nomorTransaksis)
            ->pluck('transaksi_id', 'nomor_transaksi');

        $details = TransaksiDetail::with('produk')
            ->whereIn('transaksi_id', $transaksiMap->values())
            ->get()
            ->groupBy('transaksi_id');

        $penjualan->getCollection()->transform(function ($item) use ($transaksiMap, $details) {
            $trxId = $transaksiMap->get('TR-'.$item->penjualan_id);
            $itemDetails = $trxId ? $details->get($trxId, collect()) : collect();
            $item->setRelation('transaksiDetail', $itemDetails);

            return $item;
        });

        return view('penjualan.index', compact(
            'penjualan',
            'periode',
            'tampilkanSemua',
            'totalOmzet',
            'totalTransaksi',
            'namaRole'
        ));
    }

    public function create()
    {
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG', 'Beauty Consultant', 'Sales Promotion Girl']);

        $user = Auth::user();
        $namaRole = strtolower($user->role?->nama_role ?? '');

        // Auto-create karyawan jika belum terhubung
        $karyawanLogin = Karyawan::firstOrCreate(
            ['user_id' => $user->user_id],
            [
                'role_id' => $user->role_id,
                'nama_karyawan' => $user->nama_lengkap ?? $user->name,
                'jabatan' => $user->role?->nama_role ?? 'Sales Promotion Girl',
                'nomor_telepon' => $user->nomor_telepon,
                'tahun_masuk' => (int) date('Y'),
            ]
        );

        // Admin & Supervisor dapat lihat semua outlet
        $isAdmin = in_array($namaRole, ['admin', 'supervisor']);

        if ($isAdmin) {
            $outlet = Outlet::with('wilayah')->orderBy('nama_outlet')->get();
            $outletTugasKhusus = false;
        } else {
            // Ambil outlet yang ditugaskan ke karyawan login
            $outlet = $karyawanLogin->outlets()->with('wilayah')->orderBy('nama_outlet')->get();
            $outletTugasKhusus = $outlet->isNotEmpty();

            // Jika belum ada penugasan khusus, tampilkan semua outlet aktif
            if ($outlet->isEmpty()) {
                $outlet = Outlet::with('wilayah')->orderBy('nama_outlet')->get();
            }
        }

        $produk = Produk::with('kategori')->orderBy('nama_produk')->get();

        $daftarHarga = DB::table('harga_produk')->orderByDesc('harga_produk_id')->get()
            ->unique(function ($item) {
                return $item->produk_id.'-'.$item->wilayah_id;
            })->values();

        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        // Tentukan outlet yang terpilih secara default
        $defaultOutletId = old('outlet_id');
        if (! $defaultOutletId) {
            if ($outlet->count() === 1) {
                $defaultOutletId = $outlet->first()->outlet_id;
            } elseif ($outlet->isNotEmpty()) {
                $defaultOutletId = $outlet->first()->outlet_id;
            }
        }

        $initialStok = $defaultOutletId ? $this->getOutletStockCalculation((int) $defaultOutletId) : [];

        return view('penjualan.create', compact('outlet', 'produk', 'karyawan', 'daftarHarga', 'outletTugasKhusus', 'karyawanLogin', 'initialStok', 'defaultOutletId', 'namaRole'));
    }

    public function store(Request $request)
    {
        $this->authorizeRoles([
            'Admin', 'Supervisor', 'BC', 'SPG',
            'Beauty Consultant', 'Sales Promotion Girl',
        ]);

        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'tanggal_penjualan' => ['required', 'date'],
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'integer', 'exists:produk,produk_id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
            'status_penjualan' => ['nullable', 'string', 'max:30'],
        ]);

        // 0. Validasi Ketersediaan Sisa Stok Outlet
        $stokData = $this->getOutletStockCalculation((int) $validated['outlet_id']);

        $totalMintaPerProduk = [];
        foreach ($validated['produk_id'] as $index => $produkId) {
            $qty = (int) $validated['jumlah'][$index];
            $totalMintaPerProduk[$produkId] = ($totalMintaPerProduk[$produkId] ?? 0) + $qty;
        }

        foreach ($totalMintaPerProduk as $produkId => $totalMinta) {
            $stokItem = $stokData[$produkId] ?? null;
            $sisaStok = $stokItem['sisa_stok'] ?? 0;
            $namaProduk = $stokItem['nama_produk'] ?? "Produk ID {$produkId}";

            if ($totalMinta > $sisaStok) {
                return back()->withInput()->withErrors([
                    'jumlah' => "Kuantitas untuk produk \"{$namaProduk}\" ({$totalMinta} pcs) melebihi sisa stok yang tersedia di outlet ini (Tersedia: {$sisaStok} pcs).",
                ]);
            }
        }

        // 1. Hitung total harga

        $outlet = Outlet::findOrFail($validated['outlet_id']);
        $total_harga = 0;
        $tanggalPenjualan = Carbon::parse($validated['tanggal_penjualan'])->toDateString();
        $items_to_insert = [];

        // hitung total dan siapkan array rincian produk

        foreach ($validated['produk_id'] as $index => $produkId) {
            $quantity = (int) $validated['jumlah'][$index];
            $hargaProduk = DB::table('harga_produk')->where('produk_id', $produkId)->where('wilayah_id', $outlet->wilayah_id)->where(function ($query) use ($tanggalPenjualan) {
                $query->whereNull('tanggal_mulai')->orWhereDate('tanggal_mulai', '<=', $tanggalPenjualan);
            })
                ->where(function ($query) use ($tanggalPenjualan) {
                    $query->whereNull('tanggal_berakhir')->orWhereDate('tanggal_berakhir', '>=', $tanggalPenjualan);
                })
                ->orderByDesc('harga_produk_id')
                ->first();

            if (! $hargaProduk) {
                // Cari harga dasar produk atau dari input form
                $produkRow = DB::table('produk')->where('produk_id', $produkId)->first();
                $basePrice = (float) ($produkRow->harga_produk ?? 0);
                if ($basePrice <= 0 && isset($request->harga_satuan[$index])) {
                    $basePrice = (float) $request->harga_satuan[$index];
                }
                if ($basePrice <= 0) {
                    $basePrice = 50000;
                }

                // Otomatis buat entri harga_produk untuk wilayah outlet ini agar foreign key terpenuhi
                $hargaProdukId = DB::table('harga_produk')->insertGetId([
                    'produk_id' => $produkId,
                    'wilayah_id' => $outlet->wilayah_id,
                    'harga_produk' => $basePrice,
                    'tanggal_mulai' => '2026-01-01',
                    'tanggal_berakhir' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $hargaSatuan = $basePrice;
            } else {
                $hargaSatuan = (float) $hargaProduk->harga_produk;
                $hargaProdukId = $hargaProduk->harga_produk_id;
            }

            $total_harga += $hargaSatuan * $quantity;

            $items_to_insert[] = [
                'produk_id' => $produkId,
                'jumlah' => $quantity,
                'harga_produk_id' => $hargaProdukId,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $hargaSatuan * $quantity,
            ];
        }

        // 2. Buat otomatis data login
        $user = Auth::user();

        $karyawan = Karyawan::firstOrCreate(
            ['user_id' => $user->user_id],
            [
                'role_id' => $user->role_id,
                'nama_karyawan' => $user->nama_lengkap ?? $user->name,
                'jabatan' => $user->role?->nama_role ?? 'Sales Promotion Girl',
                'nomor_telepon' => $user->nomor_telepon,
                'tahun_masuk' => (int) date('Y'),
            ]
        );

        // Selalu hubungkan outlet ke karyawan jika belum ada penugasan
        if ($karyawan->outlets()->count() === 0) {
            $karyawan->outlets()->syncWithoutDetaching([$validated['outlet_id']]);
        }

        $karyawanId = $karyawan->karyawan_id;

        // 3. Simpan data penjualan (transaksi_id akan diisi setelah transaksi dibuat)
        $penjualan = Penjualan::create([
            'outlet_id' => $validated['outlet_id'],
            'karyawan_id' => $karyawanId,
            'tanggal_penjualan' => $validated['tanggal_penjualan'],
            'total_harga' => $total_harga,
            'status_penjualan' => $validated['status_penjualan'] ?? 'selesai',
        ]);

        // 4. Buat row di tabel `transaksi` agar transaksi_detail bisa menyimpan foreign key
        $transaksi = Transaksi::create([
            'wilayah_id' => $outlet->wilayah_id,
            'outlet_id' => $outlet->outlet_id,
            'karyawan_id' => $karyawanId,
            'nomor_transaksi' => 'TR-'.$penjualan->penjualan_id,
            'tanggal' => $validated['tanggal_penjualan'],
            'total_transaksi' => $total_harga,
            'tipe_transaksi' => 'penjualan',
        ]);

        // 5. Rincian produk ke tabel transaksi_detail (menggunakan transaksi_id)
        foreach ($items_to_insert as $item) {
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->transaksi_id,
                'produk_id' => $item['produk_id'],
                'jumlah' => $item['jumlah'],
                'harga_produk_id' => $item['harga_produk_id'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil disimpan!');
    }

    public function show(Penjualan $penjualan)
    {

        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG']);

        $penjualan->load(['outlet', 'karyawan']);

        $transaksiDetails = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
            ->join('produk', 'produk.produk_id', '=', 'transaksi_detail.produk_id')
            ->where('transaksi.nomor_transaksi', 'TR-'.$penjualan->penjualan_id)
            ->select('transaksi_detail.*', 'produk.nama_produk')
            ->get();

        return view('penjualan.show', compact('penjualan', 'transaksiDetails'));
    }

    public function edit(Penjualan $penjualan)
    {
        $this->authorizeRoles([
            'Admin', 'Supervisor', 'BC', 'SPG',
            'Beauty Consultant', 'Sales Promotion Girl',
        ]);

        $outlet = Outlet::orderBy('nama_outlet')->get();
        $produk = Produk::orderBy('nama_produk')->get();
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        $daftarHarga = DB::table('harga_produk')
            ->orderByDesc('harga_produk_id')
            ->get();

        $transaksi = Transaksi::where('nomor_transaksi', 'TR-'.$penjualan->penjualan_id)->first();
        if ($transaksi) {
            $transaksiDetails = TransaksiDetail::where('transaksi_id', $transaksi->transaksi_id)->get();
        } else {
            $transaksiDetails = TransaksiDetail::where('transaksi_id', $penjualan->penjualan_id)->get();
        }

        $harga_produk = $produk->pluck('harga_produk', 'produk_id')->toJson();

        return view('penjualan.edit', compact(
            'penjualan',
            'outlet',
            'karyawan',
            'produk',
            'daftarHarga',
            'transaksiDetails',
            'harga_produk'
        ));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $this->authorizeRoles([
            'Admin', 'Supervisor', 'BC', 'SPG',
            'Beauty Consultant', 'Sales Promotion Girl',
        ]);

        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'tanggal_penjualan' => ['required', 'date'],
            'karyawan_id' => ['nullable', 'integer', 'exists:karyawan,karyawan_id'],
            'status_penjualan' => ['nullable', 'string', 'max:30'],
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['required', 'integer', 'exists:produk,produk_id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
        ]);

        // 0. Validasi Ketersediaan Sisa Stok Outlet (Kecualikan transaksi faktur ini sendiri)
        $stokData = $this->getOutletStockCalculation((int) $validated['outlet_id'], 'TR-'.$penjualan->penjualan_id);

        $totalMintaPerProduk = [];
        foreach ($validated['produk_id'] as $index => $produkId) {
            $qty = (int) $validated['jumlah'][$index];
            $totalMintaPerProduk[$produkId] = ($totalMintaPerProduk[$produkId] ?? 0) + $qty;
        }

        foreach ($totalMintaPerProduk as $produkId => $totalMinta) {
            $stokItem = $stokData[$produkId] ?? null;
            $sisaStok = $stokItem['sisa_stok'] ?? 0;
            $namaProduk = $stokItem['nama_produk'] ?? "Produk ID {$produkId}";

            if ($totalMinta > $sisaStok) {
                return back()->withInput()->withErrors([
                    'jumlah' => "Kuantitas untuk produk \"{$namaProduk}\" ({$totalMinta} pcs) melebihi sisa stok yang tersedia di outlet ini (Tersedia: {$sisaStok} pcs).",
                ]);
            }
        }

        $outlet = Outlet::findOrFail($validated['outlet_id']);
        $total_harga = 0;
        $tanggalPenjualan = Carbon::parse($validated['tanggal_penjualan'])->toDateString();
        $items_to_insert = [];

        foreach ($validated['produk_id'] as $index => $produkId) {
            $quantity = (int) $validated['jumlah'][$index];
            $hargaProduk = DB::table('harga_produk')
                ->where('produk_id', $produkId)
                ->where('wilayah_id', $outlet->wilayah_id)
                ->where(function ($query) use ($tanggalPenjualan) {
                    $query->whereNull('tanggal_mulai')->orWhereDate('tanggal_mulai', '<=', $tanggalPenjualan);
                })
                ->where(function ($query) use ($tanggalPenjualan) {
                    $query->whereNull('tanggal_berakhir')->orWhereDate('tanggal_berakhir', '>=', $tanggalPenjualan);
                })
                ->orderByDesc('harga_produk_id')
                ->first();

            if (! $hargaProduk) {
                $formHarga = isset($request->harga_satuan[$index]) ? (float) $request->harga_satuan[$index] : 0;
                $defaultProdukHarga = (float) (DB::table('produk')->where('produk_id', $produkId)->value('harga_produk') ?? 0);
                $fallbackHarga = $formHarga > 0 ? $formHarga : $defaultProdukHarga;

                if ($fallbackHarga > 0) {
                    $hargaSatuan = $fallbackHarga;
                    $hargaProdukId = null;
                } else {
                    $namaProduk = DB::table('produk')->where('produk_id', $produkId)->value('nama_produk') ?? "ID: {$produkId}";

                    return back()->withInput()->withErrors([
                        'produk_id' => "Harga produk \"{$namaProduk}\" belum tersedia untuk wilayah outlet yang dipilih. Silakan tambahkan harga terlebih dahulu.",
                    ]);
                }
            } else {
                $hargaSatuan = (float) $hargaProduk->harga_produk;
                $hargaProdukId = $hargaProduk->harga_produk_id;
            }

            $subtotal = $hargaSatuan * $quantity;
            $total_harga += $subtotal;

            $items_to_insert[] = [
                'produk_id' => $produkId,
                'jumlah' => $quantity,
                'harga_produk_id' => $hargaProdukId,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
            ];
        }

        DB::transaction(function () use ($penjualan, $validated, $outlet, $total_harga, $items_to_insert) {
            $karyawanId = $validated['karyawan_id'] ?? $penjualan->karyawan_id;

            // 1. Update Penjualan
            $penjualan->update([
                'outlet_id' => $validated['outlet_id'],
                'karyawan_id' => $karyawanId,
                'tanggal_penjualan' => $validated['tanggal_penjualan'],
                'total_harga' => $total_harga,
                'status_penjualan' => $validated['status_penjualan'] ?? $penjualan->status_penjualan ?? 'selesai',
            ]);

            // 2. Cari atau Buat Transaksi
            $nomorTransaksi = 'TR-'.$penjualan->penjualan_id;
            $transaksi = Transaksi::firstOrCreate(
                ['nomor_transaksi' => $nomorTransaksi],
                [
                    'wilayah_id' => $outlet->wilayah_id,
                    'outlet_id' => $outlet->outlet_id,
                    'karyawan_id' => $karyawanId,
                    'tanggal' => $validated['tanggal_penjualan'],
                    'total_transaksi' => $total_harga,
                    'tipe_transaksi' => 'penjualan',
                ]
            );

            $transaksi->update([
                'wilayah_id' => $outlet->wilayah_id,
                'outlet_id' => $outlet->outlet_id,
                'karyawan_id' => $karyawanId,
                'tanggal' => $validated['tanggal_penjualan'],
                'total_transaksi' => $total_harga,
                'tipe_transaksi' => 'penjualan',
            ]);

            // 3. Update Detail Transaksi
            TransaksiDetail::where('transaksi_id', $transaksi->transaksi_id)->delete();

            foreach ($items_to_insert as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_produk_id' => $item['harga_produk_id'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        });

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG']);

        Transaksi::where('nomor_transaksi', 'TR-'.$penjualan->penjualan_id)->delete();
        $penjualan->delete();

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function downloadPDF(Penjualan $penjualan)
    {
        $this->authorizeRoles(['Admin', 'BC', 'Supervisor', 'SPG', 'Beauty Consultant', 'Sales Promotion Girl']);

        $penjualan->load(['outlet', 'karyawan']);

        // Ambil detail produk lewat tabel transaksi (nomor_transaksi = 'TR-{id}')
        $transaksiDetails = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
            ->join('produk', 'produk.produk_id', '=', 'transaksi_detail.produk_id')
            ->where('transaksi.nomor_transaksi', 'TR-'.$penjualan->penjualan_id)
            ->select(
                'transaksi_detail.*',
                'produk.nama_produk'
            )
            ->get();

        $pdf = Pdf::loadView('penjualan.pdf', compact('penjualan', 'transaksiDetails'))->setPaper('a4', 'Portrait');

        return $pdf->stream('Faktur Penjualan-'.$penjualan->penjualan_id.'.pdf');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('contoh_data_penjualan_2025.xlsx');
        if (file_exists($filePath)) {
            return response()->download($filePath, 'contoh_data_penjualan_2025.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return Excel::download(new TemplatePenjualanExport, 'template_import_penjualan.xlsx');
    }
}
