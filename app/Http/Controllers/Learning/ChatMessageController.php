<?php

namespace App\Http\Controllers\Learning;

use App\Events\ThreadAiStatusUpdated;
use App\Events\ThreadMessageCreated;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateThreadAiReply;
use App\Models\ChatThread;
use App\Support\AiUsageLimiter;
use App\Support\RealtimePayloads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatMessageController extends Controller
{
    private const AUTO_THREAD_TITLE = 'Thread Baru';

    public function index(Request $request, ChatThread $chatThread): JsonResponse
    {
        abort_unless($chatThread->user_id === $request->user()->id, 403);

        $afterId = max(0, (int) $request->integer('after'));

        $messages = $chatThread->messages()
            ->where('id', '>', $afterId)
            ->get()
            ->map(fn ($message) => RealtimePayloads::threadMessage($message))
            ->values();

        return response()->json([
            'messages' => $messages,
            'thread' => RealtimePayloads::threadStatus($chatThread->fresh()),
        ]);
    }

    public function store(Request $request, ChatThread $chatThread, AiUsageLimiter $aiUsageLimiter): RedirectResponse|JsonResponse
    {
        abort_unless($chatThread->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'content' => ['nullable', 'required_without:image', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $content = trim((string) ($validated['content'] ?? ''));
        $uploadedImage = $request->file('image');

        if ($content === '' && $uploadedImage) {
            $content = 'Tolong jelaskan gambar ini dengan bahasa Indonesia yang mudah dipahami.';
        }

        $limit = $aiUsageLimiter->check($request->user());

        if (! $limit['allowed']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $limit['message'],
                    'retry_after' => $limit['retry_after'],
                ], 429);
            }

            return back()->withErrors(['content' => $limit['message']]);
        }

        $shouldAutoTitle = $this->shouldAutoTitle($chatThread);

        $message = $chatThread->messages()->create([
            'role' => 'user',
            'content' => $content,
        ]);

        if ($uploadedImage) {
            $path = $uploadedImage->store('chat-attachments/'.$chatThread->id, 'public');

            $message->attachments()->create([
                'kind' => 'image',
                'disk' => 'public',
                'path' => $path,
                'original_name' => $uploadedImage->getClientOriginalName(),
                'mime_type' => $uploadedImage->getMimeType() ?: $uploadedImage->getClientMimeType(),
                'size' => $uploadedImage->getSize(),
            ]);
        }

        $aiUsageLimiter->hit($request->user());

        $nextThreadState = [
            'ai_status' => 'queued',
            'ai_error' => null,
        ];

        if ($shouldAutoTitle) {
            $nextThreadState['title'] = $this->makeTitleFromPrompt($content);
        }

        $chatThread->forceFill($nextThreadState)->save();

        $message->refresh();
        broadcast(new ThreadMessageCreated($message));
        broadcast(new ThreadAiStatusUpdated($chatThread->fresh()));
        GenerateThreadAiReply::dispatch($chatThread->id);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => RealtimePayloads::threadMessage($message),
                'thread' => RealtimePayloads::threadStatus($chatThread->fresh()),
            ]);
        }

        return redirect()
            ->route('chat.show', $chatThread)
            ->with('status', 'Pesan dikirim. AI sedang menyiapkan jawaban.');
    }

    private function shouldAutoTitle(ChatThread $thread): bool
    {
        return trim($thread->title) === self::AUTO_THREAD_TITLE
            && ! $thread->messages()->where('role', 'user')->exists();
    }

    private function makeTitleFromPrompt(string $prompt): string
    {
        $title = preg_replace('/\s+/', ' ', trim(strip_tags($prompt))) ?: self::AUTO_THREAD_TITLE;
        $title = trim($title, " \t\n\r\0\x0B\"'`.,;:!?");

        return Str::limit($title !== '' ? $title : self::AUTO_THREAD_TITLE, 64, '');
    }
}
