
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
Halaman Publik
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
Authentication
*/

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.store');

    // Lupa Password
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return back()->with(
            'status',
            'Form Lupa Password berhasil dikirim. Pengiriman email reset password belum diaktifkan.'
        );
    })->name('password.email');
});

/*
Halaman yang perlu login
*/

Route::middleware('auth')->group(function () {

    /*
    | Dashboard
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Master Data
    */

    Route::resource('wilayah', WilayahController::class);

    Route::resource('kategori', KategoriController::class);

    Route::resource('produk', ProdukController::class);

    Route::resource('harga-produk', HargaProdukController::class);

    Route::resource('outlet', OutletController::class);

    /*
    | Karyawan dan Tenaga Lapangan
    */

    Route::resource('karyawan', KaryawanController::class);

    Route::resource('spg', SpgController::class);

    Route::resource('bc', BcController::class);

    /*
    | Promosi
    */

    Route::resource('promosi', PromosiController::class);

    Route::resource('jadwal-promosi', JadwalPromosiController::class);

    /*
    | Transaksi dan Penjualan
    */

    Route::resource('transaksi', TransaksiController::class);

    Route::resource('transaksi-detail', TransaksiDetailController::class);
    Route::get('/penjualan/import', [PenjualanImportController::class, 'create'])->name('penjualan.import.form');

    Route::post('/penjualan/import', [PenjualanImportController::class, 'store'])->name('penjualan.import.store');
    Route::get('/penjualan/{penjualan}/pdf', [PenjualanController::class, 'downloadPdf'])->name('penjualan.pdf');

    Route::get('/penjualan/import/template', [PenjualanController::class, 'downloadTemplate'])->name('penjualan.download-template');

    Route::resource('penjualan', PenjualanController::class);

    /*
    | Analisis Performa Outlet
    */

    Route::resource(
        'outlet-performance',
        OutletPerformanceController::class
    );

    Route::resource(
        'analisis-performa',
        AnalisisPerformaController::class
    );

    // Menampilkan halaman riwayat analisis (index)
    Route::get('/analisis-performa', [AnalisisPerformaController::class, 'index'])->name('analisis-performa.index');

    // Memproses eksekusi analisis K-Means (proses)
    Route::post('/analisis-performa/proses', [AnalisisPerformaController::class, 'proses'])->name('analisis-performa.proses');

    // Menampilkan detail visualisasi grafik & rekomendasi (show)
    Route::get('/analisis-performa/{periode}', [AnalisisPerformaController::class, 'show'])->name('analisis-performa.show');

    /*
    | Logout
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::middleware(['auth', 'role:Admin,Supervisor,BC,SPG,Beauty Consultant,Sales Promotion Girl'])->group(function () {
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
});
