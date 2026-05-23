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
}
