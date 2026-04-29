<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('ocr_status')->default('not_required')->after('status');
            $table->string('ocr_engine')->nullable()->after('ocr_status');
            $table->text('ocr_warning')->nullable()->after('ocr_engine');
            $table->timestamp('ocr_completed_at')->nullable()->after('ocr_warning');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'ocr_status',
                'ocr_engine',
                'ocr_warning',
                'ocr_completed_at',
            ]);
        });
    }
};
