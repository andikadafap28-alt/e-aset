<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DepreciationController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month and year
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Report period date (end of the selected month)
        $reportDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get all assets with their category
        $assets = Asset::with('category')->get()->map(function ($asset) use ($reportDate) {
            $nilaiPerolehan = floatval($asset->harga_perolehan ?? 0);
            
            // Assuming category->umur_ekonomis is in years, convert to months
            $masaManfaatTahun = $asset->category->umur_ekonomis ?? 0;
            $masaManfaatBulan = $masaManfaatTahun * 12;

            $penyusutanPerBulan = 0;
            if ($masaManfaatBulan > 0) {
                $penyusutanPerBulan = $nilaiPerolehan / $masaManfaatBulan;
            }

            $masaManfaatDilalui = 0;
            if ($asset->year_purchased || $asset->tanggal_bast) {
                if (!empty($asset->tanggal_bast)) {
                    $purchaseDate = Carbon::parse($asset->tanggal_bast)->startOfMonth();
                } else {
                    // Default to Jan 1st of year_purchased
                    $purchaseDate = Carbon::createFromDate($asset->year_purchased, 1, 1)->startOfMonth();
                }

                if ($purchaseDate->lte($reportDate)) {
                    // Calculate elapsed months (including the purchase month)
                    $diffInMonths = $purchaseDate->diffInMonths($reportDate) + 1;
                    $masaManfaatDilalui = min($diffInMonths, $masaManfaatBulan);
                }
            }

            $akumulasiPenyusutan = $masaManfaatDilalui * $penyusutanPerBulan;
            $nilaiBuku = $nilaiPerolehan - $akumulasiPenyusutan;

            // Handle potential floating point inaccuracy
            if ($nilaiBuku < 0.01) {
                $nilaiBuku = 0;
            }

            // Append custom attributes to the asset for the view
            $asset->masa_manfaat_bulan = $masaManfaatBulan;
            $asset->penyusutan_per_bulan = $penyusutanPerBulan;
            $asset->masa_manfaat_dilalui = $masaManfaatDilalui;
            $asset->akumulasi_penyusutan = $akumulasiPenyusutan;
            $asset->nilai_buku = $nilaiBuku;
            $asset->sisa_masa_manfaat = max(0, $masaManfaatBulan - $masaManfaatDilalui);

            return $asset;
        });

        // Grouping by category (like in the PDF)
        $groupedAssets = $assets->groupBy(function($item) {
            return $item->category->nama_kategori ?? 'Tanpa Kategori';
        });

        return view('aset.penyusutan', compact('groupedAssets', 'month', 'year'));
    }
}
