<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TemplateKibExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $kib;

    public function __construct($kib = 'B')
    {
        $this->kib = strtoupper($kib ?? 'B');
    }

    public function view(): View
    {
        return view('aset.exports.template_kib', [
            'kib' => $this->kib
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Kode 1
            'B' => NumberFormat::FORMAT_TEXT, // Kode 2
            'C' => NumberFormat::FORMAT_TEXT, // Kode 3
            'D' => NumberFormat::FORMAT_TEXT, // Kode 4
            'E' => NumberFormat::FORMAT_TEXT, // Kode 5
            'F' => NumberFormat::FORMAT_TEXT, // Kode 6
            'G' => NumberFormat::FORMAT_TEXT, // Kode 7
            'H' => NumberFormat::FORMAT_TEXT, // Nama Barang
            'I' => NumberFormat::FORMAT_TEXT, // NIBAR Lama / Kode
            'J' => NumberFormat::FORMAT_TEXT, // NIBAR Baru / Register
            'K' => NumberFormat::FORMAT_TEXT, // Spesifikasi
            'L' => NumberFormat::FORMAT_TEXT, // Spesifikasi Lainnya
            'M' => NumberFormat::FORMAT_TEXT, // Merek/Tipe
            'N' => NumberFormat::FORMAT_TEXT, // Lokasi
            'O' => NumberFormat::FORMAT_NUMBER, // Jumlah
            'P' => NumberFormat::FORMAT_TEXT, // Satuan
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Harga Satuan
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Nilai Perolehan
            'S' => NumberFormat::FORMAT_TEXT, // Cara Perolehan
            'T' => NumberFormat::FORMAT_TEXT, // Tanggal/Tahun
            'U' => NumberFormat::FORMAT_TEXT, // Unit Kerja
            'V' => NumberFormat::FORMAT_TEXT, // Keterangan
        ];
    }
}
