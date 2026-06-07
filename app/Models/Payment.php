<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'gateway',
    'order_id',
    'plan_key',
    'plan_name',
    'plan',
    'amount',
    'currency',
    'duration_days',
    'room_limit',
    'match_credits',
    'status',
    'payment_method',
    'paid_at',
    'plan_starts_at',
    'plan_ends_at',
    'gateway_payload',
])]
class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    public function getRouteKeyName(): string
    {
        return 'order_id';
    }

    public static function makeOrderId(): string
    {
        return 'NLR'.now()->format('YmdHis').strtoupper(Str::random(6));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'duration_days' => 'integer',
            'room_limit' => 'integer',
            'match_credits' => 'integer',
            'paid_at' => 'datetime',
            'plan_starts_at' => 'datetime',
            'plan_ends_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }
}
