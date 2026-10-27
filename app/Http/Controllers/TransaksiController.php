<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Transaksi;
use App\Models\Wilayah;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['wilayah', 'outlet', 'karyawan']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nomor_transaksi', 'like', "%{$search}%");
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        $transaksi = $query->orderByDesc('tanggal')
            ->orderByDesc('transaksi_id')
            ->paginate(10)
            ->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();
        $outlet = Outlet::orderBy('nama_outlet')->get();
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('transaksi.create', compact('wilayah', 'outlet', 'karyawan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wilayah_id' => ['required', 'exists:wilayah,wilayah_id'],
            'outlet_id' => ['required', 'exists:outlet,outlet_id'],
            'karyawan_id' => ['required', 'exists:karyawan,karyawan_id'],
            'nomor_transaksi' => ['required', 'string', 'max:255', 'max:50'],
            'tanggal' => ['required', 'date'],
            'total_transaksi' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tipe_transaksi' => ['nullable', 'string', 'max:255', 'max:50'],
        ]);

        $apakahSesuaiOutlet = Outlet::where('outlet_id', $validated['outlet_id'])->where('wilayah_id', $validated['wilayah_id'])->exists();

        if (! $apakahSesuaiOutlet) {
            return back()->withErrors(['outlet_id' => 'Outlet yang dipilih tidak sesuai dengan wilayah yang dipilih.'])->withInput();
        }

        Transaksi::create($validated);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['wilayah', 'outlet', 'karyawan', 'transaksiDetail', 'pembayaran']);

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();
        $outlet = Outlet::orderBy('nama_outlet')->get();
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('transaksi.edit', compact('transaksi', 'wilayah', 'outlet', 'karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'wilayah_id' => ['required', 'integer', 'exists:wilayah,wilayah_id'],
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'karyawan_id' => ['required', 'integer', 'exists:karyawan,karyawan_id'],
            'nomor_transaksi' => ['required', 'string', 'max:50'],
            'tanggal' => ['required', 'date'],
            'total_transaksi' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tipe_transaksi' => ['nullable', 'string', 'max:255', 'max:50'],
        ]);

        $apakahSesuaiOutlet = Outlet::where('outlet_id', $validated['outlet_id'])->where('wilayah_id', $validated['wilayah_id'])->exists();

        if (! $apakahSesuaiOutlet) {
            return back()->withErrors(['outlet_id' => 'Outlet yang dipilih tidak sesuai dengan wilayah yang dipilih.'])->withInput();
        }

        $transaksi->update($validated);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        try {
            $transaksi->delete();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
        } catch (QueryException $e) {
            return redirect()->route('transaksi.index')->with('error', 'Transaksi tidak dapat dihapus karena masih memiliki detail transaksi atau pembayaran.');
        }
    }
}
