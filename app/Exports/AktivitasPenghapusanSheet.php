<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AktivitasPenghapusanSheet implements FromCollection, WithTitle, WithHeadings
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
                'kode' => $item->asset ? $item->asset->asset_code : '-',
                'nama' => $item->asset ? $item->asset->name : '-',
                'tanggal_penghapusan' => $item->tanggal_penghapusan,
                'alasan' => $item->alasan,
                'nilai_sisa' => $item->nilai_sisa
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['DAFTAR RIWAYAT PENGHAPUSAN ASET'],
            ['Periode: ' . $this->bulanAwal . ' s/d ' . $this->bulanAkhir],
            [],
            ['No', 'Kode Aset', 'Nama Aset', 'Tanggal Penghapusan', 'Alasan', 'Nilai Sisa / Jual (Rp)']
        ];
    }

    public function title(): string
    {
        return 'Penghapusan Aset';
    }
}
