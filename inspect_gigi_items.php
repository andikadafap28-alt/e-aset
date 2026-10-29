<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$h1 = \App\Models\AssetHandover::with(['employee', 'items.asset'])->find(1);
$items = $h1->items;
echo "Total items in BAST 1: " . $items->count() . "\n";

// Group items by name, merk, keterangan, bahan
$grouped = [];
foreach ($items as $item) {
    $a = $item->asset;
    if (!$a) continue;
    $key = $a->name . '|||' . $a->merk . '|||' . $a->keterangan . '|||' . $a->bahan;
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'name' => $a->name,
            'merk' => $a->merk,
            'keterangan' => $a->keterangan,
            'bahan' => $a->bahan,
            'kode_108' => $a->kode_108,
            'year_purchased' => $a->year_purchased,
            'count' => 0,
            'asset_ids' => []
        ];
    }
    $grouped[$key]['count']++;
    $grouped[$key]['asset_ids'][] = $a->id;
}

echo "\nBAST 1 GROUPED ITEMS (" . count($grouped) . " unique types):\n";
$no = 1;
foreach ($grouped as $g) {
    echo "{$no}. Name: {$g['name']} | Jml: {$g['count']} | Merk: '{$g['merk']}' | Ket: '{$g['keterangan']}' | Bahan: '{$g['bahan']}' | Kode: {$g['kode_108']} | Year: {$g['year_purchased']}\n";
    $no++;
}

echo "\nOTHER 2026 ASSETS:\n";
$other2026 = \App\Models\Asset::where('year_purchased', 2026)
    ->whereNotIn('id', $items->pluck('asset_id'))
    ->get();
echo "Found other 2026: " . $other2026->count() . "\n";
foreach ($other2026 as $a) {
    echo "- ID: {$a->id} | Name: {$a->name} | Ket: {$a->keterangan} | Room: {$a->room_id}\n";
}
