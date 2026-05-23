<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('inventory_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        $table->string('jenis_transaksi', 50)->default('masuk');
        $table->integer('jumlah');
        $table->decimal('harga_satuan', 15, 2)->nullable(); // Kolom harga satuan mendukung maksimal 2 digit di belakang koma
        $table->text('keterangan')->nullable();
        $table->date('tanggal_transaksi');
        $table->boolean('status_hutang')->default(false); // Untuk mencatat pengadaan hutang
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
