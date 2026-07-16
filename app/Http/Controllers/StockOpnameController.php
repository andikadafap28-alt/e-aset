<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stockOpnames = StockOpname::with('user')->latest()->paginate(10);
        return view('stock_opname.index', compact('stockOpnames'));
    }

    public function create()
    {
        // Ambil daftar lokasi unik dari tabel assets
        $locations = Asset::select('location')->whereNotNull('location')->distinct()->pluck('location');
        return view('stock_opname.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'location' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $stockOpname = StockOpname::create([
            'tanggal' => $request->tanggal,
            'location' => $request->location === 'all' ? null : $request->location,
            'user_id' => Auth::id(),
            'notes' => $request->notes,
            'status' => 'In Progress',
        ]);

        return redirect()->route('stock-opname.scan', $stockOpname->id)
            ->with('success', 'Sesi Stock Opname berhasil dimulai!');
    }

    public function scan($id)
    {
        $stockOpname = StockOpname::findOrFail($id);
        
        if ($stockOpname->status === 'Completed') {
            return redirect()->route('stock-opname.show', $stockOpname->id)
                ->with('error', 'Sesi Stock Opname ini sudah selesai.');
        }

        $scannedCount = StockOpnameDetail::where('stock_opname_id', $stockOpname->id)->count();

        return view('stock_opname.scan', compact('stockOpname', 'scannedCount'));
    }

    public function recordScan(Request $request, $id)
    {
        $stockOpname = StockOpname::findOrFail($id);

        $request->validate([
            'asset_code' => 'required|string'
        ]);

        $asset = Asset::where('asset_code', $request->asset_code)->first();

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Kode Aset tidak ditemukan di database.'
            ], 404);
        }

        // Cek apakah sudah discan sebelumnya di sesi ini
        $existingScan = StockOpnameDetail::where('stock_opname_id', $stockOpname->id)
            ->where('asset_id', $asset->id)
            ->first();

        if ($existingScan) {
            return response()->json([
                'success' => false,
                'message' => 'Aset ini sudah di-scan sebelumnya.'
            ], 400);
        }

        // Tentukan Status (Matched atau Misplaced)
        $expectedLocation = $asset->location;
        $actualLocation = $stockOpname->location;
        
        $status = 'Matched';
        if ($stockOpname->location && $stockOpname->location !== 'all') {
            if (strtolower($expectedLocation) !== strtolower($actualLocation)) {
                $status = 'Misplaced';
            }
        }

        $detail = StockOpnameDetail::create([
            'stock_opname_id' => $stockOpname->id,
            'asset_id' => $asset->id,
            'expected_location' => $expectedLocation,
            'actual_location' => $actualLocation,
            'status' => $status,
        ]);

        $scannedCount = StockOpnameDetail::where('stock_opname_id', $stockOpname->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil di-scan!',
            'data' => [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'status' => $status,
                'scanned_count' => $scannedCount
            ]
        ]);
    }

    public function finish(Request $request, $id)
    {
        $stockOpname = StockOpname::findOrFail($id);

        if ($stockOpname->status === 'Completed') {
            return redirect()->route('stock-opname.show', $stockOpname->id);
        }

        DB::transaction(function () use ($stockOpname) {
            // 1. Dapatkan daftar ID aset yang seharusnya ada di lokasi ini
            $expectedAssetsQuery = Asset::where('status_aktif', true);
            
            if ($stockOpname->location && $stockOpname->location !== 'all') {
                $expectedAssetsQuery->where('location', $stockOpname->location);
            }
            
            $expectedAssetIds = $expectedAssetsQuery->pluck('id')->toArray();

            // 2. Dapatkan daftar ID aset yang berhasil di-scan (Matched/Misplaced)
            $scannedAssetIds = StockOpnameDetail::where('stock_opname_id', $stockOpname->id)
                ->pluck('asset_id')->toArray();

            // 3. Cari aset yang Missing (Seharusnya ada tapi tidak di-scan)
            $missingAssetIds = array_diff($expectedAssetIds, $scannedAssetIds);

            // 4. Catat aset Missing ke tabel details
            $missingDetails = [];
            foreach ($missingAssetIds as $missingId) {
                $missingDetails[] = [
                    'stock_opname_id' => $stockOpname->id,
                    'asset_id' => $missingId,
                    'expected_location' => $stockOpname->location,
                    'actual_location' => null, // Tidak diketahui
                    'status' => 'Missing',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (count($missingDetails) > 0) {
                StockOpnameDetail::insert($missingDetails);
            }

            // 5. Update status Stock Opname
            $stockOpname->update([
                'status' => 'Completed'
            ]);
        });

        return redirect()->route('stock-opname.show', $stockOpname->id)
            ->with('success', 'Stock Opname berhasil diselesaikan. Berikut adalah laporannya.');
    }

    public function show($id)
    {
        $stockOpname = StockOpname::with(['user', 'details.asset'])->findOrFail($id);
        
        $stats = [
            'total' => $stockOpname->details->count(),
            'matched' => $stockOpname->details->where('status', 'Matched')->count(),
            'misplaced' => $stockOpname->details->where('status', 'Misplaced')->count(),
            'missing' => $stockOpname->details->where('status', 'Missing')->count(),
        ];

        return view('stock_opname.show', compact('stockOpname', 'stats'));
    }
}
