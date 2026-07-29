<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TemplateKibExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function view(): View
    {
        return view('aset.exports.template_kib');
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT, // NIBAR
            'M' => NumberFormat::FORMAT_TEXT, // No Register
            'V' => NumberFormat::FORMAT_TEXT, // Bukti Kepemilikan Nomor
        ];
    }
}
