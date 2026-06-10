<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRequest extends Model
{
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'material_id',
        'chat_thread_id',
        'chat_message_id',
        'quiz_set_id',
        'flashcard_deck_id',
        'feature',
        'provider',
        'model',
        'status',
        'started_at',
        'completed_at',
        'latency_ms',
        'input_tokens',
        'output_tokens',
        'response_id',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function chatThread(): BelongsTo
    {
        return $this->belongsTo(ChatThread::class);
    }

    public function chatMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class);
    }

    public function quizSet(): BelongsTo
    {
        return $this->belongsTo(QuizSet::class);
    }

    public function flashcardDeck(): BelongsTo
    {
        return $this->belongsTo(FlashcardDeck::class);
    }
}
