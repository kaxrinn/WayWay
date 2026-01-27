<?php
// File: database/migrations/2024_01_01_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // nullable untuk Google OAuth
            $table->string('google_id')->nullable()->unique(); // untuk Google OAuth
            $table->string('avatar')->nullable(); // foto profil dari Google
            $table->enum('role', ['admin', 'pemilik_wisata', 'wisatawan'])->default('wisatawan');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};