<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower('Untuk stok paracetamol dan nilainya?')));
$stopwords = ['jumlah', 'stok', 'berapa', 'obat', 'barang', 'ini', 'itu', 'di', 'pada', 'dari', 'yang', 'dan', 'atau', 'untuk', 'ada', 'tidak', 'tolong', 'carikan', 'tampilkan', 'apakah', 'sisa', 'klo', 'kalo', 'kalau', 'jika', 'saya', 'punya', 'total', 'totalnya', 'hitung', 'dihitung', 'dengan', 'rupiah', 'harga', 'harganya'];
$keywords = array_diff($words, $stopwords);
$keywords = array_filter($keywords, fn($w) => strlen($w) > 3);
print_r($keywords);
$q = App\Models\Item::query();
foreach ($keywords as $kw) {
    $q->orWhere('nama_barang', 'ILIKE', '%' . $kw . '%');
}
print_r($q->get()->pluck('nama_barang')->toArray());
