<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $selectedPlan = $request->query('plan');
        $usersQuery = User::query()->latest();

        if (in_array($selectedPlan, ['free', 'premium'], true)) {
            $usersQuery->where('plan', $selectedPlan);
        } else {
            $selectedPlan = 'all';
        }

        $users = $usersQuery->paginate(10)->withQueryString();
        $planStats = [
            'all' => User::count(),
            'free' => User::where('plan', 'free')->count(),
            'premium' => User::where('plan', 'premium')->count(),
        ];

        return view('pages.admin.users.index', compact('users', 'planStats', 'selectedPlan'));
    }

    public function suspend(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa men-suspend akun sendiri.');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'User berhasil disuspend.');
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return back()->with('success', 'User berhasil diaktifkan.');
    }

    public function updatePlan(Request $request, User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Paket akun admin tidak perlu diubah dari halaman user.');
        }

        $validated = $request->validate([
            'plan' => ['required', 'in:free,premium'],
        ]);

        $planLimits = $validated['plan'] === 'premium'
            ? ['room_limit' => 10, 'match_credits' => 99]
            : ['room_limit' => 2, 'match_credits' => 3];

        $user->update([
            'plan' => $validated['plan'],
            ...$planLimits,
        ]);

        $label = $validated['plan'] === 'premium' ? 'Paid' : 'Freemium';

        return back()->with('success', "Paket {$user->name} berhasil diubah ke {$label}.");
    }
}
