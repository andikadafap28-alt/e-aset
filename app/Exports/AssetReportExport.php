<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class AssetReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->type == 'inventaris') {
            return ['Kode Aset', 'Nama Aset', 'Kategori', 'Tahun Pengadaan', 'Kondisi', 'Lokasi', 'Harga Perolehan'];
        } elseif ($this->type == 'penyusutan') {
            return ['Kode Aset', 'Nama Aset', 'Kategori', 'Tahun Pengadaan', 'Umur Ekonomis (Tahun)', 'Harga Perolehan', 'Akumulasi Penyusutan', 'Nilai Buku'];
        } else { // disposal
            return ['Kode Aset', 'Nama Aset', 'Kategori', 'Tahun Pengadaan', 'Harga Perolehan', 'Tanggal Dihapus', 'Alasan Penghapusan'];
        }
    }

    public function map($row): array
    {
        if ($this->type == 'inventaris') {
            return [
                $row->asset_code,
                $row->name,
                $row->category ? $row->category->nama_kategori : ($row->getAttribute('category') ?: '-'),
                $row->year_purchased,
                $row->condition,
                $row->location,
                $row->harga_perolehan,
            ];
        } elseif ($this->type == 'penyusutan') {
            return [
                $row->asset_code,
                $row->name,
                $row->category ? $row->category->nama_kategori : ($row->getAttribute('category') ?: '-'),
                $row->year_purchased,
                $row->category ? $row->category->umur_ekonomis : '-',
                $row->harga_perolehan,
                $row->accumulated_depreciation,
                $row->book_value,
            ];
        } else { // disposal
            $asset = $row->asset;
            return [
                $asset ? $asset->asset_code : '-',
                $asset ? $asset->name : '-',
                $asset && $asset->category ? $asset->category->nama_kategori : ($asset ? $asset->getAttribute('category') : '-'),
                $asset ? $asset->year_purchased : '-',
                $asset ? $asset->harga_perolehan : 0,
                Carbon::parse($row->tanggal_penghapusan)->translatedFormat('d F Y'),
                $row->alasan,
            ];
        }
    }
}
