<?php

namespace App\Notifications;

use App\Models\StudyRoomMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RoomMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly StudyRoomMessage $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'room_message',
            'title' => 'Pesan baru di group chat kelas',
            'body' => "{$this->message->user?->name} mengirim pesan di {$this->message->room?->name}.",
            'content' => str($this->message->content)->limit(180)->toString(),
            'url' => route('rooms.show', $this->message->study_room_id),
            'message_id' => $this->message->id,
            'room_id' => $this->message->study_room_id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user?->name,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
