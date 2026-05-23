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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->index(['item_id', 'jenis_transaksi', 'tanggal_transaksi']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index('kategori_besar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_tables', function (Blueprint $table) {
            //
        });
    }
};
