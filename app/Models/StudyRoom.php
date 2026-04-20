<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'topic',
        'description',
        'visibility',
        'max_members',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(StudyRoomMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StudyRoomMessage::class)->latest();
    }
}
