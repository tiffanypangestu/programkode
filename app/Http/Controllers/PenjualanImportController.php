<?php

namespace App\Http\Controllers;

use App\Imports\PenjualanImport;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class PenjualanImportController extends Controller
{
    public function create()
    {
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('penjualan.import', compact('karyawan'));
    }

    public function store(Request $request)
    {
        if (! $request->filled('karyawan_id')) {
            $karyawanId = auth()->user()?->karyawan?->karyawan_id ?? Karyawan::first()?->karyawan_id;
            if ($karyawanId) {
                $request->merge(['karyawan_id' => $karyawanId]);
            }
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:10240'],
            'karyawan_id' => ['required', 'integer', 'exists:karyawan,karyawan_id'],
        ],
            [
                'file.required' => 'Silakan pilih file Excel penjualan.',
                'file.mimes' => 'File harus berformat XLSX atau CSV.',
                'file.max' => 'Ukuran file maksimal 10 MB.',
                'karyawan_id.required' => 'Pilih karyawan penanggung jawab import.',
                'karyawan_id.exists' => 'Karyawan yang dipilih tidak ditemukan.',
            ]);

        try {
            Excel::import(new PenjualanImport((int) $validated['karyawan_id']), $request->file('file'));

            return redirect()->route('transaksi.index')->with('success', 'Data historis berhasil diimport!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Import gagal. Periksa log untuk detailnya!');
        }
    }
}
