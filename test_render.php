<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/pengadaan/tambah',
        'POST',
        [
            'nama_barang' => 'Test Item',
            'kategori' => 'Alat Kesehatan',
            'satuan' => 'Unit',
            'stok_sekarang' => 10,
            'harga_satuan' => 10000,
            'tahun_pengadaan' => '2024'
        ]
    )
);

echo "STATUS: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() != 200) {
    echo $response->getContent();
} else {
    echo "Peminjaman OK\n";
}
