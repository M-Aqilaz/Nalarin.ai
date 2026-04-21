<?php

namespace App\Http\Controllers;

use App\Events\RoomMessageCreated;
use App\Events\RoomTypingUpdated;
use App\Models\User;
use App\Notifications\RoomMessageNotification;
use App\Models\StudyRoom;
use App\Support\RealtimePayloads;
use App\Support\TypingStateStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudyRoomMessageController extends Controller
{
    public function index(Request $request, StudyRoom $room, TypingStateStore $typingStateStore): JsonResponse
    {
        abort_unless($this->canReadMessages($request->user()->id, $room), 403);

        $afterId = max(0, (int) $request->integer('after'));
        $blockedIds = $request->user()->blockedUsers()->pluck('blocked_user_id')->all();

        $messages = $room->messages()
            ->with('user')
            ->where('id', '>', $afterId)
            ->whereNotIn('user_id', $blockedIds)
            ->get()
            ->map(fn ($message) => RealtimePayloads::roomMessage($message))
            ->values();

        return response()->json([
            'messages' => $messages,
            'typing_users' => $typingStateStore->active('room', $room->id, $request->user()->id),
        ]);
    }

    public function typing(Request $request, StudyRoom $room, TypingStateStore $typingStateStore): JsonResponse
    {
        abort_unless($room->members()->where('user_id', $request->user()->id)->where('status', 'active')->exists(), 403);
        $typingStateStore->touch('room', $room->id, $request->user()->id, $request->user()->name);

        broadcast(new RoomTypingUpdated($room->id, $request->user()->id, $request->user()->name));

        return response()->json(['ok' => true]);
    }

    public function store(Request $request, StudyRoom $room): RedirectResponse|JsonResponse
    {
        abort_unless($room->members()->where('user_id', $request->user()->id)->where('status', 'active')->exists(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $message = $room->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'type' => 'text',
        ]);

        $message->load('user');
        broadcast(new RoomMessageCreated($message));
        User::query()
            ->whereIn('id', $room->members()->where('status', 'active')->where('user_id', '!=', $request->user()->id)->pluck('user_id'))
            ->get()
            ->each(fn (User $user) => $user->notify(new RoomMessageNotification($message)));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => RealtimePayloads::roomMessage($message),
            ]);
        }

        return redirect()->route('rooms.show', $room)->with('status', 'Pesan room terkirim.');
    }

    private function canReadMessages(int $userId, StudyRoom $room): bool
    {
        if ($room->visibility === 'public') {
            return true;
        }

        return $room->members()->where('user_id', $userId)->where('status', 'active')->exists();
    }
}
