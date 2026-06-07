<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_agent_subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket'); // Basic, Silver, Gold
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->default(0); // 0 untuk free
            $table->integer('max_packages'); // max paket wisata
            $table->integer('durasi_bulan')->default(1); // durasi paket
            $table->json('fitur')->nullable(); // featured, analytics, priority
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_agent_subscription_packages');
    }
};