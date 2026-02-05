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
        Schema::table('promosi', function (Blueprint $table) {
            // Make destinasi_id nullable
            $table->unsignedBigInteger('destinasi_id')->nullable()->change();
            
            // Add user_id for user subscriptions
            if (!Schema::hasColumn('promosi', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promosi', function (Blueprint $table) {
            $table->unsignedBigInteger('destinasi_id')->nullable(false)->change();
            
            if (Schema::hasColumn('promosi', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};