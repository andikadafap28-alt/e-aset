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
        Schema::create('bot_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->nullable(); // Nomor WA user
            $table->enum('sender', ['user', 'bot']); // Siapa yang mengirim pesan
            $table->text('message'); // Isi pesan
            $table->string('intent')->nullable(); // Tujuan pesan (misal: cek_stok, kirim_pdf)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_conversations');
    }
};
