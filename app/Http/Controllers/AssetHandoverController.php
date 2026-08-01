<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHandover;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetHandoverController extends Controller
{
    public function index()
    {
        $handovers = AssetHandover::with(['asset', 'employee', 'items.asset'])->latest()->get();
        return view('bast.index', compact('handovers'));
    }

    public function create()
    {
        $assets = Asset::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();
        return view('bast.create', compact('assets', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'handover_date' => 'required|date',
            'keperluan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'sumber_dana' => 'nullable|string',
        ]);

        $handover = AssetHandover::create([
            'employee_id' => $request->employee_id,
            'handover_date' => $request->handover_date,
            'keperluan' => $request->keperluan,
            'keterangan' => $request->keterangan,
            'sumber_dana' => $request->sumber_dana,
            // 'asset_id' diabaikan atau dibiarkan null karena menggunakan tabel pivot
        ]);

        foreach ($request->asset_ids as $assetId) {
            \App\Models\AssetHandoverItem::create([
                'asset_handover_id' => $handover->id,
                'asset_id' => $assetId
            ]);
        }

        return redirect()->route('bast.show', $handover->id)->with('success', 'BAST berhasil dibuat untuk ' . count($request->asset_ids) . ' barang.');
    }

    public function show(AssetHandover $bast)
    {
        $bast->load('asset', 'employee', 'items.asset');
        return view('bast.print', compact('bast'));
    }
    
    public function destroy(AssetHandover $bast)
    {
        $bast->delete();
        return back()->with('success', 'BAST berhasil dihapus.');
    }
}
