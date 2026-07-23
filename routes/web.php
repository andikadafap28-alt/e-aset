<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk mencegah Supabase dari paused (Keep Alive Ping)
Route::get('/api/keep-alive', function () {
    try {
        \Illuminate\Support\Facades\DB::select('SELECT 1');
        return response()->json(['status' => 'success', 'message' => 'Supabase pinged successfully.']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// Verifikasi BAST (Publik)
Route::get('/verify/bast/loan/{id}', [\App\Http\Controllers\AssetLoanController::class, 'verifyBast'])->name('verify.bast');

// Halaman Publik Detail Aset (Scan QR)
Route::get('/item/{asset_code}', [AssetController::class, 'publicShow'])->name('public.show');
Route::post('/item/{asset_code}/verify', [AssetController::class, 'verifyPublicPassword'])->name('public.verify');

// Webhook
Route::get('/webhook', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);
Route::get('/webhook/', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);
Route::get('/webhook/telegram/setup', [\App\Http\Controllers\TelegramWebhookController::class, 'setupWebhook']);
Route::post('/webhook/telegram', [\App\Http\Controllers\TelegramWebhookController::class, 'handle']);

// Rute Bantuan untuk Testing Role (Tanpa Password)
Route::get('/dev/login/{id}', function ($id) {
    auth()->loginUsingId($id);
    return back()->with('success', 'Berhasil login sebagai: ' . auth()->user()->name . ' (Role: ' . auth()->user()->role . ')');
});

// ==========================================
// RUTE TERPROTEKSI (AUTH)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Jalur Utama: Saat membuka aplikasi pertama kali, arahkan ke dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard (Bisa diakses semua role)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ==========================================
    // KHUSUS ADMIN (Pengaturan Sistem & Integrasi)
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        
        Route::prefix('asisten')->name('asisten.')->group(function () {
            Route::get('/wa', [\App\Http\Controllers\AssistantController::class, 'waChats'])->name('wa');
            Route::get('/tele', [\App\Http\Controllers\AssistantController::class, 'teleChats'])->name('tele');
        });
    });

    // ==========================================
    // RUTE UMUM (Manajemen Aset & Laporan & RAKSA)
    // Sebagian rute POST/PUT/DELETE akan diproteksi di Controller masing-masing
    // jika manajemen tidak boleh menyimpan. Atau bisa ditambah middleware 'role:admin'.
    // Karena permintaan adalah "semuanya saya yang handle" dan manajemen "hanya melihat",
    // maka kita bisa membiarkan route tetap terbuka tapi di filter di Controller,
    // ATAU kita pasang role:admin pada aksi tulis/ubah/hapus di route.
    // Demi keamanan, kita batasi aksi "menyimpan" di Controller untuk Manajemen dan User.
    // ==========================================

    Route::prefix('aset')->name('aset.')->group(function () {
        Route::get('/data/items', [AssetController::class, 'index'])->name('data.items');
        Route::get('/bmd', [AssetController::class, 'bmdIndex'])->name('bmd.index');
        Route::post('/bmd/import', [AssetController::class, 'bmdImport'])->name('bmd.import');
        Route::get('/aspak', [AssetController::class, 'aspakIndex'])->name('aspak.index');

        Route::get('/categories', [\App\Http\Controllers\AssetCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\AssetCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [\App\Http\Controllers\AssetCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::post('/disposal/{assetId}', [\App\Http\Controllers\AssetDisposalController::class, 'store'])->name('disposal.store');

        Route::get('/pengadaan/items', [\App\Http\Controllers\InventoryController::class, 'index'])
            ->defaults('kategori_besar', 'pengadaan')
            ->name('pengadaan.items');
        Route::get('/pengadaan/{id}/detail', [\App\Http\Controllers\InventoryController::class, 'show'])
            ->defaults('kategori_besar', 'pengadaan')
            ->name('pengadaan.detail');

        Route::get('/bantuan-sarpras/items', [\App\Http\Controllers\InventoryController::class, 'index'])
            ->defaults('kategori_besar', 'bantuan_sarpras')
            ->name('bantuan_sarpras.items');
        Route::get('/bantuan-sarpras/{id}/detail', [\App\Http\Controllers\InventoryController::class, 'show'])
            ->defaults('kategori_besar', 'bantuan_sarpras')
            ->name('bantuan_sarpras.detail');

        Route::get('/pemeliharaan', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('pemeliharaan.index');
        Route::post('/pemeliharaan', [\App\Http\Controllers\AssetMaintenanceController::class, 'store'])->name('pemeliharaan.store');
        Route::post('/pemeliharaan/{id}/complete', [\App\Http\Controllers\AssetMaintenanceController::class, 'complete'])->name('pemeliharaan.complete');
        Route::post('/pemeliharaan/{id}/cancel', [\App\Http\Controllers\AssetMaintenanceController::class, 'cancel'])->name('pemeliharaan.cancel');
        Route::get('/monitoring/items', [AssetController::class, 'monitoring'])->name('monitoring.items');
        Route::get('/pelabelan/items', [AssetController::class, 'pelabelan'])->name('pelabelan.items');
        Route::post('/aset/pelabelan/print', [AssetController::class, 'printLabels'])->name('aset.pelabelan.print');

        Route::post('/aset/{id}/koreksi', [AssetController::class, 'storeCorrection'])->name('aset.koreksi.store');

        Route::get('/print-queue/data', [AssetController::class, 'getPrintQueueData'])->name('print-queue.data');
        Route::post('/print-queue/remove/{id}', [AssetController::class, 'removeFromPrintQueue'])->name('print-queue.remove');
        Route::post('/print-queue/clear', [AssetController::class, 'clearPrintQueue'])->name('print-queue.clear');
        Route::get('/print-queue/print', [AssetController::class, 'printQueue'])->name('print-queue.print');
        Route::get('/mutasi/items', [AssetController::class, 'mutasi'])->name('mutasi.items');
        Route::get('/mutasi/create', [AssetController::class, 'createMutasi'])->name('mutasi.create');
        Route::post('/mutasi', [AssetController::class, 'storeMutasi'])->name('mutasi.store');

        Route::get('/peminjaman', [\App\Http\Controllers\AssetLoanController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [\App\Http\Controllers\AssetLoanController::class, 'store'])->name('peminjaman.store');
        Route::post('/peminjaman/{id}/kembali', [\App\Http\Controllers\AssetLoanController::class, 'returnLoan'])->name('peminjaman.return');
        Route::post('/peminjaman/{id}/approve', [\App\Http\Controllers\AssetLoanController::class, 'approveLoan'])->name('peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [\App\Http\Controllers\AssetLoanController::class, 'rejectLoan'])->name('peminjaman.reject');
        
        Route::post('/import-kode-108', [InventoryController::class, 'importKode108'])->name('import-kode-108');
    });

    Route::post('/aset/import-kode-108', [\App\Http\Controllers\AssetController::class, 'importKode108'])->name('aset.import-kode-108');
    Route::post('/aset/bulk-action', [\App\Http\Controllers\AssetController::class, 'bulkAction'])->name('aset.bulk-action');
    Route::resource('aset', AssetController::class);

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('generate');
        Route::get('/aset/pdf', [\App\Http\Controllers\ReportController::class, 'downloadAssetReport'])->name('aset.pdf');
        Route::get('/penyusutan', [\App\Http\Controllers\ReportController::class, 'depreciation'])->name('penyusutan');
        Route::get('/audit-log', [\App\Http\Controllers\ReportController::class, 'auditLog'])->name('audit-log');
        
        Route::get('/rekap', [\App\Http\Controllers\ReportController::class, 'rekap'])->name('rekap');
        Route::get('/rekap/export', [\App\Http\Controllers\ReportController::class, 'exportRekap'])->name('rekap.export');
        Route::post('/persediaan-global/export', [\App\Http\Controllers\ReportController::class, 'exportPersediaanGlobal'])->name('persediaan-global.export');
        Route::post('/aktivitas-aset/export', [\App\Http\Controllers\ReportController::class, 'exportAktivitasAset'])->name('aktivitas-aset.export');

        Route::get('/rekonsiliasi', [\App\Http\Controllers\ReportController::class, 'rekonsiliasi'])->name('rekonsiliasi');
        Route::post('/rekonsiliasi/export', [\App\Http\Controllers\ReportController::class, 'exportRekonsiliasi'])->name('rekonsiliasi.export');
    });

    Route::get('/procurement-file/{id}', [InventoryController::class, 'viewProcurementFile']);
    Route::delete('/procurement-file/{id}', [InventoryController::class, 'destroyProcurementFile'])->name('procurement.destroy-file');

    Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StockOpnameController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\StockOpnameController::class, 'store'])->name('store');
        Route::get('/{id}/scan', [\App\Http\Controllers\StockOpnameController::class, 'scan'])->name('scan');
        Route::post('/{id}/record', [\App\Http\Controllers\StockOpnameController::class, 'recordScan'])->name('record');
        Route::post('/{id}/finish', [\App\Http\Controllers\StockOpnameController::class, 'finish'])->name('finish');
        Route::get('/{id}', [\App\Http\Controllers\StockOpnameController::class, 'show'])->name('show');
    });

    Route::get('/ajax/kode-108', [InventoryController::class, 'getKode108']);
    Route::get('/scanner', [InventoryController::class, 'scannerPage']);
    Route::get('/scan/{id}', [InventoryController::class, 'scanResult']);

    Route::prefix('{kategori_besar}')->group(function () {
        Route::get('/tambah', [InventoryController::class, 'createMaster']);
        Route::post('/tambah', [InventoryController::class, 'storeMaster']);
        Route::get('/items', [InventoryController::class, 'index']);
        Route::get('/master', [InventoryController::class, 'masterList']);
        Route::get('/{id}/edit-master', [InventoryController::class, 'editMaster']);
        Route::put('/{id}/edit-master', [InventoryController::class, 'updateMaster']);
        Route::get('/{id}/detail', [InventoryController::class, 'show']);
        Route::get('/{id}/kartu-stok/pdf', [InventoryController::class, 'printKartuStok']);
        Route::post('/{id}/scan-procurement', [InventoryController::class, 'scanProcurementFile']);
        Route::post('/{id}/procurement-files', [InventoryController::class, 'uploadProcurementFile']);
        Route::delete('/{id}', [InventoryController::class, 'destroy']);
        Route::post('/{id}/ai-extract', [InventoryController::class, 'extractAi']);
        Route::post('/{id}/ai-store', [InventoryController::class, 'storeAiTransactions']);
        
        Route::get('/transaksi/tambah', [InventoryController::class, 'createTransaction']);
        Route::post('/transaksi/tambah', [InventoryController::class, 'storeTransaction']);
        Route::delete('/transaksi/{id}', [InventoryController::class, 'destroyTransaction']);
        
        Route::get('/opname', [InventoryController::class, 'opnamePage']);
        Route::post('/opname', [InventoryController::class, 'storeOpname']);
        
        Route::get('/hutang', [InventoryController::class, 'hutangPage']);
        Route::post('/hutang/{id}/spj', [InventoryController::class, 'updateSpj']);
        
        Route::get('/transaksi/{id}/edit', [InventoryController::class, 'editTransaction']);
        Route::put('/transaksi/{id}', [InventoryController::class, 'updateTransaction']);
        Route::post('/transaksi/{id}/approve', [InventoryController::class, 'approveTransaction'])->name('inventory.transaksi.approve');
        
        Route::get('/export', [InventoryController::class, 'exportPage']);
        Route::post('/export/download', [InventoryController::class, 'downloadExcel']);
        Route::post('/import', [InventoryController::class, 'importLogistik']);
        Route::get('/{id}/label', [InventoryController::class, 'printLabel']);
    });
});