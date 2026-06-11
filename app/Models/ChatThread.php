<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ChatThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_id',
        'title',
        'ai_status',
        'ai_error',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function summary(): HasOneThrough
    {
        return $this->hasOneThrough(
            AiSummary::class,
            Material::class,
            'id',
            'material_id',
            'material_id',
            'id',
        )->latest('ai_summaries.created_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'thread_id')->orderBy('id');
    }
}
