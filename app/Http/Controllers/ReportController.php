<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetDisposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::orderBy('nama_kategori', 'asc')->get();
        return view('reports.index', compact('categories'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:inventaris,penyusutan,disposal',
            'export_format' => 'required|in:pdf,excel'
        ]);

        $type = $request->input('report_type');
        $format = $request->input('export_format');
        
        $categoryId = $request->input('category_id');
        $condition = $request->input('condition');
        $yearStart = $request->input('year_start');
        $yearEnd = $request->input('year_end');

        // Query Builder
        if ($type == 'disposal') {
            $query = AssetDisposal::with(['asset', 'asset.category']);
        } else {
            $query = Asset::with('category')->where('status_aktif', true);
        }

        // Apply Filters
        if ($categoryId && $categoryId !== 'all') {
            if ($type == 'disposal') {
                $query->whereHas('asset', function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            } else {
                $query->where('category_id', $categoryId);
            }
        }

        if ($type !== 'disposal' && $condition && $condition !== 'all') {
            $query->where('condition', $condition);
        }

        if ($yearStart) {
            if ($type == 'disposal') {
                $query->whereHas('asset', function($q) use ($yearStart) {
                    $q->where('year_purchased', '>=', $yearStart);
                });
            } else {
                $query->where('year_purchased', '>=', $yearStart);
            }
        }

        if ($yearEnd) {
            if ($type == 'disposal') {
                $query->whereHas('asset', function($q) use ($yearEnd) {
                    $q->where('year_purchased', '<=', $yearEnd);
                });
            } else {
                $query->where('year_purchased', '<=', $yearEnd);
            }
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan dengan filter tersebut.');
        }

        if ($format === 'excel') {
            $fileName = 'Laporan_' . ucfirst($type) . '_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new AssetReportExport($data, $type), $fileName);
        }

        // PDF Generation
        $pdfData = [
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'data' => $data,
            'filters' => [
                'category' => $categoryId !== 'all' && $categoryId ? AssetCategory::find($categoryId)->nama_kategori : 'Semua Kategori',
                'condition' => $type !== 'disposal' && $condition !== 'all' ? $condition : 'Semua Kondisi',
                'years' => ($yearStart ? $yearStart : 'Awal') . ' - ' . ($yearEnd ? $yearEnd : 'Sekarang')
            ]
        ];

        if ($type == 'inventaris') {
            $pdfData['title'] = 'Laporan Daftar Inventaris Aset';
            $pdf = Pdf::loadView('reports.asset_pdf', $pdfData)->setPaper('A4', 'landscape');
        } elseif ($type == 'penyusutan') {
            $pdfData['title'] = 'Laporan Penyusutan Nilai Aset';
            $pdfData['totalPurchaseValue'] = $data->sum('harga_perolehan');
            $pdfData['totalDepreciation'] = $data->sum(function($asset) { return $asset->accumulated_depreciation; });
            $pdfData['totalBookValue'] = $data->sum(function($asset) { return $asset->book_value; });
            $pdf = Pdf::loadView('reports.depreciation_pdf', $pdfData)->setPaper('A4', 'landscape');
        } else {
            $pdfData['title'] = 'Laporan Penghapusan Aset (Disposal)';
            $pdf = Pdf::loadView('reports.disposal_pdf', $pdfData)->setPaper('A4', 'landscape');
        }

        return $pdf->download('Laporan_' . ucfirst($type) . '_' . date('Ymd_His') . '.pdf');
    }

    public function downloadAssetReport()
    {
        return redirect()->route('laporan.index');
    }

    private function getRekapData(Request $request)
    {
        $categoryId = $request->query('category_id');
        $location = $request->query('location');
        $condition = $request->query('condition');
        $yearStart = $request->query('year_start');
        $yearEnd = $request->query('year_end');
        $groupBy = $request->query('group_by', 'category'); // 'category' or 'location'

        $query = Asset::with('category')->where('status_aktif', true);

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }
        if ($location && $location !== 'all') {
            $query->where('location', $location);
        }
        if ($condition && $condition !== 'all') {
            $query->where('condition', $condition);
        }
        if ($yearStart) {
            $query->where('year_purchased', '>=', $yearStart);
        }
        if ($yearEnd) {
            $query->where('year_purchased', '<=', $yearEnd);
        }

        $assets = $query->get();

        // Grouping
        $grouped = $assets->groupBy(function($asset) use ($groupBy) {
            if ($groupBy === 'location') {
                return $asset->location ?: 'Tanpa Lokasi';
            }
            return is_object($asset->category) ? $asset->category->nama_kategori : ($asset->getAttribute('category') ?: 'Tanpa Kategori');
        });

        $rekap = [];
        foreach ($grouped as $key => $groupAssets) {
            $rekap[] = [
                'group_name' => $key,
                'total_aset' => $groupAssets->count(),
                'baik' => $groupAssets->where('condition', 'Baik')->count(),
                'rusak_ringan' => $groupAssets->where('condition', 'Rusak Ringan')->count(),
                'rusak_berat' => $groupAssets->where('condition', 'Rusak Berat')->count(),
                'total_nilai' => $groupAssets->sum('harga_perolehan')
            ];
        }

        // Sort by group name
        usort($rekap, function($a, $b) {
            return strcmp($a['group_name'], $b['group_name']);
        });

        return [
            'data' => $rekap,
            'groupBy' => $groupBy
        ];
    }

    public function rekap(Request $request)
    {
        $categories = AssetCategory::orderBy('nama_kategori', 'asc')->get();
        // Get distinct locations for filter
        $locations = Asset::where('status_aktif', true)->whereNotNull('location')->where('location', '!=', '')->distinct()->pluck('location');
        
        $rekapResult = [];
        $groupBy = 'category';
        
        // If there's any filter applied (or just simply always calculate)
        if ($request->has('group_by')) {
            $result = $this->getRekapData($request);
            $rekapResult = $result['data'];
            $groupBy = $result['groupBy'];
        } else {
            // Default load
            $result = $this->getRekapData($request);
            $rekapResult = $result['data'];
            $groupBy = $result['groupBy'];
        }
        
        return view('reports.rekap', compact('categories', 'locations', 'rekapResult', 'groupBy'));
    }

    public function exportRekap(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $result = $this->getRekapData($request);
        $rekapResult = $result['data'];
        $groupBy = $result['groupBy'];

        if (empty($rekapResult)) {
            return back()->with('error', 'Tidak ada data untuk diekspor dengan filter tersebut.');
        }

        if ($format === 'excel') {
            return Excel::download(new \App\Exports\RekapAssetExport($rekapResult, $groupBy), 'Rekapitulasi_Aset_' . date('Ymd_His') . '.xlsx');
        }

        // PDF Generation
        $pdfData = [
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'rekapResult' => $rekapResult,
            'groupBy' => $groupBy,
            'title' => 'Laporan Rekapitulasi Aset'
        ];

        $pdf = Pdf::loadView('reports.rekap_pdf', $pdfData)->setPaper('A4', 'portrait');
        return $pdf->download('Rekapitulasi_Aset_' . date('Ymd_His') . '.pdf');
    }

    public function exportPersediaanGlobal(Request $request)
    {
        $request->validate([
            'bulan_awal' => 'required|date_format:Y-m',
            'bulan_akhir' => 'required|date_format:Y-m|after_or_equal:bulan_awal',
            'jenis_laporan' => 'required|in:internal,dinas',
            'export_format' => 'required|in:pdf,excel'
        ]);

        $bulanAwal = $request->bulan_awal;
        $bulanAkhir = $request->bulan_akhir;
        $jenis = $request->jenis_laporan;
        $format = $request->export_format;

        if ($format === 'excel') {
            $fileName = 'Buku_Persediaan_' . $bulanAwal . '_sd_' . $bulanAkhir . '.xlsx';
            return Excel::download(new \App\Exports\LaporanPersediaanGlobalExport($bulanAwal, $bulanAkhir, $jenis), $fileName);
        }

        // PDF Generation
        $kategoris = \App\Models\Item::select('kategori_besar')->distinct()->pluck('kategori_besar');
        $allData = [];

        foreach ($kategoris as $kategori) {
            $items = \App\Models\Item::where('kategori_besar', $kategori)->with('transactions')->get();
            $kategoriName = str_replace(['_', '-'], ' ', $kategori);
            $kategoriName = ucwords($kategoriName);
            
            $sheet = new \App\Exports\GlobalTransaksiSheet($bulanAwal, $bulanAkhir, $jenis, $items, $kategoriName);
            $allData[$kategoriName] = $sheet->collection();
        }

        $pdfData = [
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'bulanAwalRaw' => $bulanAwal,
            'bulanAkhirRaw' => $bulanAkhir,
            'bulanAwal' => Carbon::parse($bulanAwal)->translatedFormat('F Y'),
            'bulanAkhir' => Carbon::parse($bulanAkhir)->translatedFormat('F Y'),
            'jenis' => $jenis,
            'allData' => $allData,
            'title' => 'Buku Persediaan Gudang (Logistik)'
        ];

        $pdf = Pdf::loadView('reports.persediaan_global_pdf', $pdfData)->setPaper('A4', 'landscape');
        return $pdf->download('Buku_Persediaan_' . $bulanAwal . '_sd_' . $bulanAkhir . '.pdf');
    }

    public function exportAktivitasAset(Request $request)
    {
        $request->validate([
            'bulan_awal' => 'required|date_format:Y-m',
            'bulan_akhir' => 'required|date_format:Y-m|after_or_equal:bulan_awal',
            'export_format' => 'required|in:pdf,excel'
        ]);

        $bulanAwal = $request->bulan_awal;
        $bulanAkhir = $request->bulan_akhir;
        $format = $request->export_format;
        
        $start = Carbon::parse($bulanAwal)->startOfMonth();
        $end = Carbon::parse($bulanAkhir)->endOfMonth();

        // 1. Aset Masuk
        $asetMasuk = \App\Models\Asset::with('category')->whereBetween('created_at', [$start, $end])->get();
        
        // 2. Pemeliharaan
        $pemeliharaan = \App\Models\AssetMaintenance::with('asset')->where('status', 'selesai')->whereBetween('tanggal_pelaksanaan', [$start, $end])->get();
        
        // 3. Penghapusan
        $penghapusan = \App\Models\AssetDisposal::with('asset')->whereBetween('tanggal_penghapusan', [$start, $end])->get();

        if ($format === 'excel') {
            $fileName = 'Riwayat_Aset_' . $bulanAwal . '_sd_' . $bulanAkhir . '.xlsx';
            return Excel::download(new \App\Exports\AktivitasAsetExport($asetMasuk, $pemeliharaan, $penghapusan, $bulanAwal, $bulanAkhir), $fileName);
        }

        // PDF Generation
        $pdfData = [
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'bulanAwal' => Carbon::parse($bulanAwal)->translatedFormat('F Y'),
            'bulanAkhir' => Carbon::parse($bulanAkhir)->translatedFormat('F Y'),
            'asetMasuk' => $asetMasuk,
            'pemeliharaan' => $pemeliharaan,
            'penghapusan' => $penghapusan,
            'title' => 'Laporan Riwayat & Aktivitas Aset Tetap'
        ];

        $pdf = Pdf::loadView('reports.aktivitas_aset_pdf', $pdfData)->setPaper('A4', 'landscape');
        return $pdf->download('Riwayat_Aset_' . $bulanAwal . '_sd_' . $bulanAkhir . '.pdf');
    }
}
