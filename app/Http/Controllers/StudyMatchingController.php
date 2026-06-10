<?php

namespace App\Http\Controllers;

use App\Events\StudyMatchMessageCreated;
use App\Events\StudyMatchTypingUpdated;
use App\Models\StudyMatch;
use App\Models\UserBlock;
use App\Models\UserReport;
use App\Notifications\StudyMatchMessageNotification;
use App\Services\Analytics\AnalyticsTracker;
use App\Services\Learning\StudyMatchingService;
use App\Support\RealtimePayloads;
use App\Support\TypingStateStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyMatchingController extends Controller
{
    public function index(Request $request, StudyMatchingService $matchingService): View|RedirectResponse
    {
        $user = $request->user()->load('studyProfile');

        if ($user->studyProfile) {
            return redirect()->route('matchmaking.roulette');
        }

        $activeMatch = $matchingService->findMatchFor($user)?->load(['userOne.studyProfile', 'userTwo.studyProfile', 'messages.user']);
        $queue = $user->matchQueueEntries()
            ->where('status', 'waiting')
            ->where('selected_topic', '!=', \App\Models\MatchQueueEntry::ROULETTE_TOPIC)
            ->latest()
            ->first();

        return view('pages.user.matchmaking.index', compact('user', 'activeMatch', 'queue'));
    }

    public function roulette(Request $request, StudyMatchingService $matchingService): View
    {
        $user = $request->user()->load('studyProfile');
        $activeMatch = $matchingService->findRouletteMatchFor($user)?->load(['userOne.studyProfile', 'userTwo.studyProfile', 'messages.user']);
        $queue = $user->matchQueueEntries()
            ->where('status', 'waiting')
            ->where('selected_topic', \App\Models\MatchQueueEntry::ROULETTE_TOPIC)
            ->latest()
            ->first();

        return view('pages.user.matchmaking.roulette', compact('user', 'activeMatch', 'queue'));
    }

    public function rouletteStatus(Request $request, StudyMatchingService $matchingService): JsonResponse
    {
        $user = $request->user();
        $activeMatch = $matchingService->findRouletteMatchFor($user);
        $latestRouletteMatch = StudyMatch::query()
            ->where('topic', StudyMatchingService::ROULETTE_TOPIC_LABEL)
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->latest('matched_at')
            ->first();
        $queue = $user->matchQueueEntries()
            ->where('status', 'waiting')
            ->where('selected_topic', \App\Models\MatchQueueEntry::ROULETTE_TOPIC)
            ->latest()
            ->first();

        return response()->json([
            'matched' => (bool) $activeMatch,
            'match_id' => $activeMatch?->id,
            'redirect_url' => $activeMatch ? route('matchmaking.roulette') : null,
            'searching' => (bool) $queue,
            'queue_status' => $queue?->status,
            'latest_match_id' => $latestRouletteMatch?->id,
            'latest_match_status' => $latestRouletteMatch?->status,
        ]);
    }

    public function updateProfile(Request $request, AnalyticsTracker $analytics): RedirectResponse
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

        $analytics->trackFeature($request->user(), 'study_matching_profile', 'Study Matching', 'profile_updated', [
            'enabled' => $request->boolean('is_matchmaking_enabled'),
        ], $request);

        return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_profile_ready_status'));
    }

    public function search(Request $request, StudyMatchingService $matchingService, AnalyticsTracker $analytics): RedirectResponse
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

        $analytics->trackFeature($request->user(), 'study_matching_search', 'Study Matching', $result['match'] ? 'matched' : 'queued', [
            'selected_topic' => $validated['selected_topic'],
            'match_id' => $result['match']?->id,
        ], $request);

        if ($result['match']) {
            return redirect()->route('matches.show', $result['match'])->with('status', __('ui.match_found_status'));
        }

        return redirect()->route('matchmaking.index')->with('status', __('ui.match_queue_joined_status'));
    }

    public function cancel(Request $request, StudyMatchingService $matchingService, AnalyticsTracker $analytics): RedirectResponse
    {
        $matchingService->cancel($request->user());

        $analytics->trackFeature($request->user(), 'study_matching_cancel', 'Study Matching', 'cancelled', [], $request);

        return redirect()->route('matchmaking.index')->with('status', __('ui.match_queue_cancelled_status'));
    }

    public function rouletteStart(Request $request, StudyMatchingService $matchingService, AnalyticsTracker $analytics): RedirectResponse
    {
        $result = $matchingService->enqueueRoulette($request->user()->load('studyProfile'));

        if ($result['error']) {
            return redirect()->route('matchmaking.roulette')->withErrors(['matchmaking' => $result['error']]);
        }

        $analytics->trackFeature($request->user(), 'study_matching_roulette_start', 'Study Matching', $result['match'] ? 'matched' : 'queued', [
            'match_id' => $result['match']?->id,
        ], $request);

        if ($result['match']) {
            return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_found_status'));
        }

        return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_searching_status_message'));
    }

    public function rouletteNext(Request $request, StudyMatchingService $matchingService, AnalyticsTracker $analytics): RedirectResponse
    {
        $user = $request->user();
        $activeMatch = $matchingService->findRouletteMatchFor($user);

        if ($activeMatch && $activeMatch->involves($user)) {
            $activeMatch->update(['status' => 'completed']);
        }

        $matchingService->cancelRoulette($user);
        $result = $matchingService->enqueueRoulette($user->load('studyProfile'));

        if ($result['error']) {
            return redirect()->route('matchmaking.roulette')->withErrors(['matchmaking' => $result['error']]);
        }

        $analytics->trackFeature($request->user(), 'study_matching_roulette_next', 'Study Matching', $result['match'] ? 'matched' : 'queued', [
            'match_id' => $result['match']?->id,
        ], $request);

        if ($result['match']) {
            return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_new_partner_found_status'));
        }

        return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_searching_next_status'));
    }

    public function rouletteStop(Request $request, StudyMatchingService $matchingService, AnalyticsTracker $analytics): RedirectResponse
    {
        $user = $request->user();
        $activeMatch = $matchingService->findRouletteMatchFor($user);

        if ($activeMatch && $activeMatch->involves($user)) {
            $activeMatch->update(['status' => 'completed']);
        }

        $matchingService->cancelRoulette($user);

        $analytics->trackFeature($request->user(), 'study_matching_roulette_stop', 'Study Matching', 'stopped', [
            'match_id' => $activeMatch?->id,
        ], $request);

        return redirect()->route('matchmaking.roulette')->with('status', __('ui.match_stopped_status'));
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

    public function sendMessage(Request $request, StudyMatch $match, AnalyticsTracker $analytics): RedirectResponse|JsonResponse
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
        $analytics->trackFeature($request->user(), 'study_matching_message', 'Study Matching', 'message_sent', [
            'match_id' => $match->id,
            'message_id' => $message->id,
        ], $request);

        broadcast(new StudyMatchMessageCreated($message));
        $match->partnerFor($request->user())?->notify(new StudyMatchMessageNotification($message));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => RealtimePayloads::matchMessage($message),
            ]);
        }

        return redirect()->route('matches.show', $match)->with('status', __('ui.match_message_sent_status'));
    }

    public function end(Request $request, StudyMatch $match, AnalyticsTracker $analytics): RedirectResponse
    {
        abort_unless($match->involves(auth()->user()), 403);
        $match->update(['status' => 'completed']);

        $analytics->trackFeature($request->user(), 'study_matching_end', 'Study Matching', 'completed', [
            'match_id' => $match->id,
        ], $request);

        return redirect()->route('matchmaking.index')->with('status', __('ui.match_session_closed_status'));
    }

    public function block(Request $request, StudyMatch $match, AnalyticsTracker $analytics): RedirectResponse
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

        $analytics->trackFeature($request->user(), 'study_matching_block', 'Study Matching', 'blocked', [
            'match_id' => $match->id,
            'blocked_user_id' => $partner?->id,
        ], $request);

        return redirect()->route('matchmaking.index')->with('status', __('ui.match_partner_blocked_status'));
    }

    public function report(Request $request, StudyMatch $match, AnalyticsTracker $analytics): RedirectResponse
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

        $analytics->trackFeature($request->user(), 'study_matching_report', 'Study Matching', 'reported', [
            'match_id' => $match->id,
            'reported_user_id' => $partner?->id,
        ], $request);

        return redirect()->route('matches.show', $match)->with('status', __('ui.match_report_sent_status'));
    }
}
