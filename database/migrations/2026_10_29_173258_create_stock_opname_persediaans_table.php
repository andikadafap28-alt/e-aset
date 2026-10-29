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
        Schema::create('stock_opname_persediaans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->default('000.2.1.2/    /413.102.5.18/2026');
            $table->date('tanggal_opname');
            $table->decimal('atk', 15, 2)->default(0);
            $table->decimal('bahan_cetak', 15, 2)->default(0);
            $table->decimal('benda_pos', 15, 2)->default(0);
            $table->decimal('bahan_komputer', 15, 2)->default(0);
            $table->decimal('obat_blud', 15, 2)->default(0);
            $table->decimal('bmhp_blud', 15, 2)->default(0);
            $table->decimal('obat_apbd', 15, 2)->default(0);
            $table->decimal('bmhp_apbd', 15, 2)->default(0);
            $table->decimal('obat_apbn', 15, 2)->default(0);
            $table->decimal('bmhp_apbn', 15, 2)->default(0);
            $table->string('nama_pemeriksa')->default('dr. Muhamad Sunaryadi');
            $table->string('nip_pemeriksa')->default('19690313 200212 1 007');
            $table->string('jabatan_pemeriksa')->default('Kepala Puskesmas Mantup');
            $table->string('nama_diperiksa')->default('Andika Dafa Penta Pratama');
            $table->string('nip_diperiksa')->default('19990728 202504 1 003');
            $table->string('jabatan_diperiksa')->default('Pengurus Barang Pembantu');
            $table->text('keterangan')->nullable();
            $table->json('custom_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_persediaans');
    }
};
