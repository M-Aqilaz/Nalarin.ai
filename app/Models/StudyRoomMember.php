<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyRoomMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(StudyRoom::class, 'study_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
