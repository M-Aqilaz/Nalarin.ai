<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudyRoomMessageController extends Controller
{
    public function store(Request $request, StudyRoom $room): RedirectResponse
    {
        abort_unless($room->members()->where('user_id', $request->user()->id)->where('status', 'active')->exists(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $room->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'type' => 'text',
        ]);

        return redirect()->route('rooms.show', $room)->with('status', 'Pesan room terkirim.');
    }
}
