<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('topic');
            $table->text('description')->nullable();
            $table->string('visibility')->default('public');
            $table->unsignedInteger('max_members')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('study_room_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->string('status')->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['study_room_id', 'user_id']);
        });

        Schema::create('study_room_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reply_to_message_id')->nullable()->constrained('study_room_messages')->nullOnDelete();
            $table->text('content');
            $table->string('type')->default('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_room_messages');
        Schema::dropIfExists('study_room_members');
        Schema::dropIfExists('study_rooms');
    }
};
