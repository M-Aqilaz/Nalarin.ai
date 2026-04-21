<?php

namespace App\Events;

use App\Models\StudyMatchMessage;
use App\Support\RealtimePayloads;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudyMatchMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public StudyMatchMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("match.{$this->message->study_match_id}")];
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return ['message' => RealtimePayloads::matchMessage($this->message)];
    }
}
