<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_set_id',
        'prompt',
        'choices',
        'correct_choice',
        'explanation',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'choices' => 'array',
        ];
    }

    public function quizSet(): BelongsTo
    {
        return $this->belongsTo(QuizSet::class);
    }
}
