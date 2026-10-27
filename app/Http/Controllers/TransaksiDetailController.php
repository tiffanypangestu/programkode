<?php

namespace App\Http\Controllers;

use App\Models\HargaProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil seluruh detail transaksi dari penjualan BC, SPG, Supervisor, Admin, maupun Import
        $transaksiDetail = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->join('outlet', 'transaksi.outlet_id', '=', 'outlet.outlet_id')
            ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.produk_id')
            ->leftJoin('karyawan', 'transaksi.karyawan_id', '=', 'karyawan.karyawan_id')
            ->leftJoin('users', 'karyawan.user_id', '=', 'users.user_id')
            ->leftJoin('role', function ($join) {
                $join->on('karyawan.role_id', '=', 'role.role_id')
                    ->orOn('users.role_id', '=', 'role.role_id');
            })
            ->leftJoin('spg', 'users.user_id', '=', 'spg.user_id')
            ->leftJoin('bc', 'users.user_id', '=', 'bc.user_id')
            ->select(
                'transaksi_detail.transaksi_detail_id',
                'transaksi_detail.transaksi_id',
                'transaksi.nomor_transaksi',
                'transaksi.tanggal as tanggal_transaksi',
                'outlet.nama_outlet',
                'produk.nama_produk',
                'transaksi_detail.jumlah',
                'transaksi_detail.harga_satuan',
                'transaksi_detail.subtotal',
                DB::raw('COALESCE(karyawan.nama_karyawan, spg.nama_spg, bc.nama_bc, users.name, "-") as nama_petugas'),
                DB::raw('COALESCE(role.nama_role, karyawan.jabatan, "Karyawan") as jabatan_petugas')
            )
            ->orderBy('transaksi_detail.transaksi_detail_id', 'DESC')
            ->paginate(10);

        return view('transaksi_detail.index', compact('transaksiDetail'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transaksi = Transaksi::with('wilayah')->orderByDesc('tanggal')->get();
        $produk = Produk::orderBy('nama_produk')->get();
        $hargaProduk = HargaProduk::with(['produk', 'wilayah'])->orderBy('produk_id')->get();

        return view('transaksi_detail.create', compact('transaksi', 'produk', 'hargaProduk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'transaksi_id' => ['required', 'integer', 'exists:transaksi,transaksi_id'],
            'produk_id' => ['required', 'integer', 'exists:produk,produk_id'],
            'harga_produk_id' => ['required', 'integer', 'exists:harga_produk,harga_produk_id'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:42596028'],
        ]);

        DB::transaction(function () use ($validate) {
            $transaksi = Transaksi::where('transaksi_id', $validate['transaksi_id'])->lockForUpdate()->firstOrFail();
            $hargaProduk = HargaProduk::where('harga_produk_id', $validate['harga_produk_id'])->firstOrFail();

            // produk harus sesuai dengan harga
            if ((int) $hargaProduk->produk_id !== (int) $validate['produk_id']) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga yang dipilih bukan harga untuk produk tersebut',
                ]);
            }

            // harga produk harus sesuai dengan wilayah
            if ((int) $hargaProduk->wilayah_id !== (int) $transaksi->wilayah_id) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga produk tidak sesuai dengan wilayah transaksi',
                ]);
            }

            // periksa masa berlaku apabila harga ditentukan
            $tanggal = $transaksi->tanggal;

            if ($hargaProduk->tanggal_mulai && $tanggal < $hargaProduk->tanggal_mulai->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga produk belum berlaku pada tanggal transaksi',
                ]);
            }

            if ($hargaProduk->tanggal_berakhir && $tanggal > $hargaProduk->tanggal_berakhir->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Masa berlaku tanggal produk sudah selesai.',
                ]);
            }

            $harga_satuan = (float) $hargaProduk->harga_produk;
            $jumlah = (int) $validate['jumlah'];
            $subtotal = round($harga_satuan * $jumlah, 2);

            TransaksiDetail::create([
                'transaksi_id' => $transaksi->transaksi_id,
                'produk_id' => $validate['produk_id'],
                'harga_produk_id' => $hargaProduk->harga_produk_id,
                'jumlah' => $jumlah,
                'harga_satuan' => $harga_satuan,
                'subtotal' => $subtotal,
            ]);

            // hitung total berdasarkan transaksi secara keseluruhan
            $transaksi->total_transaksi = TransaksiDetail::where('transaksi_id', $transaksi->transaksi_id)->sum('subtotal');
            $transaksi->save();
        });

        return redirect()->route('transaksi-detail.index')->with('success', 'Detail transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TransaksiDetail $transaksiDetail)
    {
        $transaksiDetail->load(['transaksi.outlet', 'transaksi.karyawan.role', 'produk', 'hargaProduk']);

        return view('transaksi_detail.show', compact('transaksiDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransaksiDetail $transaksiDetail)
    {
        $transaksi = Transaksi::with('wilayah')->orderByDesc('tanggal')->get();
        $produk = Produk::orderBy('nama_produk')->get();
        $hargaProduk = HargaProduk::with(['produk', 'wilayah'])->orderBy('produk_id')->get();

        return view('transaksi_detail.edit', compact('transaksiDetail', 'transaksi', 'produk', 'hargaProduk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransaksiDetail $transaksiDetail)
    {
        $validated = $request->validate([
            'transaksi_id' => ['required', 'integer', 'exists:transaksi,transaksi_id'],
            'produk_id' => ['required', 'integer', 'exists:produk,produk_id'],
            'harga_produk_id' => ['required', 'integer', 'exists:harga_produk,harga_produk_id'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:294682059'],
        ]);

        DB::transaction(function () use ($validated, $transaksiDetail) {
            $transaksi_lama = $transaksiDetail->transaksi_id;
            $id_transaksi = collect([
                $transaksi_lama, $validated['transaksi_id'],
            ])->unique()->sort()->values();

            $transaksi_kunci = Transaksi::whereIn('transaksi_id', $id_transaksi)->orderBy('transaksi_id')->lockForUpdate()->get()->keyBy('transaksi_id');

            $transaksi_new = $transaksi_kunci->get($validated['transaksi_id']);
            $hargaProduk = HargaProduk::where('harga_produk_id', $validated['harga_produk_id'])->firstOrFail();

            if ((int) $hargaProduk->produk_id !== (int) $validated['produk_id']) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga yang dipilih bukan harga untuk produk tersebut',
                ]);
            }

            if ((int) $hargaProduk->wilayah_id !== (int) $transaksi_new->wilayah_id) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga yang dipilih tidak sesuai dengan wilayah',
                ]);
            }

            $tanggal = $transaksi_new->tanggal;
            if ($hargaProduk->tanggal_mulai && $tanggal < $hargaProduk->tanggal_mulai->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga produk belum berlaku pada tanggal transaksi.',
                ]);
            }

            if ($hargaProduk->tanggal_berakhir && $tanggal < $hargaProduk->tanggal_berakhir->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'harga_produk_id' => 'Harga produk sudah tidak berlaku',
                ]);
            }

            $jumlah = (int) $validated['jumlah'];
            $hargaSatuan = (float) $hargaProduk->harga_produk;

            $transaksiDetail->update([
                'transaksi_id' => $transaksi_new->transaksi_id,
                'produk_id' => $validated['produk_id'],
                'harga_produk_id' => $hargaProduk->harga_produk_id,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => round($hargaSatuan * $jumlah, 2),
            ]);

            // update transaksi lama dan baru apabila ada perbedaan
            foreach ($transaksi_kunci as $itemTransaksi) {
                $itemTransaksi->total_transaksi = TransaksiDetail::where('transaksi_id', $itemTransaksi->transaksi_id)->sum('subtotal');
                $itemTransaksi->save();
            }
        });

        return redirect()->route('transaksi-detail.index')->with('success', 'Transaksi Detail berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransaksiDetail $transaksiDetail)
    {
        DB::transaction(function () use ($transaksiDetail) {
            $transaksi = Transaksi::where('transaksi_id', $transaksiDetail->transaksi_id)->lockForUpdate()->firstOrFail();
            $transaksiDetail->delete();
            $transaksi->total_transaksi = TransaksiDetail::where('transaksi_id', $transaksi->transaksi_id)->sum('subtotal');
            $transaksi->save();
        });

        return redirect()->route('transaksi-detail.index')->with('success', 'Detail transaksi berhasil dihapus');
    }
}
