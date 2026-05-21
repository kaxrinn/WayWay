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
Schema::create('saved_itineraries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->string('location')->nullable();
    $table->date('start_date');
    $table->date('end_date');
    $table->integer('total_days')->default(1);
    $table->string('companion')->nullable();
    $table->string('budget')->nullable();
    $table->json('categories')->nullable();
    $table->longText('itinerary_data');
    $table->string('total_cost')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_itineraries');
    }
};
