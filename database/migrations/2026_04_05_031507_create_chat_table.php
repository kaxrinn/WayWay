<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sesi chat (per user atau per session)
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('session_token', 64)->unique(); // untuk guest user
            $table->enum('stage', [
                'greeting',       // baru buka chatbot
                'eliciting',      // sedang tanya preferensi
                'recommending',   // sudah kasih rekomendasi
                'itinerary',      // mode itinerary
                'general',        // tanya umum tentang destinasi
            ])->default('greeting');
            $table->json('preferences')->nullable(); // simpan preferensi user
            $table->timestamps();
        });

        // Tabel pesan chat
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->json('context_destinasi')->nullable(); // destinasi yang dijadikan konteks RAG
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};