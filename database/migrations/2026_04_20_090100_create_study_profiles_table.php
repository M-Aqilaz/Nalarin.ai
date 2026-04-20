<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('education_level')->nullable();
            $table->string('primary_subject')->nullable();
            $table->string('goal')->nullable();
            $table->string('study_style')->nullable();
            $table->text('bio')->nullable();
            $table->string('availability')->nullable();
            $table->boolean('is_matchmaking_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_profiles');
    }
};
