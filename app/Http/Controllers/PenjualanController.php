<?php

namespace App\Http\Controllers;

use App\Exports\TemplatePenjualanExport;
use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Penjualan;
use App\Models\Produk;
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
        $user = request()->user();
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

    public function index()
    {
        $user = Auth::user();
        $query = Penjualan::with(['outlet', 'karyawan']);

        $namaRole = $user->role?->nama_role ?? '';
        if (in_array($namaRole, ['Sales Promotion Girl', 'SPG'])) {
            $query->where('karyawan_id', $user->karyawan_id);
        } elseif ($namaRole === 'Supervisor') {
            $query->whereHas('outlet', function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            });
        } elseif (in_array($namaRole, ['Beauty Consultant', 'BC'])) {
            $query->whereIn('outlet_id', $user->outletids ?? []);
        }

        $penjualan = $query->latest()->paginate(10);

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

        return view('penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG']);
        $outlet = Outlet::orderBy('nama_outlet')->get();
        $produk = Produk::orderBy('nama_produk')->get();

        $daftarHarga = DB::table('harga_produk')->orderByDesc('harga_produk_id')->get()
            ->unique(function ($item) {
                return $item->produk_id.'-'.$item->wilayah_id;
            })->values();

        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('penjualan.create', compact('outlet', 'produk', 'karyawan', 'daftarHarga'));
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
                $namaProduk = DB::table('produk')->where('produk_id', $produkId)->value('nama_produk') ?? "ID: {$produkId}";

                return back()->withInput()->withErrors([
                    'produk_id' => "Harga produk \"{$namaProduk}\" belum tersedia untuk wilayah outlet yang dipilih. Silakan tambahkan harga terlebih dahulu.",
                ]);
            }
            $hargaSatuan = $hargaProduk ? (float) $hargaProduk->harga_produk : 0;
            $total_harga += (float) $hargaProduk->harga_produk * $quantity;

            $items_to_insert[] = [
                'produk_id' => $produkId,
                'jumlah' => $quantity,
                'harga_produk_id' => $hargaProduk->harga_produk_id,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $hargaSatuan * $quantity,
            ];
        }

        // 2. Cari data karyawan berdasarkan user yang login
        $user = Auth::user();

        $karyawan = Karyawan::where('user_id', $user->user_id)->first();

        if (! $karyawan) {
            return back()
                ->withInput()
                ->withErrors([
                    'karyawan_id' => 'Akun Anda belum terhubung dengan data karyawan. Hubungkan akun melalui data karyawan terlebih dahulu.',
                ]);
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
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG']);

        $outlet = Outlet::orderBy('nama_outlet')->get();
        $produk = Produk::orderBy('nama_produk')->get();
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();
        $transaksi = TransaksiDetail::orderBy('harga_satuan')->get();
        $subtotal = TransaksiDetail::orderBy('subtotal')->get();

        // harga produk json file
        $harga_produk = $produk->pluck('harga_satuan', 'produk_id')->toJson();

        return view('penjualan.edit', compact('penjualan', 'outlet', 'karyawan', 'produk', 'transaksi', 'subtotal', 'harga_produk'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $this->authorizeRoles(['Admin', 'Supervisor', 'BC', 'SPG']);

        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'karyawan_id' => ['required', 'integer', 'exists:karyawan,karyawan_id'],
            'tanggal_penjualan' => ['required', 'date'],
            'total_harga' => ['required', 'numeric', 'min:0'],
            'status_penjualan' => ['nullable', 'string', 'max:30'],
        ]);

        $validated['status_penjualan'] = $validated['status_penjualan'] ?? 'selesai';

        $penjualan->update($validated);

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
        return Excel::download(new TemplatePenjualanExport, 'template_import_penjualan.xlsx');
    }
}
