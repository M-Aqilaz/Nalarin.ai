<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('plan', 'premium')
            ->where('plan_key', 'free')
            ->update([
                'plan_key' => 'pro_monthly',
                'match_credits_monthly_allowance' => 99,
            ]);

        DB::table('users')
            ->where('plan', 'free')
            ->update([
                'plan_key' => 'free',
                'match_credits_monthly_allowance' => 3,
                'match_credits_reset_at' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('users')->update([
            'plan_key' => 'free',
            'match_credits_monthly_allowance' => 3,
            'match_credits_reset_at' => null,
        ]);
    }
};
