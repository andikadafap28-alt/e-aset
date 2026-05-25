<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function calculateChartMax($maxValue) {
        if ($maxValue <= 0) return 6000; // Default jika kosong
        
        // Bagi nilai tertinggi dengan 6 untuk mencari ukuran tiap anak tangga (step)
        $rawStep = $maxValue / 6;
        
        // Cari magnitude (orde besaran), misal 466.000 punya magnitude 100.000
        $magnitude = pow(10, floor(log10($rawStep)));
        $fraction = $rawStep / $magnitude;
        
        // Bulatkan fraction ke angka 'cantik' (1, 2, 2.5, 5, atau 10)
        if ($fraction <= 1) { $niceFraction = 1; }
        elseif ($fraction <= 2) { $niceFraction = 2; }
        elseif ($fraction <= 2.5) { $niceFraction = 2.5; }
        elseif ($fraction <= 5) { $niceFraction = 5; }
        else { $niceFraction = 10; }
        
        // Hitung step yang sudah dibulatkan, kalikan 6 untuk nilai max Y-axis
        $step = $niceFraction * $magnitude;
        return $step * 6;
    }

    public function index()
    {
        // 1. Ringkasan Total Item per Kategori Besar
        $summaryStok = Item::select('kategori_besar', DB::raw('SUM(stok_sekarang) as total_stok'), DB::raw('COUNT(id) as jenis_barang'))
            ->groupBy('kategori_besar')
            ->get();

        $kategoriList = [
            'atk' => ['label' => 'ATK', 'icon' => 'indigo', 'total' => 0, 'jenis' => 0],
            'bahan_cetak' => ['label' => 'Bahan Cetak', 'icon' => 'blue', 'total' => 0, 'jenis' => 0],
            'benda_pos' => ['label' => 'Benda Pos', 'icon' => 'sky', 'total' => 0, 'jenis' => 0],
            'bahan_komputer' => ['label' => 'Bahan Komputer', 'icon' => 'purple', 'total' => 0, 'jenis' => 0],
            'obat' => ['label' => 'Obat', 'icon' => 'rose', 'total' => 0, 'jenis' => 0],
            'bahan_lainnya' => ['label' => 'Bahan Lainnya', 'icon' => 'emerald', 'total' => 0, 'jenis' => 0],
            'natura_pakan_lainnya' => ['label' => 'Natura & Pakan', 'icon' => 'amber', 'total' => 0, 'jenis' => 0],
            'vaksin' => ['label' => 'Vaksin', 'icon' => 'teal', 'total' => 0, 'jenis' => 0],
            'obat_apbd' => ['label' => 'Obat APBD', 'icon' => 'cyan', 'total' => 0, 'jenis' => 0],
        ];

        foreach ($summaryStok as $row) {
            $kat = $row->kategori_besar;
            // Handle if old data exists
            if ($kat == 'persediaan') $kat = 'atk';
            if ($kat == 'natura_pakan') $kat = 'natura_pakan_lainnya';

            if (isset($kategoriList[$kat])) {
                $kategoriList[$kat]['total'] = $row->total_stok;
                $kategoriList[$kat]['jenis'] = $row->jenis_barang;
            }
        }

        // 2. Transaksi Bulan Ini
        $bulanIniDate = Carbon::now();
        $bulanIni = $bulanIniDate->month;
        $tahunIni = $bulanIniDate->year;

        $transaksiBulanIni = InventoryTransaction::whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->select('jenis_transaksi', DB::raw('SUM(jumlah * harga_satuan) as nilai_rupiah'))
            ->groupBy('jenis_transaksi')
            ->get();

        $masukBulanIni = 0;
        $keluarBulanIni = 0;

        foreach ($transaksiBulanIni as $row) {
            if ($row->jenis_transaksi == 'masuk') $masukBulanIni = $row->nilai_rupiah;
            if ($row->jenis_transaksi == 'keluar') $keluarBulanIni = $row->nilai_rupiah;
        }

        // 3. Transaksi Bulan Lalu
        $bulanLaluDate = Carbon::now()->subMonth();
        $bulanLalu = $bulanLaluDate->month;
        $tahunLalu = $bulanLaluDate->year;

        $transaksiBulanLalu = InventoryTransaction::whereMonth('tanggal_transaksi', $bulanLalu)
            ->whereYear('tanggal_transaksi', $tahunLalu)
            ->select('jenis_transaksi', DB::raw('SUM(jumlah * harga_satuan) as nilai_rupiah'))
            ->groupBy('jenis_transaksi')
            ->get();
            
        $masukBulanLalu = 0;
        $keluarBulanLalu = 0;

        foreach ($transaksiBulanLalu as $row) {
            if ($row->jenis_transaksi == 'masuk') $masukBulanLalu = $row->nilai_rupiah;
            if ($row->jenis_transaksi == 'keluar') $keluarBulanLalu = $row->nilai_rupiah;
        }

        // 4. Data untuk Chart (6 Bulan Terakhir - Pemasukan & Pengeluaran untuk semua kategori)
        $chartBulanData = [];
        $chartLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartBulanData[] = [
                'month' => $date->month,
                'year' => $date->year
            ];
            $chartLabels[] = $date->translatedFormat('M Y');
        }

        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $transactions = DB::table('inventory_transactions')
            ->join('items', 'inventory_transactions.item_id', '=', 'items.id')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->select(
                'items.kategori_besar',
                'inventory_transactions.jenis_transaksi',
                DB::raw('EXTRACT(MONTH FROM tanggal_transaksi) as month'),
                DB::raw('EXTRACT(YEAR FROM tanggal_transaksi) as year'),
                DB::raw('SUM(inventory_transactions.jumlah * inventory_transactions.harga_satuan) as total')
            )
            ->groupBy(
                'items.kategori_besar',
                'inventory_transactions.jenis_transaksi',
                DB::raw('EXTRACT(YEAR FROM tanggal_transaksi)'),
                DB::raw('EXTRACT(MONTH FROM tanggal_transaksi)')
            )
            ->get();

        $allChartData = [];
        foreach (array_keys($kategoriList) as $cat) {
            $allChartData[$cat] = [
                'masuk' => array_fill(0, 6, 0),
                'keluar' => array_fill(0, 6, 0)
            ];
        }

        foreach ($transactions as $trx) {
            $kat = $trx->kategori_besar;
            if ($kat == 'persediaan') $kat = 'atk';
            if ($kat == 'natura_pakan') $kat = 'natura_pakan_lainnya';

            if (!isset($allChartData[$kat])) continue;

            $index = -1;
            foreach ($chartBulanData as $i => $cb) {
                if ($cb['month'] == $trx->month && $cb['year'] == $trx->year) {
                    $index = $i;
                    break;
                }
            }

            if ($index !== -1) {
                if ($trx->jenis_transaksi == 'masuk') {
                    $allChartData[$kat]['masuk'][$index] += (float)$trx->total;
                } else {
                    $allChartData[$kat]['keluar'][$index] += (float)$trx->total;
                }
            }
        }

        // 5. Recent Transactions
        $recentTransactions = InventoryTransaction::with('item')
            ->latest()
            ->take(5)
            ->get();

        // 6. Low Stock Items
        $lowStockItems = Item::where('stok_sekarang', '<', 10)
            ->where('stok_sekarang', '>', 0)
            ->whereIn('kategori_besar', ['atk', 'persediaan', 'bahan_komputer', 'bahan_cetak', 'benda_pos', 'obat', 'bahan_lainnya', 'natura_pakan_lainnya', 'vaksin', 'obat_apbd'])
            ->orderBy('stok_sekarang', 'asc')
            ->take(10) // taking 10 for UI fit
            ->get();

        // 7. Calculate Chart Max
        $chartMaxData = [];
        $globalMax = 0;
        foreach ($allChartData as $kat => $data) {
            $maxIn = !empty($data['masuk']) ? max($data['masuk']) : 0;
            $maxOut = !empty($data['keluar']) ? max($data['keluar']) : 0;
            $localMax = max($maxIn, $maxOut);
            $chartMaxData[$kat] = $this->calculateChartMax($localMax);
            $globalMax = max($globalMax, $localMax);
        }
        $chartMax = $this->calculateChartMax($globalMax);

        // 8. Statistik Aset (Dioptimalkan agar tidak memuat semua data ke memori/N+1 Query)
        $totalPurchaseValue = \App\Models\Asset::sum('harga_perolehan');
        
        $totalDepreciation = 0;
        $totalBookValue = 0;
        
        // Gunakan chunking dan eager load kategori untuk mencegah N+1 Query & Out of Memory
        \App\Models\Asset::with('category')->orderBy('id')->chunk(500, function($chunk) use (&$totalDepreciation, &$totalBookValue) {
            foreach ($chunk as $asset) {
                $totalDepreciation += $asset->accumulated_depreciation;
                $totalBookValue += $asset->book_value;
            }
        });

        $assetStats = [
            'total' => \App\Models\Asset::count(),
            'aktif' => \App\Models\Asset::where('status_aktif', true)->count(),
            'disposed' => \App\Models\Asset::where('status_aktif', false)->count(),
            'baik' => \App\Models\Asset::where('condition', 'Baik')->count(),
            'rusak' => \App\Models\Asset::whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])->count(),
            'perlu_kalibrasi' => \App\Models\Asset::whereNotNull('last_calibration')
                                    ->where('last_calibration', '<', now()->subDays(365))
                                    ->count(),
            'total_purchase' => $totalPurchaseValue,
            'total_depreciation' => $totalDepreciation,
            'total_book_value' => $totalBookValue,
        ];

        // Data untuk Chart Aset Kondisi
        $chartKondisi = \App\Models\Asset::selectRaw('condition, count(*) as count')->groupBy('condition')->pluck('count', 'condition');

        // Data untuk Chart Aset Kategori
        $chartKategori = \Illuminate\Support\Facades\DB::table('assets')
            ->leftJoin('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->selectRaw('COALESCE(asset_categories.nama_kategori, assets.category, \'Lainnya\') as kat_name, count(*) as count')
            ->groupBy('kat_name')
            ->pluck('count', 'kat_name');

        return view('dashboard', compact(
            'kategoriList', 
            'masukBulanIni', 
            'keluarBulanIni', 
            'masukBulanLalu',
            'keluarBulanLalu',
            'chartLabels',
            'allChartData',
            'recentTransactions',
            'lowStockItems',
            'chartMax',
            'chartMaxData',
            'assetStats',
            'chartKondisi',
            'chartKategori'
        ));
    }
}
