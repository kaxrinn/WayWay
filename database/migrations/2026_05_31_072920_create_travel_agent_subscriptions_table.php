<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_agent_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('travel_agent_subscription_packages')->onDelete('cascade');
            $table->string('snap_token')->nullable();
            $table->enum('payment_method', ['midtrans', 'free'])->default('free');
            $table->enum('status', ['pending', 'active', 'expired'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->unique(['travel_agent_id', 'package_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_agent_subscriptions');
    }
};