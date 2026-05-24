<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hanya hapus kolom embedding — tidak perlu bikin itinerary_histories
        // karena sudah dibuat oleh migration temenmu sebelumnya
        Schema::table('destinasi', function (Blueprint $table) {
            if (Schema::hasColumn('destinasi', 'embedding')) {
                $table->dropColumn('embedding');
            }
            if (Schema::hasColumn('destinasi', 'embedding_text')) {
                $table->dropColumn('embedding_text');
            }
            if (Schema::hasColumn('destinasi', 'embedded_at')) {
                $table->dropColumn('embedded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinasi', function (Blueprint $table) {
            $table->text('embedding_text')->nullable()->after('deskripsi');
            $table->json('embedding')->nullable()->after('embedding_text');
            $table->timestamp('embedded_at')->nullable()->after('embedding');
        });
    }
};