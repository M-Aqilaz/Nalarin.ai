<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_deck_id')->constrained()->cascadeOnDelete();
            $table->string('front');
            $table->text('back');
            $table->text('example')->nullable();
            $table->string('difficulty', 20)->default('Menengah');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('streak')->default(0);
            $table->unsignedInteger('interval_minutes')->default(0);
            $table->decimal('ease_factor', 4, 2)->default(2.30);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
