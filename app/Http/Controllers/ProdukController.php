<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // inisialisasi query
        $query = Produk::with(['kategori', 'hargaProduk']);

        // pencarian nama atau deskripsi produk
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', '%'.$search.'%')
                    ->orWhere('deskripsi_produk', 'like', '%'.$search.'%');
            });
        }

        // filter kategori yang valid dan tepat
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // total semua produk di database
        $total_produk = Produk::count();

        // query dengan pagination dan menjaga query string filter
        $produk = $query->orderBy('nama_produk', 'asc')->paginate(10)->withQueryString();

        // master data kategori untuk pilihan filter
        $kategori_list = Kategori::orderBy('nama_kategori')->get();

        return view('produk.index', compact('produk', 'kategori_list', 'total_produk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('produk.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'deskripsi_produk' => 'nullable|string',
        ]);

        // Tambahkan harga_produk default 0
        $validated['harga_produk'] = 0;

        Produk::create($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        $produk->load(['kategori', 'hargaProduk']);

        return view('produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('produk.edit', compact('produk', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'deskripsi_produk' => 'nullable|string',
        ]);

        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $namaProduk = $produk->nama_produk;

        try {
            DB::transaction(function () use ($produk) {
                // 1. Hapus transaksi detail yang menggunakan produk ini
                DB::table('transaksi_detail')->where('produk_id', $produk->produk_id)->delete();

                // 2. Hapus penetapan harga produk di semua wilayah
                DB::table('harga_produk')->where('produk_id', $produk->produk_id)->delete();

                // 3. Hapus produk
                $produk->delete();
            });

            return redirect()->route('produk.index')->with('success', "Produk '{$namaProduk}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('produk.index')->with(
                'error',
                "Gagal menghapus produk '{$namaProduk}': ".$e->getMessage()
            );
        }
    }
}
