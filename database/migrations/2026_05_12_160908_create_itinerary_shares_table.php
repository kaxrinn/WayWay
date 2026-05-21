<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel untuk menyimpan itinerary yang di-share user.
 * Itinerary disimpan sebagai JSON blob, diakses via UUID token.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_shares', function (Blueprint $table) {
            $table->id();
            $table->string('token', 36)->unique()->index(); // UUID
            $table->json('data');                           // Full itinerary JSON
            $table->unsignedBigInteger('user_id')->nullable(); // Bisa anonymous
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_shares');
    }
};
