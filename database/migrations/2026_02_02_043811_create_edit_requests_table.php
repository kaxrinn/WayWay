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
        Schema::create('edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destinasi_id')->constrained('destinasi')->onDelete('cascade');
            $table->enum('request_type', ['edit_foto', 'edit_info', 'delete_foto', 'add_foto'])
                ->comment('Jenis request edit');
            $table->text('request_data')->nullable()
                ->comment('Data yang mau diubah (JSON)');
            $table->text('keterangan')->nullable()
                ->comment('Alasan/keterangan dari pemilik');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable()
                ->comment('Catatan dari admin');
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('Admin yang approve/reject');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_requests');
    }
};