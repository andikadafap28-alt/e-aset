<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AktivitasAsetMasukSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $data;
    protected $bulanAwal;
    protected $bulanAkhir;

    public function __construct($data, $bulanAwal, $bulanAkhir)
    {
        $this->data = $data;
        $this->bulanAwal = $bulanAwal;
        $this->bulanAkhir = $bulanAkhir;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                'no' => $no++,
                'kode' => $item->asset_code,
                'nama' => $item->name,
                'kategori' => is_object($item->category) ? $item->category->nama_kategori : ($item->getAttribute('category') ?: '-'),
                'lokasi' => $item->location,
                'tanggal_masuk' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',
                'harga' => $item->harga_perolehan
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['DAFTAR ASET MASUK BARU'],
            ['Periode: ' . $this->bulanAwal . ' s/d ' . $this->bulanAkhir],
            [],
            ['No', 'Kode Aset', 'Nama Aset', 'Kategori', 'Lokasi', 'Tanggal Masuk', 'Harga Perolehan (Rp)']
        ];
    }

    public function title(): string
    {
        return 'Aset Masuk Baru';
    }
}
