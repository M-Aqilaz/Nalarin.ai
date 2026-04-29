<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'raw_text',
        'status',
        'ocr_status',
        'ocr_engine',
        'ocr_warning',
        'ocr_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'ocr_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(AiSummary::class)->latest();
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class)->latest();
    }

    public function flashcardDeck(): HasOne
    {
        return $this->hasOne(FlashcardDeck::class);
    }

    public function quizSet(): HasOne
    {
        return $this->hasOne(QuizSet::class);
    }
}
