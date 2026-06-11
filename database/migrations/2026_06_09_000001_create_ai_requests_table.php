<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('chat_thread_id')->nullable()->constrained('chat_threads')->nullOnDelete();
            $table->foreignId('chat_message_id')->nullable()->constrained('chat_messages')->nullOnDelete();
            $table->foreignId('quiz_set_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flashcard_deck_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature')->index();
            $table->string('provider')->nullable()->index();
            $table->string('model')->nullable();
            $table->string('status')->default('processing')->index();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->string('response_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_requests');
    }
};
