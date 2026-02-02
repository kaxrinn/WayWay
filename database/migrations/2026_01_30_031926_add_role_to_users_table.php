<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('no_telepon')->nullable()->after('email');

        $table->foreignId('destinasi_id')
              ->nullable()
              ->constrained('destinasi')
              ->nullOnDelete()
              ->after('role');
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['destinasi_id']);
            $table->dropColumn(['destinasi_id', 'no_telepon']);
        });
    }
};