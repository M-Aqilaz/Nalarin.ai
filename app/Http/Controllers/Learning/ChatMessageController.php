<?php

namespace App\Http\Controllers\Learning;

use App\Events\ThreadAiStatusUpdated;
use App\Events\ThreadMessageCreated;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateThreadAiReply;
use App\Models\ChatThread;
use App\Support\RealtimePayloads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
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

    public function store(Request $request, ChatThread $chatThread): RedirectResponse|JsonResponse
    {
        abort_unless($chatThread->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $message = $chatThread->messages()->create([
            'role' => 'user',
            'content' => $validated['content'],
        ]);

        $chatThread->forceFill([
            'ai_status' => 'queued',
            'ai_error' => null,
        ])->save();

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
}
