<?php

namespace App\Support;

use App\Models\User;

trait ResolvesLearningUser
{
    protected function resolveLearningUser(): User
    {
        return auth()->user()
            ?? User::query()->firstOrCreate(
                ['email' => 'test@example.com'],
                ['name' => 'Test User', 'password' => 'password', 'role' => 'user']
            );
    }
}
