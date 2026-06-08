<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-fuchsia-100/90">{{ __('ui.study_matching') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ __('ui.match_find_partner') }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.match_find_partner_description') }}</p>
        </div>
    </x-slot>
    <div class="space-y-6">
        @php
            $profileEnabled = $user->studyProfile?->is_matchmaking_enabled ?? false;
            $nalaMood = $activeMatch ? 'happy' : ($profileEnabled ? 'flat' : 'sad');
            $nalaTitle = $activeMatch ? __('ui.match_active_title') : ($profileEnabled ? __('ui.match_profile_ready_title') : __('ui.match_profile_inactive_title'));
            $nalaMessage = $activeMatch
                ? __('ui.match_active_description')
                : ($profileEnabled
                    ? __('ui.match_profile_ready_description')
                    : __('ui.match_profile_inactive_description'));
        @endphp

        <x-nala-guide :mood="$nalaMood" :title="$nalaTitle" :message="$nalaMessage" :action-label="$activeMatch ? __('ui.match_open_active') : __('ui.match_roulette')" :action-url="$activeMatch ? route('matches.show', $activeMatch) : route('matchmaking.roulette')" compact />

        <section class="feature-hero">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-fuchsia-100/90">{{ __('ui.match_meaningful_matching') }}</p>
                <h3 class="mt-3 font-outfit text-2xl font-semibold text-white">{{ __('ui.match_prepare_profile_title') }}</h3>
                <p class="mt-3 text-sm text-slate-100/80">{{ __('ui.match_prepare_profile_description') }}</p>
            </div>
            <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex items-center justify-center px-5 py-3 text-sm sm:w-auto">Study Roulette</a>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="glass-panel accent-card-violet rounded-[1.75rem] p-5 md:p-6 space-y-6">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
            @endif
            <div>
                <h3 class="font-outfit text-lg font-semibold text-white">{{ __('ui.match_profile') }}</h3>
                <p class="mt-1 text-sm text-slate-300/70">{{ __('ui.match_profile_form_description') }}</p>
            </div>
            <form method="POST" action="{{ route('matchmaking.profile.update') }}" class="space-y-4">
                @csrf
                <input name="education_level" value="{{ old('education_level', $user->studyProfile?->education_level) }}" placeholder="{{ __('ui.match_education_level') }}" class="glass-input w-full px-4 py-3">
                <input name="primary_subject" value="{{ old('primary_subject', $user->studyProfile?->primary_subject) }}" placeholder="{{ __('ui.match_primary_subject') }}" class="glass-input w-full px-4 py-3">
                <input name="goal" value="{{ old('goal', $user->studyProfile?->goal) }}" placeholder="{{ __('ui.match_learning_goal') }}" class="glass-input w-full px-4 py-3">
                <input name="study_style" value="{{ old('study_style', $user->studyProfile?->study_style) }}" placeholder="{{ __('ui.match_study_style') }}" class="glass-input w-full px-4 py-3">
                <input name="availability" value="{{ old('availability', $user->studyProfile?->availability) }}" placeholder="{{ __('ui.match_availability') }}" class="glass-input w-full px-4 py-3">
                <textarea name="bio" rows="4" placeholder="{{ __('ui.match_short_bio') }}" class="glass-input w-full px-4 py-3">{{ old('bio', $user->studyProfile?->bio) }}</textarea>
                <label class="flex items-start gap-3 text-sm text-slate-200"><input type="checkbox" name="is_matchmaking_enabled" value="1" class="mt-1 shrink-0" @checked(old('is_matchmaking_enabled', $user->studyProfile?->is_matchmaking_enabled ?? true))> <span>{{ __('ui.match_enable') }}</span></label>
                <button class="user-primary-button w-full px-5 py-3 sm:w-auto">{{ __('ui.match_save_profile') }}</button>
            </form>
        </section>

        <section class="glass-panel accent-card-pink rounded-[1.75rem] p-5 md:p-6 space-y-6">
            <div class="flex flex-col gap-4">
                <div>
                    <h3 class="font-outfit text-lg font-semibold text-white">Study Roulette</h3>
                    <p class="mt-1 text-sm text-slate-300/70">{{ __('ui.match_queue_description', ['count' => auth()->user()->match_credits]) }}</p>
                </div>
                @if ($activeMatch)
                    <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex w-full px-4 py-2.5 text-sm sm:w-auto">{{ __('ui.match_continue_roulette') }}</a>
                @endif
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-5">
                <p class="text-sm leading-6 text-slate-300/80">{{ __('ui.match_mode_description') }}</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.match_active_profile') }}</p>
                    <p class="mt-2 text-sm text-white">{{ ($user->studyProfile?->is_matchmaking_enabled ?? false) ? __('ui.match_ready_to_use') : __('ui.match_not_active') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.match_active_session') }}</p>
                    <p class="mt-2 text-sm text-white">{{ $activeMatch ? __('ui.match_session_running') : __('ui.match_no_active_session') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('matchmaking.roulette') }}" class="user-primary-button inline-flex w-full items-center justify-center px-6 py-3 text-sm sm:w-auto">{{ __('ui.match_open_roulette') }}</a>
                @if ($activeMatch)
                    <a href="{{ route('matches.show', $activeMatch) }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.08] sm:w-auto">{{ __('ui.match_open_active') }}</a>
                @endif
            </div>
        </section>
        </div>
    </div>
</x-app-layout>
