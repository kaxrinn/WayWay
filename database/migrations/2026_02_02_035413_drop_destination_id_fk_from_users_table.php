<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // 1️⃣ HAPUS FOREIGN KEY dulu
            $table->dropForeign(['destinasi_id']);

            // 2️⃣ BARU HAPUS KOLOM
            $table->dropColumn('destinasi_id');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // balikin lagi kalau rollback
            $table->foreignId('destinasi_id')
                  ->nullable()
                  ->constrained('destinations')
                  ->onDelete('set null');

        });
    }
};
