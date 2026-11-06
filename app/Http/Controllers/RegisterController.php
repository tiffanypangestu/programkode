<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // database pemetaan
        $role_map = [
            '1' => 'Admin',
            '2' => 'Supervisor',
            '3' => 'Beauty Consultant',
            '4' => 'Sales Promotion Girl',

            'Admin' => 'Admin',
            'Supervisor' => 'Supervisor',
            'BC' => 'Beauty Consultant',
            'SPG' => 'Sales Promotion Girl',

            'Beauty Consultant' => 'Beauty Consultant',
            'Sales Promotion Girl' => 'Sales Promotion Girl',
        ];
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'jabatan' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:12', 'confirmed'],
        ]);

        $user_new = null;

        DB::transaction(function () use ($validated, $role_map, &$user_new) {
            $nama_role = $role_map[$validated['jabatan']] ?? $validated['jabatan'];

            $role = Role::where('nama_role', $nama_role)->first();

            if (! $role) {
                throw ValidationException::withMessages([
                    'jabatan' => 'Role yang dipilih belum tersedia!',
                ]);
            }

            $user_new = User::create([
                'name' => $validated['name'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $role->role_id,
            ]);

            if (in_array($nama_role, ['Sales Promotion Girl', 'SPG']) && method_exists($user_new, 'spg')) {
                $user_new->spg()->create([
                    'nama_spg' => $validated['nama_lengkap'],
                ]);
            } elseif (in_array($nama_role, ['Beauty Consultant', 'BC']) && method_exists($user_new, 'bc')) {
                $user_new->bc()->create([
                    'nama_bc' => $validated['nama_lengkap'],
                ]);
            }

            // Otomatis buat data karyawan agar akun terhubung langsung
            Karyawan::create([
                'user_id'       => $user_new->user_id,
                'role_id'       => $role->role_id,
                'nama_karyawan' => $validated['nama_lengkap'],
                'jabatan'       => $nama_role,
                'nomor_telepon' => $user_new->nomor_telepon ?? null,
                'tahun_masuk'   => (int) date('Y'),
            ]);
        });

        Auth::login($user_new);

        $nama_role = $user_new->role->nama_role ?? '';

        return match ($nama_role) {
            'Admin', 'Supervisor' => redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! '),
            'Beauty Consultant', 'Sales Promotion Girl' => redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! '),
            default => redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! '),
        };
    }
}
