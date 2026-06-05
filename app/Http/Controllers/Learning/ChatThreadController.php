<?php

namespace App\Http\Controllers\Learning;

use App\Events\ThreadAiStatusUpdated;
use App\Jobs\GenerateThreadAiReply;
use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatThreadController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $threads = ChatThread::query()
            ->where('user_id', auth()->id())
            ->with(['material', 'user'])
            ->withCount('messages')
            ->latest()
            ->get();

        if ($threads->isNotEmpty()) {
            return redirect()->route('chat.show', $threads->first());
        }

        $materials = Material::query()->where('user_id', auth()->id())->latest()->get(['id', 'title']);

        return view('pages.user.chat.index', compact('threads', 'materials'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'material_id' => ['nullable', 'exists:materials,id'],
            'opening_message' => ['nullable', 'string'],
        ]);

        if (! empty($validated['material_id'])) {
            Material::query()
                ->where('id', $validated['material_id'])
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        }

        $thread = ChatThread::create([
            'user_id' => $request->user()->id,
            'material_id' => $validated['material_id'] ?? null,
            'title' => $validated['title'],
            'ai_status' => 'idle',
        ]);

        if (! empty($validated['opening_message'])) {
            $thread->messages()->create([
                'role' => 'user',
                'content' => $validated['opening_message'],
            ]);

            $thread->forceFill([
                'ai_status' => 'queued',
                'ai_error' => null,
            ])->save();

            broadcast(new ThreadAiStatusUpdated($thread->fresh()));
            GenerateThreadAiReply::dispatch($thread->id);
        }

        return redirect()
            ->route('chat.show', $thread)
            ->with('status', 'Thread chat berhasil dibuat.');
    }

    public function update(Request $request, ChatThread $chatThread): RedirectResponse
    {
        abort_unless($chatThread->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'material_id' => ['nullable', 'exists:materials,id'],
        ]);

        if (! empty($validated['material_id'])) {
            Material::query()
                ->where('id', $validated['material_id'])
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        }

        $chatThread->update([
            'title' => $validated['title'],
            'material_id' => $validated['material_id'] ?? null,
        ]);

        return redirect()
            ->route('chat.show', $chatThread)
            ->with('status', 'Thread chat berhasil diperbarui.');
    }

    public function destroy(Request $request, ChatThread $chatThread): RedirectResponse
    {
        abort_unless($chatThread->user_id === $request->user()->id, 403);

        DB::transaction(function () use ($chatThread) {
            $chatThread->messages()->with('attachments')->get()->each(function ($message) {
                $message->attachments->each(fn ($attachment) => Storage::disk($attachment->disk)->delete($attachment->path));
                $message->delete();
            });

            Storage::disk('public')->deleteDirectory('chat-attachments/'.$chatThread->id);
            $chatThread->delete();
        });

        $nextThread = ChatThread::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        return redirect()
            ->route($nextThread ? 'chat.show' : 'feature.chat', $nextThread ? [$nextThread] : [])
            ->with('status', 'Thread dan seluruh percakapannya berhasil dihapus.');
    }

    public function show(ChatThread $chatThread): View
    {
        abort_unless($chatThread->user_id === auth()->id(), 403);
        $chatThread->load([
            'material',
            'user',
            'messages' => fn ($query) => $query->with('attachments')->orderBy('id'),
        ]);

        $threads = ChatThread::query()
            ->where('user_id', auth()->id())
            ->with(['material'])
            ->withCount('messages')
            ->latest()
            ->get();

        $materials = Material::query()->where('user_id', auth()->id())->latest()->get(['id', 'title']);

        return view('pages.user.chat.show', [
            'thread' => $chatThread,
            'threads' => $threads,
            'materials' => $materials,
        ]);
    }
}
