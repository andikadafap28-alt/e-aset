<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAssetExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;
    protected $groupBy;

    public function __construct(array $data, string $groupBy)
    {
        $this->data = $data;
        $this->groupBy = $groupBy;
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                $item['group_name'],
                $item['total_aset'],
                $item['baik'],
                $item['rusak_ringan'],
                $item['rusak_berat'],
                'Rp ' . number_format($item['total_nilai'], 0, ',', '.')
            ];
        }

        // Add Total Row
        $rows[] = [
            '',
            'TOTAL KESELURUHAN',
            array_sum(array_column($this->data, 'total_aset')),
            array_sum(array_column($this->data, 'baik')),
            array_sum(array_column($this->data, 'rusak_ringan')),
            array_sum(array_column($this->data, 'rusak_berat')),
            'Rp ' . number_format(array_sum(array_column($this->data, 'total_nilai')), 0, ',', '.')
        ];

        return $rows;
    }

    public function headings(): array
    {
        $groupTitle = $this->groupBy === 'location' ? 'Ruangan / Lokasi' : 'Kategori Aset';
        return [
            ['LAPORAN REKAPITULASI ASET'],
            ['Tanggal Cetak: ' . date('d/m/Y')],
            [],
            [
                'No',
                $groupTitle,
                'Total Aset',
                'Kondisi Baik',
                'Kondisi Rusak Ringan',
                'Kondisi Rusak Berat',
                'Total Nilai (Rp)'
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 40,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $lastRow = count($this->data) + 5;
        $sheet->getStyle('A'.$lastRow.':G'.$lastRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$lastRow.':G'.$lastRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCBD5E1');

        $sheet->getStyle('A4:G'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
