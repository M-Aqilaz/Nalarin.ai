<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_queue_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('selected_topic');
            $table->string('preferred_level')->nullable();
            $table->string('preferred_session_type')->nullable();
            $table->string('status')->default('waiting');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('study_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->string('status')->default('active');
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();
        });

        Schema::create('study_match_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'blocked_user_id']);
        });

        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reportable_type')->nullable();
            $table->unsignedBigInteger('reportable_id')->nullable();
            $table->text('reason');
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('study_match_messages');
        Schema::dropIfExists('study_matches');
        Schema::dropIfExists('match_queue_entries');
    }
};
