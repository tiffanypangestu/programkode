<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Spg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class SpgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spg = Spg::with('user')->orderBy('nama_spg')->paginate(10);

        return view('spg.index', compact('spg'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('spg.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_spg' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'nomor_telepon' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $roleSpg = Role::where('nama_role', 'Sales Promotion Girl')->first();

            $user = User::create([
                'name' => $validated['nama_spg'],
                'nama_lengkap' => $validated['nama_spg'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'role_id' => $roleSpg ? $roleSpg->role_id : 4,
            ]);

            Spg::create([
                'user_id' => $user->user_id,
                'nama_spg' => $validated['nama_spg'],
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            ]);
        });

        return redirect()->route('spg.index')->with('success', 'Data SPG berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Spg $spg)
    {
        $spg->load('user');

        return view('spg.show', compact('spg'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spg $spg)
    {
        $spg->load('user');

        return view('spg.edit', compact('spg'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Spg $spg)
    {
        $userId = $spg->user_id;

        $validated = $request->validate([
            'nama_spg' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId, 'user_id')],
            'password' => ['nullable', 'string', 'min:6'],
            'nomor_telepon' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $spg) {
            if ($spg->user) {
                $userData = [
                    'name' => $validated['nama_spg'],
                    'nama_lengkap' => $validated['nama_spg'],
                    'email' => $validated['email'],
                    'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $spg->user->update($userData);
            } else {
                $roleSpg = Role::where('nama_role', 'Sales Promotion Girl')->first();
                $user = User::create([
                    'name' => $validated['nama_spg'],
                    'nama_lengkap' => $validated['nama_spg'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'Spg12345!'),
                    'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                    'role_id' => $roleSpg ? $roleSpg->role_id : 4,
                ]);
                $spg->user_id = $user->user_id;
            }

            $spg->update([
                'user_id' => $spg->user_id,
                'nama_spg' => $validated['nama_spg'],
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            ]);
        });

        return redirect()->route('spg.index')->with('success', 'Data SPG berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spg $spg)
    {
        $namaSpg = $spg->nama_spg;

        try {
            DB::transaction(function () use ($spg) {
                // Hapus jadwal promosi terkait jika ada
                if (method_exists($spg, 'jadwalPromosi')) {
                    $spg->jadwalPromosi()->delete();
                }

                $user = $spg->user;
                $spg->delete();

                if ($user) {
                    $user->delete();
                }
            });

            return redirect()->route('spg.index')->with('success', "Data SPG '{$namaSpg}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('spg.index')->with(
                'error',
                "Gagal menghapus data SPG '{$namaSpg}': " . $e->getMessage()
            );
        }
    }
}
