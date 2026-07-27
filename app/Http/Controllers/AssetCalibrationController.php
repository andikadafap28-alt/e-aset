<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetCalibration;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

class AssetCalibrationController extends Controller
{
    public function store(Request $request, $asset_id)
    {
        $request->validate([
            'tanggal_kalibrasi' => 'required|date',
            'sertifikat' => 'required|string|max:255',
            'masa_berlaku' => 'nullable|date',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($asset_id);

        $calibrationData = [
            'asset_id' => $asset->id,
            'tanggal_kalibrasi' => $request->tanggal_kalibrasi,
            'sertifikat' => $request->sertifikat,
            'masa_berlaku' => $request->masa_berlaku,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/calibrations', $filename);
            $calibrationData['file_dokumen'] = 'storage/calibrations/' . $filename;
        }

        AssetCalibration::create($calibrationData);
        
        // Update asset's last calibration date if new calibration is more recent
        if (empty($asset->last_calibration) || $request->tanggal_kalibrasi >= $asset->last_calibration) {
            $asset->update(['last_calibration' => $request->tanggal_kalibrasi]);
        }

        return redirect()->back()->with('success', 'Riwayat kalibrasi berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $calibration = AssetCalibration::findOrFail($id);
        
        if ($calibration->file_dokumen) {
            $path = str_replace('storage/', 'public/', $calibration->file_dokumen);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
        
        $calibration->delete();

        return redirect()->back()->with('success', 'Riwayat kalibrasi berhasil dihapus.');
    }
}
