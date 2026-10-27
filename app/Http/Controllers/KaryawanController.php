<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawan = Karyawan::with(['user', 'role'])->orderBy('karyawan_id')->paginate(10);

        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Role::orderBy('nama_role')->get();
        $users = User::whereDoesntHave('karyawan')->orderBy('name')->get();

        return view('karyawan.create', compact('role', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,user_id'],
            'role_id' => ['required', 'integer', 'exists:role,role_id'],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'tahun_masuk' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ]);
        Karyawan::create($validated);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['user', 'role']);

        return view('karyawan.show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        $role = Role::orderBy('nama_role')->get();
        $users = User::where(function ($query) use ($karyawan) {
            $query->whereDoesntHave('karyawan')
                ->orWhere('user_id', $karyawan->user_id);
        })->orderBy('name')->get();

        return view('karyawan.edit', compact('karyawan', 'role', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users, user_id', Rule::unique('karyawan', 'user_id')->ignore($karyawan->karyawan_id, 'karyawan_id')],
            'role_id' => ['required', 'integer', 'exists:role,role_id'],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'tahun_masuk' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ]);
        $karyawan->update($validated);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        $namaKaryawan = $karyawan->nama_karyawan;

        try {
            DB::transaction(function () use ($karyawan) {
                // 1. Ambil transaksi yang dicatat atas nama karyawan ini
                $transaksiIds = DB::table('transaksi')
                    ->where('karyawan_id', $karyawan->karyawan_id)
                    ->pluck('transaksi_id');

                // 2. Hapus detail transaksi
                if ($transaksiIds->isNotEmpty()) {
                    DB::table('transaksi_detail')->whereIn('transaksi_id', $transaksiIds)->delete();
                    DB::table('transaksi')->whereIn('transaksi_id', $transaksiIds)->delete();
                }

                // 3. Hapus penjualan yang dicatat atas nama karyawan ini
                DB::table('penjualan')->where('karyawan_id', $karyawan->karyawan_id)->delete();

                // 4. Hapus data karyawan
                $karyawan->delete();
            });

            return redirect()->route('karyawan.index')->with('success', "Karyawan '{$namaKaryawan}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('karyawan.index')->with(
                'error',
                "Gagal menghapus karyawan '{$namaKaryawan}': " . $e->getMessage()
            );
        }
    }
}
