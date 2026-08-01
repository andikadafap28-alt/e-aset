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
        Schema::create('asset_handover_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_handover_id')->constrained('asset_handovers')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->timestamps();
        });

        // Pindahkan data lama dari asset_handovers ke asset_handover_items
        \Illuminate\Support\Facades\DB::statement('INSERT INTO asset_handover_items (asset_handover_id, asset_id, created_at, updated_at) SELECT id, asset_id, created_at, updated_at FROM asset_handovers WHERE asset_id IS NOT NULL');

        // Opsional: Buat asset_id di asset_handovers menjadi nullable karena sudah tidak dipakai sebagai relasi tunggal
        Schema::table('asset_handovers', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_handovers', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->nullable(false)->change();
        });
        
        Schema::dropIfExists('asset_handover_items');
    }
};
