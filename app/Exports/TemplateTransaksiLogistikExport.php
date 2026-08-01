<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateTransaksiLogistikExport implements WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Tanggal Transaksi (YYYY-MM-DD)',
            'Tanggal SPJ (YYYY-MM-DD, Opsional)',
            'Kode Barang (Opsional)',
            'Nama Barang',
            'Kategori (Contoh: Umum)',
            'Satuan (Contoh: Pcs)',
            'Harga Satuan (Angka)',
            'Jenis Transaksi (masuk / keluar)',
            'Jumlah',
            'Keterangan (Contoh: Pembelian, UGD, dll)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
