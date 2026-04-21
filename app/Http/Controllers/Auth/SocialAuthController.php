<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        if (! $this->isSupportedProvider($provider)) {
            abort(404);
        }

        if (! $this->isProviderConfigured($provider)) {
            return redirect()
                ->route('login')
                ->withErrors(['social' => 'Login dengan '.ucfirst($provider).' belum dikonfigurasi.']);
        }

        $driver = Socialite::driver($provider);

        if ($provider === 'discord') {
            $driver->scopes(['identify', 'email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! $this->isSupportedProvider($provider)) {
            abort(404);
        }

        if (! $this->isProviderConfigured($provider)) {
            return redirect()
                ->route('login')
                ->withErrors(['social' => 'Login dengan '.ucfirst($provider).' belum dikonfigurasi.']);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['social' => 'Autentikasi '.ucfirst($provider).' gagal. Silakan coba lagi.']);
        }

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user && $socialUser->getEmail()) {
            $user = User::query()->where('email', $socialUser->getEmail())->first();
        }

        $shouldDispatchRegisteredEvent = false;

        if (! $user) {
            $shouldDispatchRegisteredEvent = true;

            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: ucfirst($provider).' User',
                'email' => $socialUser->getEmail() ?: $this->placeholderEmail($provider, $socialUser->getId()),
                'password' => Hash::make(Str::random(40)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_avatar' => $socialUser->getAvatar(),
            ]);

            if ($socialUser->getEmail()) {
                $user->forceFill([
                    'email_verified_at' => now(),
                ])->save();
            }
        } else {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_avatar' => $socialUser->getAvatar() ?: $user->provider_avatar,
                'email_verified_at' => $user->email_verified_at ?: ($socialUser->getEmail() ? now() : null),
            ])->save();
        }

        if (! $user->is_active) {
            return redirect()
                ->route('login')
                ->withErrors(['social' => 'Akun Anda sedang dinonaktifkan. Hubungi admin untuk bantuan.']);
        }

        if ($shouldDispatchRegisteredEvent) {
            event(new Registered($user));
        }

        Auth::login($user, true);

        return redirect()->intended(
            $user->role === 'admin'
                ? route('admin.dashboard', absolute: false)
                : route('dashboard', absolute: false)
        );
    }

    protected function isSupportedProvider(string $provider): bool
    {
        return in_array($provider, ['google', 'discord'], true);
    }

    protected function isProviderConfigured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"))
            && filled(config("services.{$provider}.redirect"));
    }

    protected function placeholderEmail(string $provider, string $providerId): string
    {
        return "{$provider}_{$providerId}@users.nalarin.local";
    }
}
