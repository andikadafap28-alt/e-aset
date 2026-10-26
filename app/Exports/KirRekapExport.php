<?php

namespace App\Exports;

use App\Models\KirItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KirRekapExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenis Barang / Nama Barang',
            'Merek / Model',
            'Bahan',
            'Total Jumlah Fisik',
            'Kondisi Baik',
            'Kondisi Kurang Baik',
            'Kondisi Rusak Berat',
            'Lokasi Persebaran Ruangan'
        ];
    }

    public function collection(): Collection
    {
        $query = KirItem::with('room');

        if (!empty($this->filters['room_id'])) {
            $query->where('room_id', $this->filters['room_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('merk', 'ILIKE', "%{$search}%")
                  ->orWhere('bahan', 'ILIKE', "%{$search}%");
            });
        }

        $allItems = $query->get();

        $grouped = $allItems->groupBy(function($item) {
            $name = trim(strtolower((string)$item->name));
            $merk = trim(strtolower((string)($item->merk ?? '-')));
            $bahan = trim(strtolower((string)($item->bahan ?? 'campuran')));
            return $name . '|' . $merk . '|' . $bahan;
        });

        $rows = collect();
        $no = 1;

        foreach ($grouped as $group) {
            $first = $group->first();
            $ruanganList = $group->map(function($i) {
                return ($i->room ? $i->room->name : 'Tanpa Ruangan') . ' (' . $i->jumlah . ')';
            })->unique()->values()->all();

            $rows->push([
                $no++,
                $first->name,
                $first->merk ?: '-',
                $first->bahan ?: 'Campuran',
                $group->sum('jumlah'),
                $group->sum('kondisi_baik'),
                $group->sum('kondisi_kurang_baik'),
                $group->sum('kondisi_rusak_berat'),
                implode(', ', $ruanganList)
            ]);
        }

        return $rows->sortBy(1)->values();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate 800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A1:I' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
