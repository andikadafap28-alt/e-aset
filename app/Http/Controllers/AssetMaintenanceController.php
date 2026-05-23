<?php

namespace App\Http\Controllers;

use App\Models\AssetMaintenance;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = AssetMaintenance::with('asset')->orderBy('tanggal_jadwal', 'asc')->get();
        $assets = Asset::all();
        return view('aset.pemeliharaan', compact('maintenances', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'jenis_pemeliharaan' => 'required|string|max:255',
            'tanggal_jadwal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        AssetMaintenance::create([
            'asset_id' => $request->asset_id,
            'jenis_pemeliharaan' => $request->jenis_pemeliharaan,
            'tanggal_jadwal' => $request->tanggal_jadwal,
            'catatan' => $request->catatan,
            'status' => 'menunggu',
        ]);

        return back()->with('success', 'Jadwal pemeliharaan berhasil ditambahkan.');
    }

    public function complete(Request $request, $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        
        $request->validate([
            'tanggal_pelaksanaan' => 'required|date',
            'biaya' => 'nullable|numeric',
            'catatan_hasil' => 'nullable|string',
        ]);

        $maintenance->update([
            'status' => 'selesai',
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'biaya' => $request->biaya ?? 0,
            'catatan' => $maintenance->catatan . "\n\nCatatan Hasil: " . $request->catatan_hasil,
        ]);

        return back()->with('success', 'Pemeliharaan berhasil diselesaikan.');
    }

    public function cancel($id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $maintenance->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Jadwal pemeliharaan dibatalkan.');
    }
}
