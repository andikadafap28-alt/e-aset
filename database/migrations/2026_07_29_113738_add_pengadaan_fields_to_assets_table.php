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
            $table->string('merk')->nullable()->after('name');
            $table->string('penyedia')->nullable()->after('merk');
            $table->date('tanggal_bast')->nullable()->after('penyedia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['merk', 'penyedia', 'tanggal_bast']);
        });
    }
};
