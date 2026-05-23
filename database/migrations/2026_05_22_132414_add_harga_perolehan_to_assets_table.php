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
            $table->decimal('harga_perolehan', 15, 2)->nullable()->after('category');
            $table->boolean('status_aktif')->default(true)->after('document_link');
            $table->foreignId('category_id')->nullable()->after('id')->constrained('asset_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['harga_perolehan', 'status_aktif', 'category_id']);
        });
    }
};
