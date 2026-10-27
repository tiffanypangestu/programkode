<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outlet = Outlet::with('wilayah')->orderBy('outlet_id')->paginate(10);

        return view('outlet.index', compact('outlet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('outlet.create', compact('wilayah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wilayah_id' => ['required', 'exists:wilayah,wilayah_id'],
            'kode_outlet' => ['required', 'string', 'max:255', 'unique:outlet,kode_outlet'],
            'nama_outlet' => ['required', 'string', 'max:255'],
            'alamat_outlet' => ['nullable', 'string'],
            'tipe_outlet' => ['nullable', 'string', 'max:255'],
            'status_outlet' => ['nullable', 'string', 'max:255'],
        ]);
        Outlet::create($validated);

        return redirect()->route('outlet.index')->with('success', 'Outlet berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Outlet $outlet)
    {
        $outlet->load('wilayah');

        return view('outlet.show', compact('outlet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Outlet $outlet)
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('outlet.edit', compact('outlet', 'wilayah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'wilayah_id' => ['required', 'integer', 'exists:wilayah,wilayah_id'],
            'kode_outlet' => ['required', 'string', 'max:255',
                Rule::unique('outlet', 'kode_outlet')
                    ->ignore($outlet->outlet_id, 'outlet_id'),
            ],
            'nama_outlet' => ['required', 'string', 'max:255'],
            'alamat_outlet' => ['nullable', 'string', 'max:255'],
            'tipe_outlet' => ['nullable', 'string', 'max:255'],
            'status_outlet' => ['nullable', 'string', 'max:255'],
        ]);

        $outlet->update($validated);

        return redirect()
            ->route('outlet.index')
            ->with('success', 'Outlet berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlet $outlet)
    {
        $outletNama = $outlet->nama_outlet;

        try {
            DB::transaction(function () use ($outlet) {
                // 1. Ambil transaksi yang terkait dengan outlet ini
                $transaksiIds = DB::table('transaksi')
                    ->where('outlet_id', $outlet->outlet_id)
                    ->pluck('transaksi_id');

                // 2. Hapus detail transaksi terkait outlet ini
                if ($transaksiIds->isNotEmpty()) {
                    DB::table('transaksi_detail')->whereIn('transaksi_id', $transaksiIds)->delete();
                }

                // 3. Hapus data transaksi terkait outlet ini
                DB::table('transaksi')->where('outlet_id', $outlet->outlet_id)->delete();

                // 4. Hapus data penjualan terkait outlet ini
                DB::table('penjualan')->where('outlet_id', $outlet->outlet_id)->delete();

                // 5. Hapus riwayat performa outlet jika ada
                DB::table('outlet_performance')->where('outlet_id', $outlet->outlet_id)->delete();

                // 6. Hapus outlet
                $outlet->delete();
            });

            return redirect()->route('outlet.index')->with('success', "Outlet '{$outletNama}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('outlet.index')->with(
                'error',
                "Gagal menghapus outlet '{$outletNama}': " . $e->getMessage()
            );
        }
    }
}
