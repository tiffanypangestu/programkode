<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query();

        // logika filter pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_wilayah', 'like', '%'.$search.'%')
                    ->orWhere('deskripsi_wilayah', 'like', '%'.$search.'%');
            });
        }

        $wilayah = $query->orderBy('nama_wilayah')->paginate(3)->withQueryString();

        return view('wilayah.index', compact('wilayah'));
    }

    public function create()
    {
        return view('wilayah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_wilayah' => ['required', 'string', 'max:255', 'unique:wilayah,nama_wilayah'],
            'deskripsi_wilayah' => ['nullable', 'string'],
        ]);
        Wilayah::create($validated);

        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function show(Wilayah $wilayah)
    {
        return view('wilayah.show', compact('wilayah'));
    }

    public function edit(Wilayah $wilayah)
    {
        return view('wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $validated = $request->validate([
            'nama_wilayah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wilayah', 'nama_wilayah')->ignore($wilayah->wilayah_id, 'wilayah_id'),
            ],
            'deskripsi_wilayah' => ['nullable', 'string'],
        ]);
        $wilayah->update($validated);

        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        $namaWilayah = $wilayah->nama_wilayah;

        try {
            DB::transaction(function () use ($wilayah) {
                // 1. Ambil outlet yang terdaftar di wilayah ini
                $outletIds = DB::table('outlet')
                    ->where('wilayah_id', $wilayah->wilayah_id)
                    ->pluck('outlet_id');

                // 2. Ambil transaksi yang terdaftar di wilayah ini atau dari outlet-outlet tersebut
                $transaksiIds = DB::table('transaksi')
                    ->where('wilayah_id', $wilayah->wilayah_id)
                    ->orWhereIn('outlet_id', $outletIds)
                    ->pluck('transaksi_id');

                // 3. Hapus detail transaksi
                if ($transaksiIds->isNotEmpty()) {
                    DB::table('transaksi_detail')->whereIn('transaksi_id', $transaksiIds)->delete();
                    DB::table('transaksi')->whereIn('transaksi_id', $transaksiIds)->delete();
                }

                // 4. Hapus penjualan dan data performa outlet
                if ($outletIds->isNotEmpty()) {
                    DB::table('penjualan')->whereIn('outlet_id', $outletIds)->delete();
                    DB::table('outlet_performance')->whereIn('outlet_id', $outletIds)->delete();
                    DB::table('outlet')->whereIn('outlet_id', $outletIds)->delete();
                }

                // 5. Hapus penetapan harga produk di wilayah ini
                DB::table('harga_produk')->where('wilayah_id', $wilayah->wilayah_id)->delete();

                // 6. Hapus wilayah
                $wilayah->delete();
            });

            return redirect()->route('wilayah.index')->with('success', "Wilayah '{$namaWilayah}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('wilayah.index')->with(
                'error',
                "Gagal menghapus wilayah '{$namaWilayah}': ".$e->getMessage()
            );
        }
    }
}
