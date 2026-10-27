<?php

namespace App\Http\Controllers;

use App\Models\Bc;
use App\Models\JadwalPromosi;
use App\Models\Promosi;
use App\Models\Spg;
use Illuminate\Http\Request;

class JadwalPromosiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwalPromosi = JadwalPromosi::with(['promosi'.'spg', 'bc'])->orderByDesc('tanggal_promosi')->paginate(10);

        return view('jadwal_promosi.index', compact('jadwalPromosi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $promosi = Promosi::orderBy('nama_promosi')->get();
        $spg = Spg::orderBy('nama_spg')->get();
        $bc = Bc::orderBy('nama_bc')->get();

        return view('jadwal_promosi.create', compact('promosi', 'spg', 'bc'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'promosi_id' => ['required', 'integer', 'exists:promosi,promosi_id'],
            'spg_id' => ['nullable', 'integer', 'exists:spg,spg_id'],
            'bc_id' => ['nullable', 'integer', 'exists:bc,bc_id'],
            'tanggal_promosi' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        JadwalPromosi::create($validated);

        return redirect()->route('jadwal-promosi.index')->with('success', 'Jadwal promosi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalPromosi $jadwalPromosi)
    {
        $jadwalPromosi->load(['promosi', 'spg', 'bc']);

        return view('jadwal_promosi.show', compact('jadwalPromosi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalPromosi $jadwalPromosi)
    {
        $promosi = Promosi::orderBy('nama_promosi')->get();
        $spg = Spg::orderBy('nama_spg')->get();
        $bc = Bc::orderBy('nama_bc')->get();

        return view('jadwal_promosi.edit', compact('jadwalPromosi', 'promosi', 'spg', 'bc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalPromosi $jadwalPromosi)
    {
        $validated = $request->validate([
            'promosi_id' => ['required', 'integer', 'exists:promosi,promosi_id'],
            'spg_id' => ['nullable', 'integer', 'exists:spg,spg_id'],
            'bc_id' => ['nullable', 'integer', 'exists:bc,bc_id'],
            'tanggal_promosi' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $jadwalPromosi->update($validated);

        return redirect()->route('jadwal-promosi.index')->with('success', 'Jadwal Promosi berhasil ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalPromosi $jadwalPromosi)
    {
        $jadwalPromosi->delete();

        return redirect()->route('jadwal-promosi.index')->with('success', 'Jadwal promosi berhasil dihapus!');
    }
}
