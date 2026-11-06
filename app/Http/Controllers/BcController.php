<?php

namespace App\Http\Controllers;

use App\Models\Bc;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class BcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bc = Bc::with('user')->orderBy('nama_bc')->paginate(10);

        return view('bc.index', compact('bc'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('bc')->orderBy('name')->get();

        return view('bc.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,user_id', Rule::unique('bc', 'user_id')],
            'nama_bc' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'nomor_telepon' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $roleBc = Role::where('nama_role', 'Beauty Consultant')->first();

            $user = User::create([
                'name' => $validated['nama_bc'],
                'nama_lengkap' => $validated['nama_bc'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'role_id' => $roleBc ? $roleBc->role_id : 3,
            ]);

            Bc::create([
                'user_id' => $user->user_id,
                'nama_bc' => $validated['nama_bc'],
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            ]);

            Karyawan::create([
                'user_id'       => $user->user_id,
                'role_id'       => $roleBc ? $roleBc->role_id : 3,
                'nama_karyawan' => $validated['nama_bc'],
                'jabatan'       => 'Beauty Consultant',
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'tahun_masuk'   => (int) date('Y'),
            ]);
        });

        return redirect()->route('bc.index')->with('success', 'Data BC berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bc $bc)
    {
        $bc->load('user');

        return view('bc.show', compact('bc'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bc $bc)
    {
        $users = User::where(function ($query) use ($bc) {
            $query->whereDoesntHave('bc')->orWhere('user_id', $bc->user_id);
        })->orderBy('name')->get();

        return view('bc.edit', compact('bc', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bc $bc)
    {
        $userId = $bc->user_id;

        $validated = $request->validate([
            'nama_bc' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId, 'user_id')],
            'password' => ['nullable', 'string', 'min:6', 'max:12'],
            'nomor_telepon' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $bc) {
            if ($bc->user) {
                $user_data = [
                    'name' => $validated['nama_bc'],
                    'nama_lengkap' => $validated['nama_bc'],
                    'email' => $validated['email'],
                    'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                ];

                if (! empty($validated['password'])) {
                    $user_data['password'] = Hash::make($validated['password']);
                }

                $bc->user->update($user_data);
            } else {
                $roleBc = Role::where('nama_role', 'Beauty Consultant')->first();
                $user = User::create([
                    'name' => $validated['nama_bc'],
                    'nama_lengkap' => $validated['nama_bc'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'Bc12345!'),
                    'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                    'role_id' => $roleBc ? $roleBc->role_id : 3,
                ]);
                $bc->user_id = $user->user_id;
            }

            $bc->update([
                'user_id' => $bc->user_id,
                'nama_bc' => $validated['nama_bc'],
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            ]);
        });

        return redirect()->route('bc.index')->with('success', 'Data BC berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bc $bc)
    {
        $namaBc = $bc->nama_bc;

        try {
            DB::transaction(function () use ($bc) {
                // Hapus jadwal promosi terkait jika ada
                if (method_exists($bc, 'jadwalPromosi')) {
                    $bc->jadwalPromosi()->delete();
                }

                $user = $bc->user;
                $bc->delete();

                if ($user) {
                    $user->delete();
                }
            });

            return redirect()->route('bc.index')->with('success', "Data Beauty Consultant '{$namaBc}' berhasil dihapus.");
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('bc.index')->with(
                'error',
                "Gagal menghapus data BC '{$namaBc}': " . $e->getMessage()
            );
        }
    }
}
