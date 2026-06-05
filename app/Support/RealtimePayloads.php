<?php

namespace App\Support;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\StudyMatchMessage;
use App\Models\StudyRoomMessage;

class RealtimePayloads
{
    public static function threadMessage(ChatMessage $message): array
    {
        $message->loadMissing('attachments');

        return [
            'id' => $message->id,
            'thread_id' => $message->thread_id,
            'role' => $message->role,
            'content' => $message->content,
            'token_count' => $message->token_count,
            'attachments' => $message->attachments
                ->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'kind' => $attachment->kind,
                    'url' => $attachment->url(),
                    'original_name' => $attachment->original_name,
                    'mime_type' => $attachment->mime_type,
                    'size' => $attachment->size,
                ])
                ->values()
                ->all(),
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }

    public static function roomMessage(StudyRoomMessage $message): array
    {
        return [
            'id' => $message->id,
            'study_room_id' => $message->study_room_id,
            'user_id' => $message->user_id,
            'user_name' => $message->user?->name,
            'content' => $message->content,
            'type' => $message->type,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }

    public static function matchMessage(StudyMatchMessage $message): array
    {
        return [
            'id' => $message->id,
            'study_match_id' => $message->study_match_id,
            'user_id' => $message->user_id,
            'user_name' => $message->user?->name,
            'content' => $message->content,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }

    public static function threadStatus(ChatThread $thread): array
    {
        return [
            'id' => $thread->id,
            'title' => $thread->title,
            'ai_status' => $thread->ai_status,
            'ai_error' => $thread->ai_error,
        ];
    }
}
