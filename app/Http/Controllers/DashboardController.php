<?php

namespace App\Http\Controllers;

use App\Models\AnalisisPerforma;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahOutlet = Outlet::count();
        $jumlahProduk = Produk::count();
        $jumlahTransaksi = Transaksi::count();
        $jumlahAnalisis = AnalisisPerforma::count();

        $new_analisis = AnalisisPerforma::latest('created_at')->take(5)->get();

        return view('dashboard', compact('jumlahOutlet', 'jumlahProduk', 'jumlahTransaksi', 'jumlahAnalisis', 'new_analisis'));
    }
}
