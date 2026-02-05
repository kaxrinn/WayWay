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
        Schema::table('paket_promosi', function (Blueprint $table) {
            // Batasan per paket
            $table->integer('max_destinasi')->nullable()->after('durasi_hari')
                ->comment('NULL = unlimited, angka = max destinasi yang bisa dibuat');
            
            $table->integer('max_foto')->nullable()->after('max_destinasi')
                ->comment('NULL = unlimited, angka = max foto per destinasi');
            
            $table->integer('max_video')->nullable()->after('max_foto')
                ->comment('NULL = unlimited, angka = max video per destinasi');
            
            $table->integer('priority_level')->default(1)->after('max_video')
                ->comment('1=Basic, 2=Standard, 3=Premium - untuk sorting');
            
            $table->boolean('can_edit_foto')->default(false)->after('priority_level')
                ->comment('Apakah bisa edit/hapus foto sendiri');
            
            $table->boolean('is_featured_allowed')->default(false)->after('can_edit_foto')
                ->comment('Apakah destinasi bisa jadi featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_promosi', function (Blueprint $table) {
            $table->dropColumn([
                'max_destinasi',
                'max_foto',
                'max_video',
                'priority_level',
                'can_edit_foto',
                'is_featured_allowed'
            ]);
        });
    }
};