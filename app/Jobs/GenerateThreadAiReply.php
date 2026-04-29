<?php

namespace App\Jobs;

use App\Contracts\AiThreadResponder;
use App\Events\ThreadAiStatusUpdated;
use App\Events\ThreadMessageCreated;
use App\Models\ChatThread;
use App\Notifications\ThreadAiReplyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateThreadAiReply implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $threadId) {}

    public function handle(AiThreadResponder $responder): void
    {
        $thread = ChatThread::query()
            ->with(['material', 'messages' => fn ($query) => $query->orderBy('id')])
            ->find($this->threadId);

        if (! $thread) {
            return;
        }

        $thread->forceFill([
            'ai_status' => 'processing',
            'ai_error' => null,
        ])->save();

        broadcast(new ThreadAiStatusUpdated($thread->fresh()));

        try {
            $reply = $responder->generateReply($thread);

            $assistantMessage = $thread->messages()->create([
                'role' => 'assistant',
                'content' => $reply->content,
                'token_count' => $reply->outputTokens,
            ]);

            $thread->forceFill([
                'ai_status' => 'idle',
                'ai_error' => null,
            ])->save();

            $assistantMessage->refresh();
            broadcast(new ThreadMessageCreated($assistantMessage));
            broadcast(new ThreadAiStatusUpdated($thread->fresh()));
            $thread->user?->notify(new ThreadAiReplyNotification($thread->fresh(), $assistantMessage));
        } catch (Throwable $throwable) {
            $thread->forceFill([
                'ai_status' => 'failed',
                'ai_error' => str($throwable->getMessage())->limit(1000)->toString(),
            ])->save();

            broadcast(new ThreadAiStatusUpdated($thread->fresh()));

            if (config('queue.default') === 'sync') {
                return;
            }

            throw $throwable;
        }
    }
}
