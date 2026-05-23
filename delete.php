<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = \App\Models\ProcurementFile::whereIn('file_name', [
    '2026-03-30_PENGADAAN_SP_TENSIMETER.pdf',
    '2026-03-30_SP_tensimeter_pt_lntisumber_hasil_sempurna_global_cabang_surabaya_2_C1ZGK.pdf'
])->get();

foreach($files as $f) {
    try {
        \Illuminate\Support\Facades\Storage::disk('google')->delete($f->path_gdrive);
        echo "Deleted from Drive: " . $f->file_name . "\n";
    } catch(\Exception $e) {
        echo "Error deleting from Drive: " . $e->getMessage() . "\n";
    }
    $f->delete();
    echo "Deleted from DB: " . $f->file_name . "\n";
}
echo "Done\n";
