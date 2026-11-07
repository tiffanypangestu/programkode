<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $outlets = Outlet::with('wilayah')->orderBy('nama_outlet')->get();
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        return view('auth.register', compact('outlets', 'wilayahs'));
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
            'outlet_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->jabatan, ['Sales Promotion Girl', 'SPG', '4'])),
                'exists:outlet,outlet_id',
            ],
            'wilayah_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->jabatan, ['Beauty Consultant', 'BC', '3'])),
                'exists:wilayah,wilayah_id',
            ],
            'password' => ['required', 'string', 'min:8', 'max:12', 'confirmed'],
        ], [
            'outlet_id.required' => 'Untuk pendaftaran SPG, wajib memilih Outlet tempat bertugas.',
            'wilayah_id.required' => 'Untuk pendaftaran Beauty Consultant (BC), wajib memilih Wilayah penugasan (Dalam Kota, Luar Kota, atau Luar Pulau).',
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

            // Otomatis buat data karyawan agar akun terhubung langsung
            $karyawan = Karyawan::create([
                'user_id'       => $user_new->user_id,
                'role_id'       => $role->role_id,
                'nama_karyawan' => $validated['nama_lengkap'],
                'jabatan'       => $nama_role,
                'nomor_telepon' => $user_new->nomor_telepon ?? null,
                'tahun_masuk'   => (int) date('Y'),
            ]);

            if (in_array($nama_role, ['Sales Promotion Girl', 'SPG'])) {
                if (method_exists($user_new, 'spg')) {
                    $user_new->spg()->create([
                        'nama_spg' => $validated['nama_lengkap'],
                        'tanggal_masuk' => date('Y-m-d'),
                    ]);
                }
                // Hubungkan SPG ke outlet yang dipilih
                if (! empty($validated['outlet_id'])) {
                    $karyawan->outlets()->sync([$validated['outlet_id']]);
                }
            } elseif (in_array($nama_role, ['Beauty Consultant', 'BC'])) {
                $wilayahModel = ! empty($validated['wilayah_id']) ? Wilayah::find($validated['wilayah_id']) : null;
                $namaWilayah = $wilayahModel?->nama_wilayah;

                if (method_exists($user_new, 'bc')) {
                    $user_new->bc()->create([
                        'nama_bc' => $validated['nama_lengkap'],
                        'tanggal_masuk' => date('Y-m-d'),
                        'alamat' => $namaWilayah ? 'Wilayah: ' . $namaWilayah : null,
                    ]);
                }
                // Hubungkan BC ke seluruh outlet di wilayah yang dipilih
                if (! empty($validated['wilayah_id'])) {
                    $outletIds = Outlet::where('wilayah_id', $validated['wilayah_id'])->pluck('outlet_id')->all();
                    $karyawan->outlets()->sync($outletIds);
                }
            }
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
