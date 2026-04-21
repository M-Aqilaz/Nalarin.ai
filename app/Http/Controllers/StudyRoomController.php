<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudyRoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = StudyRoom::query()
            ->with(['owner', 'members.user'])
            ->withCount('members')
            ->latest()
            ->get();

        $myRooms = $request->user()->roomMemberships()->with('room')->latest()->get();

        return view('pages.user.rooms.index', compact('rooms', 'myRooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visibility' => ['required', 'in:public,private'],
            'max_members' => ['required', 'integer', 'between:5,100'],
        ]);

        $room = StudyRoom::create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(5)),
            'topic' => $validated['topic'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
            'max_members' => $validated['max_members'],
        ]);

        $room->members()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->route('rooms.show', $room)->with('status', 'Room belajar berhasil dibuat.');
    }

    public function show(Request $request, StudyRoom $room): View
    {
        abort_unless($this->canAccess($request->user()->id, $room), 403);

        $room->load([
            'owner',
            'members.user',
            'messages' => fn ($query) => $query->with('user')->orderBy('id'),
        ]);
        $blockedIds = $request->user()->blockedUsers()->pluck('blocked_user_id')->all();
        $messages = $room->messages->reject(fn ($message) => in_array($message->user_id, $blockedIds, true))->values();
        $isMember = $room->members()->where('user_id', $request->user()->id)->where('status', 'active')->exists();

        return view('pages.user.rooms.show', compact('room', 'messages', 'isMember'));
    }

    public function join(Request $request, StudyRoom $room): RedirectResponse
    {
        abort_unless($room->is_active, 404);

        $memberCount = $room->members()->where('status', 'active')->count();

        if ($memberCount >= $room->max_members) {
            return redirect()->route('rooms.index')->withErrors(['room' => 'Room sudah penuh.']);
        }

        $membership = $room->members()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['role' => 'member', 'status' => 'active', 'joined_at' => now()]
        );

        if ($membership->status !== 'active') {
            $membership->forceFill(['status' => 'active', 'joined_at' => now()])->save();
        }

        return redirect()->route('rooms.show', $room)->with('status', 'Kamu sudah bergabung ke room ini.');
    }

    public function leave(Request $request, StudyRoom $room): RedirectResponse
    {
        $room->members()->where('user_id', $request->user()->id)->update(['status' => 'left']);

        return redirect()->route('rooms.index')->with('status', 'Kamu keluar dari room.');
    }

    private function canAccess(int $userId, StudyRoom $room): bool
    {
        if ($room->visibility === 'public') {
            return true;
        }

        return $room->members()->where('user_id', $userId)->where('status', 'active')->exists();
    }
}
