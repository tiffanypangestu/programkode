<?php

use App\Http\Controllers\AnalisisPerformaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BcController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HargaProdukController;
use App\Http\Controllers\JadwalPromosiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\OutletPerformanceController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanImportController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PromosiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SpgController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiDetailController;
use App\Http\Controllers\WilayahController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hak akses (RBAC) - dicek di server lewat middleware 'role'
|--------------------------------------------------------------------------
| Mengikuti menu di sidebar.blade.php:
|
| Semua peran (Admin, Supervisor, BC, SPG) : Dashboard, Penjualan, Analisis Performa
| Admin dan Supervisor                      : Import Excel, Transaksi, Detail Transaksi,
|                                             Outlet Performance
| Admin saja                                : Master Data, Karyawan/BC/SPG, Promosi
|
| 'BC' dan 'SPG' sudah otomatis cocok dengan 'Beauty Consultant' dan
| 'Sales Promotion Girl' (lihat $roleMap di RoleMiddleware).
| Aturan yang lebih halus (misalnya SPG hanya melihat outlet miliknya)
| tetap dicek di dalam controller.
*/

/*
Halaman Publik
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
Authentication (hanya untuk tamu)
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    // Pendaftaran: jabatan Admin dan Supervisor TIDAK boleh dipilih sendiri.
    // Batasi di RegisterController dengan Rule::in([...]).
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Email terdaftar wajib diisi.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();
        if ($user) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui! Silakan login dengan password baru Anda.');
    })->name('password.email');
});

/*
Halaman yang perlu login
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |----------------------------------------------------------------------
    | ADMIN DAN SUPERVISOR
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin,Supervisor')->group(function () {

        // Import Excel penjualan. HARUS di atas Route::resource('penjualan') supaya
        // /penjualan/import tidak tertangkap oleh /penjualan/{penjualan}.
        // (PenjualanImportController tidak punya cek peran sendiri)
        Route::get('/penjualan/import', [PenjualanImportController::class, 'create'])
            ->name('penjualan.import.form');
        Route::post('/penjualan/import', [PenjualanImportController::class, 'store'])
            ->name('penjualan.import.store');
        Route::post('/penjualan/import/batch', [PenjualanImportController::class, 'processBatch'])
            ->name('penjualan.import.batch');
        Route::get('/penjualan/import/progress', [PenjualanImportController::class, 'progress'])
            ->name('penjualan.import.progress');

        // Transaksi
        Route::resource('transaksi', TransaksiController::class);
        Route::resource('transaksi-detail', TransaksiDetailController::class);

        // Performa outlet (data manual)
        Route::resource('outlet-performance', OutletPerformanceController::class);
        // Analisis Performa Outlet (K-Means)
        Route::get('/analisis-performa', [AnalisisPerformaController::class, 'index'])
            ->name('analisis-performa.index');
        Route::post('/analisis-performa/proses', [AnalisisPerformaController::class, 'proses'])
            ->name('analisis-performa.proses');
        Route::get('/analisis-performa/{periode}', [AnalisisPerformaController::class, 'show'])
            ->name('analisis-performa.show');
    });

    /*
    |----------------------------------------------------------------------
    | SEMUA PERAN: Admin, Supervisor, BC, SPG
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin,Supervisor,BC,SPG')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Penjualan: rute khusus ditulis SEBELUM resource supaya tidak
        // tertangkap oleh /penjualan/{penjualan}
        Route::get('/penjualan/import/template', [PenjualanController::class, 'downloadTemplate'])
            ->name('penjualan.download-template');
        Route::get('/penjualan/stok-outlet/{outlet}', [PenjualanController::class, 'getStokOutletJson'])
            ->name('penjualan.stok-outlet');
        Route::get('/penjualan/{penjualan}/pdf', [PenjualanController::class, 'downloadPdf'])
            ->name('penjualan.pdf');

        Route::resource('penjualan', PenjualanController::class);
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN SAJA
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {

        // Master Data
        Route::resource('outlet', OutletController::class);
        Route::resource('wilayah', WilayahController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('harga-produk', HargaProdukController::class);
        Route::get('/harga-produk-set/{produk}', [HargaProdukController::class, 'set'])
            ->name('harga-produk.set');
        Route::post('/harga-produk-set/{produk}', [HargaProdukController::class, 'saveSet'])
            ->name('harga-produk.save-set');

        // Manajemen Karyawan
        Route::resource('karyawan', KaryawanController::class);
        Route::resource('spg', SpgController::class);
        Route::resource('bc', BcController::class);

        // Promosi
        Route::resource('promosi', PromosiController::class);
        Route::resource('jadwal-promosi', JadwalPromosiController::class);
    });
});

