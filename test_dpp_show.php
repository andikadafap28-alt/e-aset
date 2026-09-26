<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    auth()->loginUsingId(1);
    
    // Create a mock DPP object to test the view rendering
    $dpp = App\Models\Dpp::first();
    if (!$dpp) {
        $dpp = new App\Models\Dpp([
            'id' => 1,
            'nomor_surat' => '123',
            'kode_rup' => '456',
            'nama_paket' => 'Test',
            'tanggal_dpp' => now(),
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now(),
        ]);
        $dpp->id = 1;
    }
    
    $html = view('aset.dpp.show', compact('dpp'))->render();
    echo "View rendered successfully. Length: " . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
