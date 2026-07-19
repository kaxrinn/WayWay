<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::connection(null)->getConnection()->getDriverName() !== 'sqlite') {
            // Update ENUM column to add 'travel_agent'
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','pemilik_wisata','wisatawan','travel_agent') NOT NULL DEFAULT 'wisatawan'");
        }
    }

    public function down()
    {
        if (Schema::connection(null)->getConnection()->getDriverName() !== 'sqlite') {
            // Revert back to original ENUM
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','pemilik_wisata','wisatawan') NOT NULL DEFAULT 'wisatawan'");
        }
    }
};