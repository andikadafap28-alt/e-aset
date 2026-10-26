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
            if (!Schema::hasColumn('assets', 'bahan')) {
                $table->string('bahan')->nullable()->after('merk');
            }
            if (!Schema::hasColumn('assets', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('bahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'bahan')) {
                $table->dropColumn('bahan');
            }
            if (Schema::hasColumn('assets', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
