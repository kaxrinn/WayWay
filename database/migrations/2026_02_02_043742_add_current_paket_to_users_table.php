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
        Schema::table('users', function (Blueprint $table) {
            // ID paket yang sedang aktif untuk pemilik wisata
            $table->foreignId('current_paket_id')->nullable()->after('role')
                ->constrained('paket_promosi')->onDelete('set null')
                ->comment('Paket yang sedang aktif (Basic/Standard/Premium)');
            
            // Tanggal expired paket
            $table->date('paket_expired_at')->nullable()->after('current_paket_id')
                ->comment('Tanggal paket berakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_paket_id']);
            $table->dropColumn(['current_paket_id', 'paket_expired_at']);
        });
    }
};