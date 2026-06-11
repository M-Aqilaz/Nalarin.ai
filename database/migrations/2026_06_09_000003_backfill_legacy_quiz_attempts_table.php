<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quiz_attempts')) {
            return;
        }

        if (Schema::hasColumn('quiz_attempts', 'status')) {
            DB::table('quiz_attempts')
                ->where(fn ($query) => $query->whereNull('status')->orWhere('status', ''))
                ->update(['status' => 'completed']);
        }

        if (Schema::hasColumn('quiz_attempts', 'started_at') && Schema::hasColumn('quiz_attempts', 'created_at')) {
            DB::table('quiz_attempts')
                ->whereNull('started_at')
                ->whereNotNull('created_at')
                ->update(['started_at' => DB::raw('created_at')]);

            DB::table('quiz_attempts')
                ->whereNull('started_at')
                ->update(['started_at' => now()]);
        }

        if (Schema::hasColumn('quiz_attempts', 'started_at') && ! Schema::hasColumn('quiz_attempts', 'created_at')) {
            DB::table('quiz_attempts')
                ->whereNull('started_at')
                ->update(['started_at' => now()]);
        }

        if (Schema::hasColumn('quiz_attempts', 'completed_at') && Schema::hasColumn('quiz_attempts', 'updated_at')) {
            DB::table('quiz_attempts')
                ->whereNull('completed_at')
                ->whereNotNull('updated_at')
                ->update(['completed_at' => DB::raw('updated_at')]);

            if (Schema::hasColumn('quiz_attempts', 'created_at')) {
                DB::table('quiz_attempts')
                    ->whereNull('completed_at')
                    ->whereNotNull('created_at')
                    ->update(['completed_at' => DB::raw('created_at')]);
            }

            DB::table('quiz_attempts')
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);
        }

        if (Schema::hasColumn('quiz_attempts', 'completed_at') && ! Schema::hasColumn('quiz_attempts', 'updated_at')) {
            if (Schema::hasColumn('quiz_attempts', 'created_at')) {
                DB::table('quiz_attempts')
                    ->whereNull('completed_at')
                    ->whereNotNull('created_at')
                    ->update(['completed_at' => DB::raw('created_at')]);
            }

            DB::table('quiz_attempts')
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);
        }

        if (
            Schema::hasColumn('quiz_attempts', 'percentage')
            && Schema::hasColumn('quiz_attempts', 'score')
            && Schema::hasColumn('quiz_attempts', 'total_questions')
        ) {
            DB::table('quiz_attempts')
                ->where('total_questions', '>', 0)
                ->update(['percentage' => DB::raw('ROUND((score / total_questions) * 100, 2)')]);
        }
    }

    public function down(): void
    {
        //
    }
};
