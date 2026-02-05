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
        Schema::table('destinasi', function (Blueprint $table) {
            // Kolom is_featured untuk Premium Package
            $table->boolean('is_featured')->default(false)->after('status');
            
            // Kolom video dalam bentuk JSON array
            $table->json('video')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinasi', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'video']);
        });
    }
};