<?php

namespace App\Http\Controllers;

use App\Models\Promosi;
use Illuminate\Http\Request;

class PromosiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promosi = Promosi::orderByDesc('periode_mulai')->paginate(10);

        return view('promosi.index', compact('promosi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('promosi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_promosi' => ['required', 'string', 'max:255'],
            'deskripsi_promosi' => ['nullable', 'string'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
            'kategori_produk' => ['nullable', 'string', 'max:255'],
        ],
            [
                'nama_promosi.required' => 'Nama promosi wajib diisi.',
                'periode_mulai.required' => 'Tanggal mulai promosi wajib diisi.',
                'periode_selesai.required' => 'Tanggal selesai promosi wajib diisi.',
                'periode_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            ]);

        Promosi::create($validated);

        return redirect()->route('promosi.index')->with('success', 'Data promosi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promosi $promosi)
    {
        $promosi->load('jadwalPromosi');

        return view('promosi.show', compact('promosi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promosi $promosi)
    {
        return view('promosi.edit', compact('promosi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promosi $promosi)
    {
        $validated = $request->validate([
            'nama_promosi' => ['required', 'string', 'max:255'],
            'deskripsi_promosi' => ['nullable', 'string'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
            'kategori_produk' => ['nullable', 'string', 'max:255'],
        ],
            [
                'nama_promosi.required' => 'Nama promosi wajib diisi.',
                'periode_mulai.required' => 'Tanggal mulai promosi wajib diisi.',
                'periode_selesai.required' => 'Tanggal selesai promosi wajib diisi.',
                'periode_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            ]);

        $promosi->update($validated);

        return redirect()->route('promosi.index')->with('success', 'Data promosi berhasil ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promosi $promosi)
    {
        // kalau masih ada jadwal
        if ($promosi->jadwalPromosi()->exists()) {
            return redirect()->route('promosi.index')->with('error', 'Promosi tidak dapat dihapus');
        }

        $promosi->delete();

        return redirect()->route('promosi.index')->with('success', 'Data promosi berhasil dihapus!');
    }
}
