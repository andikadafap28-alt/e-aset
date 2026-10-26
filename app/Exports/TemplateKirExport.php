<?php

namespace App\Exports;

use App\Models\Room;
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

class TemplateKirExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $roomId;

    public function __construct($roomId = null)
    {
        $this->roomId = $roomId;
    }

    public function headings(): array
    {
        return [
            'Nama Ruangan',
            'No Urut',
            'Jenis Barang / Nama Barang',
            'Merek / Model',
            'Bahan',
            'Jumlah Barang',
            'Keadaan Baik',
            'Keadaan Kurang Baik',
            'Keadaan Rusak Berat',
            'Keterangan Mutasi dll.'
        ];
    }

    public function collection(): Collection
    {
        $room = null;
        if ($this->roomId) {
            $room = Room::with('assets')->find($this->roomId);
        }

        $roomName = $room ? $room->name : 'Ruang Kepala Puskesmas';

        // Jika ruangan memiliki data aset di database, gunakan data tersebut
        if ($room && $room->assets && $room->assets->count() > 0) {
            $grouped = $room->assets->groupBy(function($item) {
                $name = $item->name ?? '-';
                $merk = $item->merk ?? '';
                $bahan = $item->bahan ?? '';
                return $name . '|' . $merk . '|' . $bahan;
            });

            $rows = collect();
            $no = 1;
            foreach ($grouped as $group) {
                $first = $group->first();
                $baik = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['baik', 'b']))->count();
                $kurangBaik = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['kurang baik', 'kb', 'rusak ringan', 'rr']))->count();
                $rusakBerat = $group->filter(fn($i) => in_array(strtolower($i->condition ?? ''), ['rusak berat', 'rb']))->count();

                $total = $group->count();
                if (($baik + $kurangBaik + $rusakBerat) < $total) {
                    $baik += ($total - ($baik + $kurangBaik + $rusakBerat));
                }

                $rows->push([
                    $roomName,
                    $no++,
                    $first->name,
                    $first->merk ?: '-',
                    $first->bahan ?: 'Campuran',
                    $total,
                    $baik,
                    $kurangBaik,
                    $rusakBerat,
                    $first->keterangan ?: '-'
                ]);
            }
            return $rows;
        }

        // Data sampel standar sesuai dokumen KIR resmi (Ruang Kepala Puskesmas)
        return collect([
            [$roomName, 1, 'AC', 'Panasonic', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 2, 'TV LED', 'Toshiba', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 3, 'Antena TV', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 4, 'Sofa set + Meja', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 5, 'Kursi putar', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 6, 'Lemari kaca', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 7, 'Kursi', 'Polaris', 'Besi', 4, 4, 0, 0, '-'],
            [$roomName, 8, 'Vas bunga kecil', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 9, 'Vas bunga sedang', '-', 'Campuran', 2, 2, 0, 0, '-'],
            [$roomName, 10, 'Jam Dinding', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 11, 'UPS', '-', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 12, 'PC AIO', 'Dell', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 13, 'Mouse', 'Lenovo', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 14, 'Keyboard', 'Dell', 'Campuran', 1, 1, 0, 0, '-'],
            [$roomName, 15, 'Meja kerja', '-', 'Kayu', 1, 1, 0, 0, '-'],
            [$roomName, 16, 'Tempat sampah', '-', 'Plastik', 1, 1, 0, 0, '-'],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Styling header baris 1
        $sheet->getStyle('A1:J1')->applyFromArray([
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

        // Border untuk seluruh sel data
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A1:J' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            // Alignment center untuk kolom numerik (B, F, G, H, I)
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
