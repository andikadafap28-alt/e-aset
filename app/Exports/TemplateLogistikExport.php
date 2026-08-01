<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateLogistikExport implements WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama Barang',
            'Satuan',
            'Harga Satuan',
            'Stok Awal',
            'Penerimaan',
            'Pemakaian',
            'Total Stok Akhir'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
