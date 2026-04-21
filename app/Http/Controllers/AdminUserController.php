<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(10);

        return view('pages.admin.users.index', compact('users'));
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
}
