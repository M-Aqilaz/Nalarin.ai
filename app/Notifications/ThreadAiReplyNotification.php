<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ThreadAiReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ChatThread $thread,
        private readonly ChatMessage $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'ai_reply',
            'title' => 'AI Tutor membalas thread kamu',
            'body' => "Balasan baru tersedia di thread {$this->thread->title}.",
            'content' => str($this->message->content)->limit(180)->toString(),
            'url' => route('chat.show', $this->thread),
            'message_id' => $this->message->id,
            'thread_id' => $this->thread->id,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
