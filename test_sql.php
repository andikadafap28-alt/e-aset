<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = \Illuminate\Support\Facades\DB::table('assets')
->leftJoin('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
->whereNotNull('assets.harga_perolehan')
->where('assets.harga_perolehan', '>', 0)
->whereNotNull('asset_categories.umur_ekonomis')
->where('asset_categories.umur_ekonomis', '>', 0)
->whereNotNull('assets.year_purchased')
->select(\Illuminate\Support\Facades\DB::raw('
    SUM(
        LEAST(
            GREATEST(CAST(EXTRACT(YEAR FROM CURRENT_DATE) AS INTEGER) - CAST(assets.year_purchased AS INTEGER), 0) * ((assets.harga_perolehan - 1) / asset_categories.umur_ekonomis),
            assets.harga_perolehan - 1
        )
    ) as total_depreciation
'))
->first();

print_r($result);
