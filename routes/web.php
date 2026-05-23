<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Jalur Utama: Saat membuka aplikasi pertama kali, arahkan ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// Rute Bantuan untuk Testing Role (Tanpa Password)
Route::get('/dev/login/{id}', function ($id) {
    auth()->loginUsingId($id);
    return back()->with('success', 'Berhasil login sebagai: ' . auth()->user()->name . ' (Role: ' . auth()->user()->role . ')');
});

// Rute untuk mencegah Supabase dari paused (Keep Alive Ping)
Route::get('/api/keep-alive', function () {
    try {
        \Illuminate\Support\Facades\DB::select('SELECT 1');
        return response()->json(['status' => 'success', 'message' => 'Supabase pinged successfully.']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// Manajemen Aset
Route::prefix('aset')->name('aset.')->group(function () {
    // 1. Master Data Aset (Buku Induk Pusat)
    Route::get('/data/items', [AssetController::class, 'index'])->name('data.items');

    // Master Kategori Aset
    Route::get('/categories', [\App\Http\Controllers\AssetCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\AssetCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'destroy'])->name('categories.destroy');

    // Penghapusan Aset
    Route::post('/disposal/{assetId}', [\App\Http\Controllers\AssetDisposalController::class, 'store'])->name('disposal.store');

    // 2. Sub-menu Aset Lainnya
    Route::get('/pengadaan/items', [\App\Http\Controllers\InventoryController::class, 'index'])
        ->defaults('kategori_besar', 'pengadaan')
        ->name('pengadaan.items');

    // Rute detail pengadaan
    Route::get('/pengadaan/{id}/detail', [\App\Http\Controllers\InventoryController::class, 'show'])
        ->defaults('kategori_besar', 'pengadaan')
        ->name('pengadaan.detail');

    // 2. Pemeliharaan Aset
    Route::get('/pemeliharaan', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('pemeliharaan.index');
    Route::post('/pemeliharaan', [\App\Http\Controllers\AssetMaintenanceController::class, 'store'])->name('pemeliharaan.store');
    Route::post('/pemeliharaan/{id}/complete', [\App\Http\Controllers\AssetMaintenanceController::class, 'complete'])->name('pemeliharaan.complete');
    Route::post('/pemeliharaan/{id}/cancel', [\App\Http\Controllers\AssetMaintenanceController::class, 'cancel'])->name('pemeliharaan.cancel');
    Route::get('/monitoring/items', [AssetController::class, 'monitoring'])->name('monitoring.items');
    Route::get('/pelabelan/items', [AssetController::class, 'pelabelan'])->name('pelabelan.items');
    Route::post('/pelabelan/print', [AssetController::class, 'printLabels'])->name('pelabelan.print');

    // Keranjang Cetak Label (Print Queue)
    Route::get('/print-queue/data', [AssetController::class, 'getPrintQueueData'])->name('print-queue.data');
    Route::post('/print-queue/remove/{id}', [AssetController::class, 'removeFromPrintQueue'])->name('print-queue.remove');
    Route::post('/print-queue/clear', [AssetController::class, 'clearPrintQueue'])->name('print-queue.clear');
    Route::get('/print-queue/print', [AssetController::class, 'printQueue'])->name('print-queue.print');
    Route::get('/mutasi/items', [AssetController::class, 'mutasi'])->name('mutasi.items');
    Route::get('/mutasi/create', [AssetController::class, 'createMutasi'])->name('mutasi.create');
    Route::post('/mutasi', [AssetController::class, 'storeMutasi'])->name('mutasi.store');

    // 3. Peminjaman Aset
    Route::get('/peminjaman', [\App\Http\Controllers\AssetLoanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [\App\Http\Controllers\AssetLoanController::class, 'store'])->name('peminjaman.store');
    Route::post('/peminjaman/{id}/kembali', [\App\Http\Controllers\AssetLoanController::class, 'returnLoan'])->name('peminjaman.return');
    Route::post('/peminjaman/{id}/approve', [\App\Http\Controllers\AssetLoanController::class, 'approveLoan'])->name('peminjaman.approve');
    Route::post('/peminjaman/{id}/reject', [\App\Http\Controllers\AssetLoanController::class, 'rejectLoan'])->name('peminjaman.reject');
    // Import Kode 108 (Master)
    Route::post('/import-kode-108', [InventoryController::class, 'importKode108'])->name('import-kode-108');
});

// Verifikasi BAST (Publik)
Route::get('/verify/bast/loan/{id}', [\App\Http\Controllers\AssetLoanController::class, 'verifyBast'])->name('verify.bast');

// Resource route diletakkan di bawah
Route::resource('aset', AssetController::class);

// Pelaporan & Analytics
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::post('/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('generate');
    Route::get('/aset/pdf', [\App\Http\Controllers\ReportController::class, 'downloadAssetReport'])->name('aset.pdf');
});

// Melihat/Mengunduh Dokumen Pengadaan langsung dari Google Drive
Route::get('/procurement-file/{id}', [InventoryController::class, 'viewProcurementFile']);
Route::delete('/procurement-file/{id}', [InventoryController::class, 'destroyProcurementFile'])->name('procurement.destroy-file');


// Asisten AI WhatsApp
Route::prefix('asisten')->name('asisten.')->group(function () {
    Route::get('/chats', [\App\Http\Controllers\AssistantController::class, 'index'])->name('chats');
});

/*
|--------------------------------------------------------------------------
| Modul RAKSA (Multi-Modul)
|--------------------------------------------------------------------------
*/

// API Dropdown Kode 108
Route::get('/ajax/kode-108', [InventoryController::class, 'getKode108']);

// QR Scanner
Route::get('/scanner', [InventoryController::class, 'scannerPage']);
Route::get('/scan/{id}', [InventoryController::class, 'scanResult']);

Route::prefix('{kategori_besar}')->group(function () {
    // Menambah master barang baru
    Route::get('/tambah', [InventoryController::class, 'createMaster']);
    Route::post('/tambah', [InventoryController::class, 'storeMaster']);
    // Menampilkan halaman tabel persediaan utama
    Route::get('/items', [InventoryController::class, 'index']);

    // Menampilkan halaman tabel master barang khusus untuk edit/hapus
    Route::get('/master', [InventoryController::class, 'masterList']);

    // Menampilkan form edit master barang
    Route::get('/{id}/edit-master', [InventoryController::class, 'editMaster']);

    // Menyimpan perubahan data master barang
    Route::put('/{id}/edit-master', [InventoryController::class, 'updateMaster']);

    // Menampilkan detail spesifik barang dan riwayat mutasinya
    Route::get('/{id}/detail', [InventoryController::class, 'show']);
    Route::get('/{id}/kartu-stok/pdf', [InventoryController::class, 'printKartuStok']);

    // Mengunggah dokumen pengadaan ke Google Drive
    Route::post('/{id}/scan-procurement', [InventoryController::class, 'scanProcurementFile']);
    Route::post('/{id}/procurement-files', [InventoryController::class, 'uploadProcurementFile']);

    // Menghapus master barang dari database
    Route::delete('/{id}', [InventoryController::class, 'destroy']);

    // AI Vision OCR Feature
    Route::post('/{id}/ai-extract', [InventoryController::class, 'extractAi']);
    Route::post('/{id}/ai-store', [InventoryController::class, 'storeAiTransactions']);

    // Menampilkan form pencatatan mutasi barang
    Route::get('/transaksi/tambah', [InventoryController::class, 'createTransaction']);

    // Memproses mutasi barang dan mengkalkulasi stok
    Route::post('/transaksi/tambah', [InventoryController::class, 'storeTransaction']);

    // Menghapus satu riwayat transaksi spesifik dan mengembalikan stok
    Route::delete('/transaksi/{id}', [InventoryController::class, 'destroyTransaction']);

    // Halaman Stock Opname
    Route::get('/opname', [InventoryController::class, 'opnamePage']);
    Route::post('/opname', [InventoryController::class, 'storeOpname']);

    // Halaman Manajemen Hutang (SPJ)
    Route::get('/hutang', [InventoryController::class, 'hutangPage']);
    Route::post('/hutang/{id}/spj', [InventoryController::class, 'updateSpj']);

    // Fitur Edit Transaksi
    Route::get('/transaksi/{id}/edit', [InventoryController::class, 'editTransaction']);
    Route::put('/transaksi/{id}', [InventoryController::class, 'updateTransaction']);

    // Menampilkan halaman pop-up/filter export (Pilih bulan & jenis laporan)
    Route::get('/export', [InventoryController::class, 'exportPage']);

    // Memproses dan mengunduh file Excel sesuai filter
    Route::post('/export/download', [InventoryController::class, 'downloadExcel']);

    // Memproses import file Excel
    Route::post('/import', [InventoryController::class, 'importLogistik']);

    // Cetak Label Aset/Barang (Kode 108)
    Route::get('/{id}/label', [InventoryController::class, 'printLabel']);
    
});

// Rute untuk Webhook WhatsApp (Meta Cloud API)
Route::get('/webhook', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);
Route::get('/webhook/', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);