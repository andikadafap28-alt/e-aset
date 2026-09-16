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
        Schema::create('dpps', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->string('kode_rup');
            $table->string('nama_paket')->nullable();
            $table->text('spesifikasi_teknis')->nullable();
            $table->string('jumlah')->nullable();
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('pagu_anggaran', 15, 2)->nullable();
            $table->date('tanggal_dpp');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpps');
    }
};
