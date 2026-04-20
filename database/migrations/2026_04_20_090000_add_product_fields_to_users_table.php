<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('role');
            $table->unsignedInteger('room_limit')->default(2)->after('plan');
            $table->unsignedInteger('match_credits')->default(3)->after('room_limit');
            $table->boolean('is_active')->default(true)->after('match_credits');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan', 'room_limit', 'match_credits', 'is_active']);
        });
    }
};
