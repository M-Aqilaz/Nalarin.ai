<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanCreateRoom
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403);
        }

        if ($user->plan === 'premium') {
            return $next($request);
        }

        if ($user->ownedRooms()->count() >= $user->room_limit) {
            return redirect()
                ->route('rooms.index')
                ->withErrors(['room' => __('ui.room_limit_reached_error')]);
        }

        return $next($request);
    }
}
