<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = Kategori::orderBy('nama_kategori')->paginate(10);

        return view('kategori.index', compact('kategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'deskripsi_kategori' => 'nullable|string',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        return view('kategori.show', compact('kategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori', 'nama_kategori')
                    ->ignore($kategori->kategori_id, 'kategori_id'),
            ],
            'deskripsi_kategori' => [
                'nullable',
                'string',
            ],
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        $namaKategori = $kategori->nama_kategori;

        try {
            DB::transaction(function () use ($kategori) {
                // 1. Ambil semua ID produk dalam kategori ini
                $produkIds = DB::table('produk')
                    ->where('kategori_id', $kategori->kategori_id)
                    ->pluck('produk_id');

                if ($produkIds->isNotEmpty()) {
                    // 2. Hapus detail transaksi yang terkait produk-produk ini
                    DB::table('transaksi_detail')->whereIn('produk_id', $produkIds)->delete();

                    // 3. Hapus penetapan harga produk-produk ini
                    DB::table('harga_produk')->whereIn('produk_id', $produkIds)->delete();

                    // 4. Hapus produk-produk ini
                    DB::table('produk')->whereIn('produk_id', $produkIds)->delete();
                }

                // 5. Hapus kategori
                $kategori->delete();
            });

            return redirect()
                ->route('kategori.index')
                ->with('success', "Kategori '{$namaKategori}' beserta produk terkait berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('kategori.index')
                ->with('error', "Gagal menghapus kategori '{$namaKategori}': " . $e->getMessage());
        }
    }
}
