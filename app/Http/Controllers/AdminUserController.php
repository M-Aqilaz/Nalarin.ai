<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('Admin.users.index', compact('users'));
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa men-suspend akun sendiri.');
        }
        
        $user->update(['status' => 'suspended']);
        return back()->with('success', 'User berhasil disuspend.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User berhasil diaktifkan.');
    }
}
