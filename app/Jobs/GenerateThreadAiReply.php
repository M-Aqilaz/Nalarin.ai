<?php

namespace App\Jobs;

use App\Contracts\AiThreadResponder;
use App\Events\ThreadAiStatusUpdated;
use App\Events\ThreadMessageCreated;
use App\Models\ChatThread;
use App\Notifications\ThreadAiReplyNotification;
use App\Services\Analytics\AiRequestLogger;
use App\Support\SafeBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateThreadAiReply implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $threadId) {}

    public function handle(AiThreadResponder $responder, AiRequestLogger $aiRequests): void
    {
        $thread = ChatThread::query()
            ->with(['material', 'summary', 'messages' => fn ($query) => $query->with('attachments')->orderBy('id')])
            ->find($this->threadId);

        if (! $thread) {
            return;
        }

        $thread->forceFill([
            'ai_status' => 'processing',
            'ai_error' => null,
        ])->save();

        SafeBroadcast::event(new ThreadAiStatusUpdated($thread->fresh()));

        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://openrouter.ai/api/v1'), '/');
        $latestUserMessage = $thread->messages->where('role', 'user')->last();
        $aiRequest = $aiRequests->start([
            'user_id' => $thread->user_id,
            'material_id' => $thread->material_id,
            'chat_thread_id' => $thread->id,
            'chat_message_id' => $latestUserMessage?->id,
            'feature' => 'chat',
            'provider' => $aiRequests->providerFromBaseUrl($baseUrl),
            'model' => (string) config('services.openai.model', 'openai/gpt-oss-120b:free'),
        ]);

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

            $aiRequests->success($aiRequest, [
                'input_tokens' => $reply->inputTokens,
                'output_tokens' => $reply->outputTokens,
                'response_id' => $reply->responseId,
            ]);

            $assistantMessage->refresh();
            SafeBroadcast::event(new ThreadMessageCreated($assistantMessage));
            SafeBroadcast::event(new ThreadAiStatusUpdated($thread->fresh()));
            $thread->user?->notify(new ThreadAiReplyNotification($thread->fresh(), $assistantMessage));
        } catch (Throwable $throwable) {
            $aiRequests->failed($aiRequest, $throwable);

            $thread->forceFill([
                'ai_status' => 'failed',
                'ai_error' => str($throwable->getMessage())->limit(1000)->toString(),
            ])->save();

            SafeBroadcast::event(new ThreadAiStatusUpdated($thread->fresh()));

            if (config('queue.default') === 'sync') {
                return;
            }

            throw $throwable;
        }
    }
}
