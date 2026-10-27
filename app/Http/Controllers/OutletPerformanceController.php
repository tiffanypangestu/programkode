<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\OutletPerformance;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutletPerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $performa = OutletPerformance::with(['outlet', 'wilayah'])->orderByDesc('periode_data')->paginate(10);

        return view('outlet_performance.index', compact('performa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlet = Outlet::orderBy('nama_outlet')->get();
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('outlet_performance.create', compact('outlet', 'wilayah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'wilayah_id' => ['required', 'integer',
                Rule::exists('outlet', 'outlet_id')
                    ->where('wilayah_id', $request->input('wilayah_id'))
                    ->where('outlet_id', $request->input('outlet_id')),
            ],
            'periode_data' => ['required', 'string', 'max:255'],
            'omzet' => ['required', 'numeric', 'min:0'],
            'frekuensi_transaksi' => ['required', 'integer', 'min:0'],
            'jumlah_produk_terjual' => ['required', 'integer', 'min:0'],
            'growth' => ['required', 'numeric'],
            'kontribusi' => ['required', 'numeric', 'min:0'],
        ], [
            'wilayah_id.exists' => 'Wilayah harus sesuai dengan wilayah outlet yang dipilih.',
        ]);

        $request->validate([
            'periode_data' => [
                Rule::unique('outlet_performance', 'periode_data')->where('outlet_id', $request->input('outlet_id')),
            ],
        ],
            [
                'periode_data.unique' => 'Data performa outlet untuk periode tersebut sudah tersedia',
            ]);

        OutletPerformance::create($validated);

        return redirect()->route('outlet-performance.index')->with('success', 'Data Performa Outlet berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OutletPerformance $outletPerformance)
    {
        $outletPerformance->load(['outlet', 'wilayah']);

        return view('outlet_performance.show', compact('outletPerformance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutletPerformance $outletPerformance)
    {
        $outlet = Outlet::orderBy('nama_outlet')->get();
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return view('outlet_performance.edit', compact('outletPerformance', 'outlet', 'wilayah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutletPerformance $outletPerformance)
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlet,outlet_id'],
            'wilayah_id' => ['required', 'integer',
                Rule::exists('outlet', 'outlet_id')
                    ->where('wilayah_id', $request->input('wilayah_id'))
                    ->where('outlet_id', $request->input('outlet_id')),
            ],
            'periode_data' => ['required', 'string', 'max:255',
                Rule::unique('outlet_performance', 'periode_data')
                    ->where('outlet_id', $request->input('outlet_id'))
                    ->ignore(
                        $outletPerformance->performa_id,
                        'performa_id'
                    ),
            ],
            'omzet' => ['required', 'numeric', 'min:0'],
            'frekuensi_transaksi' => ['required', 'integer', 'min:0'],
            'jumlah_produk_terjual' => ['required', 'integer', 'min:0'],
            'growth' => ['required', 'numeric'],
            'kontribusi' => ['required', 'numeric', 'min:0'],
        ], [
            'wilayah_id.exists' => 'Wilayah harus sesuai dengan wilayah outlet yang dipilih.',
            'periode_data.unique' => 'Data performa outlet untuk periode tersebut sudah tersedia.',
        ]);

        $outletPerformance->update($validated);

        return redirect()->route('outlet-performance.index')->with('success', 'Data Performa Outlet berhasil di Update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutletPerformance $outletPerformance)
    {
        // Jangan hapus jika data sudah digunakan dalam hasil clustering.
        if (
            $outletPerformance->hasilCluster()->exists()
            || $outletPerformance->detailPerforma()->exists()
        ) {
            return redirect()
                ->route('outlet-performance.index')
                ->with(
                    'error',
                    'Data tidak dapat dihapus karena sudah digunakan dalam analisis performa.'
                );
        }

        $outletPerformance->delete();

        return redirect()->route('outlet-performance.index')->with('success', 'Data Performa Outlet berhasil dihapus!');
    }
}
