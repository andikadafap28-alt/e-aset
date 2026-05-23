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
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->nullable()->after('year_purchased');
            $table->decimal('salvage_value', 15, 2)->nullable()->after('purchase_price')->comment('Nilai Sisa');
            $table->integer('useful_life')->nullable()->after('salvage_value')->comment('Masa Manfaat (Tahun)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'salvage_value', 'useful_life']);
        });
    }
};
