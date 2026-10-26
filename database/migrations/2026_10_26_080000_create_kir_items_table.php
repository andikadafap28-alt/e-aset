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
        if (!Schema::hasTable('kir_items')) {
            Schema::create('kir_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->string('name');
                $table->string('merk')->nullable();
                $table->string('bahan')->default('Campuran')->nullable();
                $table->integer('jumlah')->default(1);
                $table->integer('kondisi_baik')->default(1);
                $table->integer('kondisi_kurang_baik')->default(0);
                $table->integer('kondisi_rusak_berat')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // Bersihkan data KIR dari tabel assets utama agar monitoring aset tetap bersih
        \App\Models\Asset::where('source', 'KIR')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kir_items');
    }
};
