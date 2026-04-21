<?php

namespace App\Events;

use App\Models\ChatThread;
use App\Support\RealtimePayloads;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadAiStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public ChatThread $thread) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("thread.{$this->thread->id}")];
    }

    public function broadcastAs(): string
    {
        return 'ai.status.updated';
    }

    public function broadcastWith(): array
    {
        return ['thread' => RealtimePayloads::threadStatus($this->thread)];
    }
}
