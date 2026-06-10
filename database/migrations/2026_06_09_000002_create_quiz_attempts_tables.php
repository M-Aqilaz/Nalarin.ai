<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('quiz_set_id')->constrained()->cascadeOnDelete();
                $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
                $table->string('status')->default('in_progress')->index();
                $table->unsignedSmallInteger('score')->default(0);
                $table->unsignedSmallInteger('total_questions')->default(0);
                $table->decimal('percentage', 5, 2)->default(0);
                $table->timestamp('started_at')->index();
                $table->timestamp('completed_at')->nullable()->index();
                $table->timestamps();
            });
        } else {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                if (! Schema::hasColumn('quiz_attempts', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                }

                if (! Schema::hasColumn('quiz_attempts', 'quiz_set_id')) {
                    $table->foreignId('quiz_set_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
                }

                if (! Schema::hasColumn('quiz_attempts', 'material_id')) {
                    $table->foreignId('material_id')->nullable()->after('quiz_set_id')->constrained()->nullOnDelete();
                }

                if (! Schema::hasColumn('quiz_attempts', 'status')) {
                    $table->string('status')->default('completed')->after('material_id')->index();
                }

                if (! Schema::hasColumn('quiz_attempts', 'score')) {
                    $table->unsignedSmallInteger('score')->default(0)->after('status');
                }

                if (! Schema::hasColumn('quiz_attempts', 'total_questions')) {
                    $table->unsignedSmallInteger('total_questions')->default(0)->after('score');
                }

                if (! Schema::hasColumn('quiz_attempts', 'percentage')) {
                    $table->decimal('percentage', 5, 2)->default(0)->after('total_questions');
                }

                if (! Schema::hasColumn('quiz_attempts', 'started_at')) {
                    $table->timestamp('started_at')->nullable()->after('percentage')->index();
                }

                if (! Schema::hasColumn('quiz_attempts', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('started_at')->index();
                }

                if (! Schema::hasColumn('quiz_attempts', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        if (! Schema::hasTable('quiz_attempt_answers')) {
            Schema::create('quiz_attempt_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
                $table->foreignId('quiz_question_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedTinyInteger('selected_choice');
                $table->unsignedTinyInteger('correct_choice');
                $table->boolean('is_correct')->default(false);
                $table->timestamp('answered_at')->index();
                $table->timestamps();

                $table->unique(['quiz_attempt_id', 'quiz_question_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
    }
};
