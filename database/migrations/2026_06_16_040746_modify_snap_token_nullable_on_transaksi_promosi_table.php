<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transaksi_promosi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_promosi', 'snap_token')) {
                $table->string('snap_token')->nullable();
            } else {
                $table->string('snap_token')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('transaksi_promosi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_promosi', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
        });
    }
};