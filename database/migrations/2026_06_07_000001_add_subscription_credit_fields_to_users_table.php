<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan_key')->default('free')->after('plan_expires_at')->index();
            $table->unsignedInteger('match_credits_monthly_allowance')->default(3)->after('match_credits');
            $table->timestamp('match_credits_reset_at')->nullable()->after('match_credits_monthly_allowance')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'plan_key',
                'match_credits_monthly_allowance',
                'match_credits_reset_at',
            ]);
        });
    }
};
