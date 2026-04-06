<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinasi', function (Blueprint $table) {
            // Teks gabungan yang akan di-embed (nama + deskripsi + kategori)
            $table->text('embedding_text')->nullable()->after('deskripsi');

            // Hasil embedding dari OpenAI (disimpan sebagai JSON array of floats)
            // text-embedding-3-small menghasilkan 1536 dimensi
            $table->json('embedding')->nullable()->after('embedding_text');

            // Timestamp kapan embedding terakhir di-generate
            $table->timestamp('embedded_at')->nullable()->after('embedding');
        });
    }

    public function down(): void
    {
        Schema::table('destinasi', function (Blueprint $table) {
            $table->dropColumn(['embedding_text', 'embedding', 'embedded_at']);
        });
    }
};