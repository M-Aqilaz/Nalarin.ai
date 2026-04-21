<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'plan',
    'room_limit',
    'match_credits',
    'is_active',
    'provider',
    'provider_id',
    'provider_avatar',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(AiSummary::class);
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class);
    }

    public function studyProfile(): HasOne
    {
        return $this->hasOne(StudyProfile::class);
    }

    public function ownedRooms(): HasMany
    {
        return $this->hasMany(StudyRoom::class, 'owner_id');
    }

    public function roomMemberships(): HasMany
    {
        return $this->hasMany(StudyRoomMember::class);
    }

    public function roomMessages(): HasMany
    {
        return $this->hasMany(StudyRoomMessage::class);
    }

    public function matchQueueEntries(): HasMany
    {
        return $this->hasMany(MatchQueueEntry::class);
    }

    public function studyMatchesAsOne(): HasMany
    {
        return $this->hasMany(StudyMatch::class, 'user_one_id');
    }

    public function studyMatchesAsTwo(): HasMany
    {
        return $this->hasMany(StudyMatch::class, 'user_two_id');
    }

    public function blockedUsers(): HasMany
    {
        return $this->hasMany(UserBlock::class);
    }

    public function blockedByUsers(): HasMany
    {
        return $this->hasMany(UserBlock::class, 'blocked_user_id');
    }

    public function reportsFiled(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    public function reportsReceived(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
    }

    public function isPremium(): bool
    {
        return $this->plan === 'premium';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
