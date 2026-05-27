<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\Item;
use App\Models\InventoryTransaction;

DB::transaction(function() {
    $assets = Asset::whereNotNull('pengadaan_id')->get();
    foreach ($assets as $asset) {
        // Check if transaction already exists for this asset to avoid double run
        $exists = InventoryTransaction::where('item_id', $asset->pengadaan_id)
            ->where('keterangan', 'like', "%{$asset->asset_code}%")
            ->exists();
        
        if (!$exists) {
            $item = Item::find($asset->pengadaan_id);
            if ($item) {
                InventoryTransaction::create([
                    'item_id' => $item->id,
                    'jenis_transaksi' => 'keluar',
                    'jumlah' => 1,
                    'harga_satuan' => $asset->harga_perolehan,
                    'keterangan' => 'Didaftarkan sebagai Aset: ' . $asset->asset_code,
                    'tanggal_transaksi' => $asset->created_at
                ]);
                $item->decrement('stok_sekarang', 1);
                echo "Processed asset {$asset->asset_code}\n";
            }
        }
    }
});
echo "Fix applied\n";
