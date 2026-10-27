<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Override;

class TemplatePenjualanExport implements FromView, WithEvents
{
    #[Override]
    public function view(): View
    {
        return view('exports.template_penjualan');
    }

    #[Override]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // ambil posisi baris terakhir yang punya data
                $baris_tertinggi = $event->sheet->getHighestRow();

                // rumus perkalian baris 2 sampai akhir
                for ($baris = 2; $baris <= $baris_tertinggi; $baris++) {
                    $event->sheet->setCellValue("G{$baris}", "=E{$baris}*F{$baris}");
                }

                // rumus SUM di baris paling bawah
                $total_baris = $baris_tertinggi + 1;
                $event->sheet->setCellValue("F{$total_baris}", 'Total Transaksi');
                $event->sheet->setCellValue("G{$total_baris}", "=SUM(G2:G{$baris_tertinggi})");
            },
        ];
    }
}
