<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promosi', function (Blueprint $table) {
            $table->string('judul_banner')->nullable()->after('destinasi_id');
            $table->text('deskripsi_banner')->nullable()->after('judul_banner');
            $table->string('banner_promosi')->nullable()->after('deskripsi_banner'); // path gambar
        });
    }

    public function down(): void
    {
        Schema::table('promosi', function (Blueprint $table) {
            $table->dropColumn(['judul_banner', 'deskripsi_banner', 'banner_promosi']);
        });
    }
};