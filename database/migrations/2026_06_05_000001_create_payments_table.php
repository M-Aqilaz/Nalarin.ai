<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->default('pakasir');
            $table->string('order_id')->unique();
            $table->string('plan_key');
            $table->string('plan_name');
            $table->string('plan')->default('premium');
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('IDR');
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedInteger('room_limit');
            $table->unsignedInteger('match_credits');
            $table->string('status')->default('pending')->index();
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('plan_starts_at')->nullable();
            $table->timestamp('plan_ends_at')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
