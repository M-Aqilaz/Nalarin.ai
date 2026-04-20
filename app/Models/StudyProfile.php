<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'education_level',
        'primary_subject',
        'goal',
        'study_style',
        'bio',
        'availability',
        'is_matchmaking_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_matchmaking_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
