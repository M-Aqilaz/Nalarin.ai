<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-violet-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M7 16V9m5 7V5m5 11v-4"></path>
                </svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-violet-100/90">{{ __('ui.focus_section') }}</p>
                <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text">{{ __('ui.focus_insights') }}</h2>
            </div>
        </div>
    </x-slot>

    @php
        $insightTranslations = collect([
            'scoreVeryStable', 'scoreStable', 'scoreUnstable', 'scoreNotFormed', 'noMode',
            'mode_deep-work', 'mode_review', 'mode_practice', 'coachStrong', 'coachMomentum',
            'coachOutput', 'coachConsistency', 'recommendationTargetReached',
            'recommendationFinishPriority', 'recommendationContinueBlock', 'recommendationStartPomodoro',
        ])->mapWithKeys(fn ($key) => [$key => __("ui.insights_{$key}")])->all();
    @endphp

    <div x-data="focusInsights({ locale: @js(app()->getLocale()), translations: @js($insightTranslations) })" x-init="init()" class="readable-study-page space-y-6">
        <section class="feature-hero overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-56 rounded-full bg-violet-400/15 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="user-kicker text-violet-100/90">{{ __('ui.insights_progress_analytics') }}</p>
                    <h1 class="mt-3 font-outfit text-3xl font-semibold tracking-tight text-white md:text-4xl">
                        {{ __('ui.insights_hero_title') }}
                    </h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-200/80">
                        {{ __('ui.insights_hero_description') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="rounded-full border border-white/10 bg-white/[0.05] px-4 py-2 text-xs font-medium text-slate-200">
                        {{ __('ui.insights_last_sync') }} <span x-text="lastSyncLabel"></span>
                    </span>
                    <button type="button" @click="refresh()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-5 text-sm font-medium text-slate-100 transition hover:bg-white/[0.06]">
                        {{ __('ui.insights_refresh') }}
                    </button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.insights_focus_score') }}</p>
                <p class="mt-3 font-outfit text-4xl text-white" x-text="focusScore"></p>
                <p class="mt-2 text-sm text-slate-400" x-text="focusScoreLabel"></p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.insights_completed_sessions') }}</p>
                <p class="mt-3 font-outfit text-4xl text-white" x-text="completedFocusSessions"></p>
                <p class="mt-2 text-sm text-slate-400">{{ __('ui.insights_daily_target') }} <span x-text="cycleTarget"></span> {{ __('ui.planner_sessions_lower') }}</p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.insights_completed_tasks') }}</p>
                <p class="mt-3 font-outfit text-4xl text-white"><span x-text="completedTaskCount"></span>/<span x-text="taskCount"></span></p>
                <p class="mt-2 text-sm text-slate-400"><span x-text="taskCompletionPercent"></span>% {{ __('ui.insights_planner_tasks_completed') }}</p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.insights_completed_blocks') }}</p>
                <p class="mt-3 font-outfit text-4xl text-white"><span x-text="completedBlockCount"></span>/<span x-text="blockCount"></span></p>
                <p class="mt-2 text-sm text-slate-400"><span x-text="blockCompletionPercent"></span>% {{ __('ui.insights_block_execution_lower') }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
            <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="user-kicker">{{ __('ui.insights_focus_health') }}</p>
                        <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">{{ __('ui.insights_performance_summary') }}</h3>
                    </div>
                    <span class="rounded-full border border-violet-400/20 bg-violet-400/10 px-4 py-2 text-xs font-medium text-violet-100">
                        {{ __('ui.insights_daily_target') }} <span x-text="progressPercent"></span>%
                    </span>
                </div>

                <div class="mt-6 grid gap-4">
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>{{ __('ui.insights_pomodoro_target') }}</span>
                            <span><span x-text="progressPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(167,139,250,0.95),rgba(34,211,238,0.95))] transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>{{ __('ui.insights_task_completion') }}</span>
                            <span><span x-text="taskCompletionPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(34,211,238,0.95),rgba(59,130,246,0.95))] transition-all duration-300" :style="`width: ${taskCompletionPercent}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>{{ __('ui.insights_block_execution') }}</span>
                            <span><span x-text="blockCompletionPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(16,185,129,0.95),rgba(34,211,238,0.95))] transition-all duration-300" :style="`width: ${blockCompletionPercent}%`"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.insights_recommendation') }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200" x-text="recommendation"></p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.insights_planner_status') }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200">
                            <span x-show="taskCount || blockCount">{{ __('ui.insights_planner_active_with') }} <span x-text="taskCount"></span> {{ __('ui.insights_tasks_and') }} <span x-text="blockCount"></span> {{ __('ui.insights_focus_blocks_lower') }}.</span>
                            <span x-show="!taskCount && !blockCount">{{ __('ui.insights_no_planner_data') }}</span>
                        </p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.insights_coach_note') }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200" x-text="coachMessage"></p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.insights_strongest_mode') }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200"><span class="uppercase" x-text="strongestMode"></span> {{ __('ui.insights_most_used_mode') }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">{{ __('ui.insights_deep_breakdown') }}</p>
                    <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">{{ __('ui.insights_focus_rhythm') }}</h3>
                    <div class="mt-5 space-y-3">
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">{{ __('ui.insights_planned_vs_done') }}</p>
                            <p class="mt-2 text-sm text-slate-400"><span x-text="completedTaskSessions"></span> {{ __('ui.insights_of') }} <span x-text="totalPlannedSessions"></span> {{ __('ui.insights_task_sessions_completed') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">{{ __('ui.insights_focused_minutes') }}</p>
                            <p class="mt-2 text-sm text-slate-400"><span x-text="completedMinutes"></span> {{ __('ui.insights_focused_minutes_completed') }} <span x-text="plannedMinutes"></span> {{ __('ui.insights_planned_minutes') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">{{ __('ui.insights_quick_interpretation') }}</p>
                            <p class="mt-2 text-sm text-slate-400">{{ __('ui.insights_quick_interpretation_description') }}</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">{{ __('ui.insights_next_move') }}</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white">{{ __('ui.insights_next_step') }}</h3>
                    <p class="mt-4 text-sm leading-6 text-slate-300/85">
                        {{ __('ui.insights_next_step_description') }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
