<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_agent_id')->constrained('users')->onDelete('cascade');
            
            // Informasi Dasar
            $table->string('nama_paket');
            $table->text('deskripsi')->nullable();
            $table->string('thumbnail')->nullable(); // path banner
            $table->decimal('harga_per_orang', 12, 2);
            $table->integer('durasi_hari');
            $table->date('tanggal_keberangkatan');
            
            // Destinasi & Fasilitas
            $table->json('destinasi'); // array of destinasi
            $table->json('fasilitas_include')->nullable(); // array include
            $table->json('fasilitas_exclude')->nullable(); // array exclude
            $table->json('itinerary')->nullable(); // array per hari
            
            // Informasi Peserta
            $table->integer('min_peserta')->default(1);
            $table->integer('max_peserta')->default(20);
            $table->string('meeting_point');
            
            // Kontak Travel Agent
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_packages');
    }
};