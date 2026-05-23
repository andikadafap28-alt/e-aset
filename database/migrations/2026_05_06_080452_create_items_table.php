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
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('kode_barang')->nullable()->unique(); // Bisa untuk kode alkes nanti
        $table->string('nama_barang');
        $table->string('kategori'); // Medis, Non Medis, dll
        $table->string('satuan'); // Pcs, Box, dll
        $table->integer('stok_sekarang')->default(0);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
