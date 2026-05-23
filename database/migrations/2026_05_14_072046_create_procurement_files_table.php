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
        Schema::create('procurement_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('kategori')->nullable();
            $table->string('jenis_dokumen')->nullable();
            $table->string('file_name')->nullable();
            $table->string('drive_file_id')->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->string('path_gdrive')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_files');
    }
};
