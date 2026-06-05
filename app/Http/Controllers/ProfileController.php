<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $roomLimit = max(0, (int) $user->room_limit);
        $ownedRoomCount = $user->ownedRooms()->count();
        $matchAllowance = $user->isPremium() ? 99 : 3;
        $matchAllowance = max($matchAllowance, (int) $user->match_credits);

        return view('pages.user.profile.edit', [
            'user' => $user,
            'limitStats' => [
                'room_limit' => $roomLimit,
                'owned_room_count' => $ownedRoomCount,
                'room_remaining' => max(0, $roomLimit - $ownedRoomCount),
                'room_percent' => $roomLimit > 0 ? min(100, (int) round(($ownedRoomCount / $roomLimit) * 100)) : 100,
                'match_allowance' => $matchAllowance,
                'match_remaining' => (int) $user->match_credits,
                'match_percent' => $matchAllowance > 0 ? min(100, (int) round(((int) $user->match_credits / $matchAllowance) * 100)) : 0,
            ],
        ]);
    }

    /**
     * Display the admin profile form inside the admin dashboard shell.
     */
    public function adminEdit(Request $request): View
    {
        return view('pages.admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        $route = $request->user()->role === 'admin' ? 'admin.profile.edit' : 'profile.edit';

        return Redirect::route($route)->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
