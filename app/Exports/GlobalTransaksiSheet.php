<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class GlobalTransaksiSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $bulanAwal;
    protected $bulanAkhir;
    protected $jenis;
    protected $items;
    protected $kategoriName;

    public function __construct($bulanAwal, $bulanAkhir, $jenis, $items, $kategoriName)
    {
        $this->bulanAwal = $bulanAwal;
        $this->bulanAkhir = $bulanAkhir;
        $this->jenis = $jenis;
        $this->items = $items;
        $this->kategoriName = $kategoriName;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->items as $item) {
            $transactions = $item->transactions()->orderBy('tanggal_transaksi', 'asc')->get();
            
            $saldoAwal = 0;
            $masuk = 0;
            $keluar = 0;

            if ($this->jenis == 'internal') {
                foreach ($transactions as $trx) {
                    if (!$trx->tanggal_transaksi) continue;
                    $bulanTrx = Carbon::parse($trx->tanggal_transaksi)->format('Y-m');
                    
                    if ($bulanTrx < $this->bulanAwal) {
                        if ($trx->jenis_transaksi == 'masuk') $saldoAwal += $trx->jumlah;
                        else $saldoAwal -= $trx->jumlah;
                    } elseif ($bulanTrx >= $this->bulanAwal && $bulanTrx <= $this->bulanAkhir) {
                        if ($trx->jenis_transaksi == 'masuk') $masuk += $trx->jumlah;
                        else $keluar += $trx->jumlah;
                    }
                }
            } else {
                // Logika Dinas (Virtual Ledger)
                $stokLunas = 0;
                $pemakaianTertunda = 0;
                $events = [];

                foreach ($transactions as $trx) {
                    if ($trx->jenis_transaksi == 'masuk') {
                        if (!$trx->status_hutang && $trx->tanggal_spj) {
                            $events[] = [
                                'type' => 'masuk',
                                'date' => $trx->tanggal_spj,
                                'jumlah' => $trx->jumlah
                            ];
                        }
                    } elseif ($trx->jenis_transaksi == 'keluar') {
                        $events[] = [
                            'type' => 'keluar',
                            'date' => $trx->tanggal_transaksi,
                            'jumlah' => $trx->jumlah
                        ];
                    }
                }

                usort($events, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });

                foreach ($events as $event) {
                    $bulanEvent = Carbon::parse($event['date'])->format('Y-m');

                    if ($event['type'] == 'masuk') {
                        $stokLunas += $event['jumlah'];
                        
                        if ($pemakaianTertunda > 0) {
                            $tercover = min($stokLunas, $pemakaianTertunda);
                            $stokLunas -= $tercover;
                            $pemakaianTertunda -= $tercover;
                            
                            // Hitung backdated keluar
                            if ($bulanEvent < $this->bulanAwal) {
                                $saldoAwal -= $tercover;
                            } elseif ($bulanEvent >= $this->bulanAwal && $bulanEvent <= $this->bulanAkhir) {
                                $keluar += $tercover;
                            }
                        }

                        if ($bulanEvent < $this->bulanAwal) {
                            $saldoAwal += $event['jumlah'];
                        } elseif ($bulanEvent >= $this->bulanAwal && $bulanEvent <= $this->bulanAkhir) {
                            $masuk += $event['jumlah'];
                        }

                    } else {
                        // type keluar
                        if ($stokLunas >= $event['jumlah']) {
                            $stokLunas -= $event['jumlah'];
                            if ($bulanEvent < $this->bulanAwal) {
                                $saldoAwal -= $event['jumlah'];
                            } elseif ($bulanEvent >= $this->bulanAwal && $bulanEvent <= $this->bulanAkhir) {
                                $keluar += $event['jumlah'];
                            }
                        } else {
                            $tercover = $stokLunas;
                            if ($tercover > 0) {
                                if ($bulanEvent < $this->bulanAwal) {
                                    $saldoAwal -= $tercover;
                                } elseif ($bulanEvent >= $this->bulanAwal && $bulanEvent <= $this->bulanAkhir) {
                                    $keluar += $tercover;
                                }
                            }
                            $sisaTdkTercover = $event['jumlah'] - $stokLunas;
                            $pemakaianTertunda += $sisaTdkTercover;
                            $stokLunas = 0;
                        }
                    }
                }
            }

            $sisa = $saldoAwal + $masuk - $keluar;

            $data[] = [
                'nama_barang' => $item->nama_barang,
                'satuan' => $item->satuan,
                'harga_satuan' => $item->harga_satuan,
                'saldo_awal' => $saldoAwal,
                'penerimaan' => $masuk,
                'pengeluaran' => $keluar,
                'sisa' => $sisa,
                'total_nilai' => $sisa * $item->harga_satuan,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Satuan',
            'Harga Satuan',
            'Saldo Awal (' . $this->bulanAwal . ')',
            'Penerimaan',
            'Pengeluaran',
            'Sisa (' . $this->bulanAkhir . ')',
            'Total Nilai Sisa'
        ];
    }

    public function title(): string
    {
        // Max 31 chars for excel sheet title
        return substr('Trx ' . $this->kategoriName, 0, 31);
    }
}
