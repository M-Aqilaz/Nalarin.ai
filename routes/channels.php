<?php

use App\Models\ChatThread;
use App\Models\StudyMatch;
use App\Models\StudyRoom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('thread.{threadId}', function ($user, int $threadId) {
    return ChatThread::query()
        ->whereKey($threadId)
        ->where('user_id', $user->id)
        ->exists();
});

Broadcast::channel('room.{roomId}', function ($user, int $roomId) {
    $room = StudyRoom::query()->find($roomId);

    if (! $room) {
        return false;
    }

    if ($room->visibility === 'public') {
        return true;
    }

    return $room->members()
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->exists();
});

Broadcast::channel('match.{matchId}', function ($user, int $matchId) {
    return StudyMatch::query()
        ->whereKey($matchId)
        ->where(fn ($query) => $query
            ->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id))
        ->exists();
});
