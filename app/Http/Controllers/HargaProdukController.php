<?php

namespace App\Http\Controllers;

use App\Models\HargaProduk;
use App\Models\Produk;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class HargaProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::with('hargaProduk')->orderBy('nama_produk')->paginate(10);
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('harga_produk.index', compact('produk', 'wilayah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('harga_produk.create', compact('produk', 'wilayah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => ['required', 'exists:produk,produk_id'],
            'wilayah_id' => ['required', 'exists:wilayah,wilayah_id'],
            'harga_produk' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        HargaProduk::create($validated);

        return redirect()->route('harga-produk.index')->with('success', 'Harga produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HargaProduk $hargaProduk)
    {
        $hargaProduk->load(['produk', 'wilayah']);

        return view('harga_produk.show', compact('hargaProduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HargaProduk $hargaProduk)
    {
        $produk = Produk::orderBy('nama_produk')->get();
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('harga_produk.edit', compact('hargaProduk', 'produk', 'wilayah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HargaProduk $hargaProduk)
    {
        $validated = $request->validate([
            'produk_id' => ['required', 'exists:produk,produk_id'],
            'wilayah_id' => ['required', 'exists:wilayah,wilayah_id'],
            'harga_produk' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $hargaProduk->update($validated);

        return redirect()->route('harga-produk.index')->with('success', 'Harga produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HargaProduk $hargaProduk)
    {
        try {
            DB::transaction(function () use ($hargaProduk) {
                // 1. Hapus transaksi detail yang menggunakan harga_produk_id ini
                DB::table('transaksi_detail')->where('harga_produk_id', $hargaProduk->harga_produk_id)->delete();

                // 2. Hapus penetapan harga produk
                $hargaProduk->delete();
            });

            return redirect()->route('harga-produk.index')->with('success', 'Harga produk berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('harga-produk.index')->with(
                'error',
                'Gagal menghapus harga produk: ' . $e->getMessage()
            );
        }
    }
}
