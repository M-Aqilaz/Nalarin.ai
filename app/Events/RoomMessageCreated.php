<?php

namespace App\Events;

use App\Models\StudyRoomMessage;
use App\Support\RealtimePayloads;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public StudyRoomMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("room.{$this->message->study_room_id}")];
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return ['message' => RealtimePayloads::roomMessage($this->message)];
    }
}
