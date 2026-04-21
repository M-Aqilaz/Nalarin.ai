<?php

namespace App\Notifications;

use App\Models\StudyMatchMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudyMatchMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly StudyMatchMessage $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'match_message',
            'title' => 'Pesan baru dari partner belajar',
            'body' => "{$this->message->user?->name} mengirim pesan baru di sesi study match.",
            'content' => str($this->message->content)->limit(180)->toString(),
            'url' => route('matches.show', $this->message->study_match_id),
            'message_id' => $this->message->id,
            'match_id' => $this->message->study_match_id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user?->name,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
