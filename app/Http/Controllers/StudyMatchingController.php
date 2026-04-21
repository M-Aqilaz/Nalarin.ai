<?php

namespace App\Http\Controllers;

use App\Events\StudyMatchMessageCreated;
use App\Events\StudyMatchTypingUpdated;
use App\Models\StudyMatch;
use App\Models\UserBlock;
use App\Models\UserReport;
use App\Notifications\StudyMatchMessageNotification;
use App\Services\Learning\StudyMatchingService;
use App\Support\RealtimePayloads;
use App\Support\TypingStateStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyMatchingController extends Controller
{
    public function index(Request $request, StudyMatchingService $matchingService): View
    {
        $user = $request->user()->load('studyProfile');
        $activeMatch = $matchingService->findMatchFor($user)?->load(['userOne.studyProfile', 'userTwo.studyProfile', 'messages.user']);
        $queue = $user->matchQueueEntries()->where('status', 'waiting')->latest()->first();

        return view('pages.user.matchmaking.index', compact('user', 'activeMatch', 'queue'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'education_level' => ['nullable', 'string', 'max:80'],
            'primary_subject' => ['nullable', 'string', 'max:120'],
            'goal' => ['nullable', 'string', 'max:120'],
            'study_style' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'availability' => ['nullable', 'string', 'max:120'],
        ]);

        $request->user()->studyProfile()->updateOrCreate([], [
            ...$validated,
            'is_matchmaking_enabled' => $request->boolean('is_matchmaking_enabled'),
        ]);

        return redirect()->route('matchmaking.index')->with('status', 'Profil study matching diperbarui.');
    }

    public function search(Request $request, StudyMatchingService $matchingService): RedirectResponse
    {
        $validated = $request->validate([
            'selected_topic' => ['required', 'string', 'max:120'],
            'preferred_level' => ['nullable', 'string', 'max:80'],
            'preferred_session_type' => ['nullable', 'string', 'max:80'],
        ]);

        $result = $matchingService->enqueue($request->user()->load('studyProfile'), $validated);

        if ($result['error']) {
            return redirect()->route('matchmaking.index')->withErrors(['matchmaking' => $result['error']]);
        }

        if ($result['match']) {
            return redirect()->route('matches.show', $result['match'])->with('status', 'Partner belajar ditemukan.');
        }

        return redirect()->route('matchmaking.index')->with('status', 'Kamu masuk antrean study matching.');
    }

    public function cancel(Request $request, StudyMatchingService $matchingService): RedirectResponse
    {
        $matchingService->cancel($request->user());

        return redirect()->route('matchmaking.index')->with('status', 'Antrean study matching dibatalkan.');
    }

    public function show(StudyMatch $match): View
    {
        abort_unless($match->involves(auth()->user()), 403);
        $match->load([
            'userOne.studyProfile',
            'userTwo.studyProfile',
            'messages' => fn ($query) => $query->with('user')->orderBy('id'),
        ]);

        return view('pages.user.matchmaking.show', compact('match'));
    }

    public function messages(Request $request, StudyMatch $match, TypingStateStore $typingStateStore): JsonResponse
    {
        abort_unless($match->involves($request->user()), 403);

        $afterId = max(0, (int) $request->integer('after'));

        $messages = $match->messages()
            ->with('user')
            ->where('id', '>', $afterId)
            ->get()
            ->map(fn ($message) => RealtimePayloads::matchMessage($message))
            ->values();

        return response()->json([
            'messages' => $messages,
            'typing_users' => $typingStateStore->active('match', $match->id, $request->user()->id),
        ]);
    }

    public function typing(Request $request, StudyMatch $match, TypingStateStore $typingStateStore): JsonResponse
    {
        abort_unless($match->involves($request->user()), 403);
        $typingStateStore->touch('match', $match->id, $request->user()->id, $request->user()->name);

        broadcast(new StudyMatchTypingUpdated($match->id, $request->user()->id, $request->user()->name));

        return response()->json(['ok' => true]);
    }

    public function sendMessage(Request $request, StudyMatch $match): RedirectResponse|JsonResponse
    {
        abort_unless($match->involves($request->user()), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $message = $match->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        $message->load('user');
        broadcast(new StudyMatchMessageCreated($message));
        $match->partnerFor($request->user())?->notify(new StudyMatchMessageNotification($message));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => RealtimePayloads::matchMessage($message),
            ]);
        }

        return redirect()->route('matches.show', $match)->with('status', 'Pesan match terkirim.');
    }

    public function end(StudyMatch $match): RedirectResponse
    {
        abort_unless($match->involves(auth()->user()), 403);
        $match->update(['status' => 'completed']);

        return redirect()->route('matchmaking.index')->with('status', 'Sesi belajar ditutup.');
    }

    public function block(Request $request, StudyMatch $match): RedirectResponse
    {
        abort_unless($match->involves($request->user()), 403);
        $partner = $match->partnerFor($request->user());

        if ($partner) {
            UserBlock::firstOrCreate([
                'user_id' => $request->user()->id,
                'blocked_user_id' => $partner->id,
            ]);
        }

        $match->update(['status' => 'cancelled']);

        return redirect()->route('matchmaking.index')->with('status', 'Partner diblokir dan match ditutup.');
    }

    public function report(Request $request, StudyMatch $match): RedirectResponse
    {
        abort_unless($match->involves($request->user()), 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1200'],
        ]);

        $partner = $match->partnerFor($request->user());

        UserReport::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $partner?->id,
            'reportable_type' => StudyMatch::class,
            'reportable_id' => $match->id,
            'reason' => $validated['reason'],
            'status' => 'open',
        ]);

        return redirect()->route('matches.show', $match)->with('status', 'Laporan berhasil dikirim ke admin.');
    }
}
