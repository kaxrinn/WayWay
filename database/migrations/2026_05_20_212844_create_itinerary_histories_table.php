<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: itinerary_histories
 * Menyimpan setiap itinerary yang berhasil di-generate oleh user.
 * Data disimpan sebagai JSON blob agar fleksibel tanpa banyak kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_histories', function (Blueprint $table) {
            $table->id();

            // Siapa yang generate — nullable supaya bisa anonymous jika diperlukan nanti
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Parameter input yang dipakai user saat generate
            $table->json('params');          // kategori_ids, budget, companion, tanggal, dll

            // Hasil lengkap dari ItineraryService (route, schedule, stats)
            $table->json('result');

            // Summary untuk ditampilkan di tabel riwayat tanpa parse JSON besar
            $table->string('tanggal_kunjungan');        // "2026-06-01"
            $table->string('companion');                 // "keluarga", "solo", dll
            $table->string('origin_label')->nullable();  // "Nagoya Hill Mall"
            $table->integer('stop_count')->default(0);   // jumlah destinasi
            $table->decimal('total_distance', 8, 2)->default(0); // km
            $table->integer('total_minutes')->default(0);
            $table->decimal('budget', 12, 2)->default(0);

            $table->timestamps();

            // Index untuk query riwayat per user
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_histories');
    }
};
