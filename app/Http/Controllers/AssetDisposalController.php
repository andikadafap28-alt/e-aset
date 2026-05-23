<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDisposal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetDisposalController extends Controller
{
    public function store(Request $request, $assetId)
    {
        $asset = Asset::findOrFail($assetId);

        $validated = $request->validate([
            'tanggal_penghapusan' => 'required|date',
            'alasan' => 'required|string',
            'catatan' => 'nullable|string'
        ]);

        $disposal = new AssetDisposal();
        $disposal->asset_id = $asset->id;
        $disposal->tanggal_penghapusan = $validated['tanggal_penghapusan'];
        $disposal->alasan = $validated['alasan'];
        $disposal->catatan = $validated['catatan'];

        // Update Asset status
        $asset->status_aktif = false;
        $asset->save();

        // Generate PDF Berita Acara
        $pdfHtml = view('aset.ba_penghapusan_pdf', compact('asset', 'disposal'))->render();
        $pdf = Pdf::loadHTML($pdfHtml)->setPaper('A4', 'portrait');
        
        $fileName = 'BA_Penghapusan_' . Str::slug($asset->asset_code) . '_' . time() . '.pdf';
        $filePath = 'dokumen_penghapusan/' . $fileName;

        // Ensure directory exists
        if (!Storage::disk('public')->exists('dokumen_penghapusan')) {
            Storage::disk('public')->makeDirectory('dokumen_penghapusan');
        }

        // Save PDF to public storage
        Storage::disk('public')->put($filePath, $pdf->output());

        $disposal->ba_path = $filePath;
        $disposal->save();

        return back()->with('success', 'Aset berhasil dihapus / disposed. Berita Acara telah dibuat.');
    }
}
