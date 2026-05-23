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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique(); // Contoh: ALKES-001
            $table->string('name');
            $table->string('category');
            $table->string('location'); // Lokasi puskesmas induk / desa
            $table->year('year_purchased');
            $table->date('last_calibration')->nullable();
            $table->string('condition'); // Baik, Rusak Ringan, Rusak Berat
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('document_link')->nullable(); // Untuk link GDrive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
