<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-orange-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-outfit text-2xl font-bold leading-tight text-white">Pomodoro Timer</h2>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .pomodoro-page {
            --accent: #f4b183;
            --accent-soft: rgba(244, 177, 131, 0.2);
            --accent-faint: rgba(244, 177, 131, 0.12);
        }

        .pomodoro-page[data-mode='shortBreak'] {
            --accent: #83d3dc;
            --accent-soft: rgba(131, 211, 220, 0.2);
            --accent-faint: rgba(131, 211, 220, 0.12);
        }

        .pomodoro-page[data-mode='longBreak'] {
            --accent: #94d7a4;
            --accent-soft: rgba(148, 215, 164, 0.2);
            --accent-faint: rgba(148, 215, 164, 0.12);
        }

        .pomodoro-input {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .pomodoro-input::-webkit-outer-spin-button,
        .pomodoro-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <div
        x-data="pomodoroTimer()"
        x-init="init()"
        :data-mode="mode"
        class="pomodoro-page relative min-h-full overflow-hidden md:h-full"
    >
        <div class="pointer-events-none absolute inset-x-12 top-0 h-48 rounded-full blur-3xl opacity-60" :style="'background: radial-gradient(circle, var(--accent-soft) 0%, transparent 72%);'"></div>

        <div class="relative mx-auto flex min-h-full max-w-6xl flex-col md:h-full">
            <section class="flex-1 overflow-hidden rounded-[32px] border border-white/10 bg-[linear-gradient(180deg,rgba(15,23,42,0.96),rgba(15,23,42,0.84))] p-4 shadow-[0_24px_80px_rgba(2,6,23,0.32)] md:p-5">
                <div class="grid items-start gap-3 xl:grid-cols-[minmax(0,1.62fr)_290px]">
                    <div class="flex min-h-0 flex-col gap-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="inline-flex flex-wrap gap-2 rounded-full border border-white/10 bg-white/[0.03] p-1">
                                <template x-for="option in [{ value: 'focus', label: 'Fokus' }, { value: 'shortBreak', label: 'Istirahat Pendek' }, { value: 'longBreak', label: 'Istirahat Panjang' }]" :key="option.value">
                                    <button
                                        type="button"
                                        @click="selectMode(option.value)"
                                        class="rounded-full px-4 py-2 text-sm font-medium transition"
                                        :class="mode === option.value ? 'text-slate-950 shadow-lg' : 'text-slate-300 hover:text-white'"
                                        :style="mode === option.value ? 'background: var(--accent);' : ''"
                                        x-text="option.label"
                                    ></button>
                                </template>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-xs font-medium text-slate-200">
                                <span class="h-2.5 w-2.5 rounded-full" :style="'background: var(--accent); box-shadow: 0 0 16px var(--accent);'"></span>
                                <span x-text="timerStatusLabel"></span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-5 rounded-[28px] border border-white/10 bg-white/[0.03] p-4 text-center md:p-5">
                            <div class="flex items-start justify-between gap-3 text-left">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Focus Flow</p>
                                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white md:text-2xl">Timer fokus yang rapi.</h3>
                                </div>

                                <div class="rounded-2xl border px-3 py-2 text-right" :style="'background: linear-gradient(135deg, var(--accent-faint), rgba(15,23,42,0.72)); border-color: var(--accent-soft);'">
                                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Siklus</p>
                                    <p class="mt-2 font-outfit text-xl text-white leading-none">
                                        <span x-text="completedCycleSessions"></span>
                                        <span class="text-sm text-slate-400">/ <span x-text="cycleTarget"></span></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-center py-1">
                                <div
                                    class="relative flex items-center justify-center rounded-full p-5"
                                    :style="`width: min(68vw, 30vh, 280px); height: min(68vw, 30vh, 280px); background: radial-gradient(circle at center, rgba(15,23,42,0.98) 0 58%, transparent 59%), conic-gradient(var(--accent) ${progressPercent}%, rgba(255,255,255,0.08) 0); box-shadow: 0 0 0 12px rgba(255,255,255,0.03);`"
                                >
                                    <div class="absolute inset-5 rounded-full border border-white/5"></div>
                                    <div class="relative z-10 px-4">
                                        <p class="text-[11px] uppercase tracking-[0.35em] text-slate-500" x-text="currentModeLabel"></p>
                                        <h1 class="mt-3 font-outfit text-4xl font-bold tracking-[-0.08em] text-white sm:text-5xl" x-text="formattedTime"></h1>
                                        <p class="mx-auto mt-3 max-w-[13rem] text-sm leading-5 text-slate-400" x-text="currentModeDescription"></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex flex-wrap justify-center gap-3">
                                    <button
                                        type="button"
                                        @click="toggleTimer()"
                                        class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl px-6 text-sm font-semibold text-slate-950 transition hover:brightness-105"
                                        :style="'background: var(--accent); box-shadow: 0 16px 40px var(--accent-faint);'"
                                    >
                                        <svg x-cloak x-show="!isRunning" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.5 5.5a1 1 0 011.53-.848l6 4a1 1 0 010 1.696l-6 4A1 1 0 016.5 13.5v-8z"></path>
                                        </svg>
                                        <svg x-cloak x-show="isRunning" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 5a1 1 0 011-1h1.5a1 1 0 011 1v10a1 1 0 01-1 1H7a1 1 0 01-1-1V5zm4.5 0a1 1 0 011-1H13a1 1 0 011 1v10a1 1 0 01-1 1h-1.5a1 1 0 01-1-1V5z"></path>
                                        </svg>
                                        <span x-text="primaryActionLabel"></span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="resetTimer()"
                                        class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-5 text-sm font-medium text-slate-100 transition hover:bg-white/[0.06]"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="button"
                                        @click="skipMode()"
                                        class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-5 text-sm font-medium text-slate-100 transition hover:bg-white/[0.06]"
                                    >
                                        Lewati
                                    </button>
                                </div>

                                <p class="mt-3 text-sm text-slate-400" x-text="notice"></p>
                            </div>
                        </div>
                    </div>

                    <aside class="flex flex-col gap-3">
                        <div class="rounded-[24px] border border-white/10 bg-[linear-gradient(180deg,rgba(15,23,42,0.88),rgba(15,23,42,0.68))] p-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Hari ini</p>
                                    <p class="mt-2 font-outfit text-3xl text-white leading-none">
                                        <span x-text="completedFocusSessions"></span>
                                        <span class="text-base text-slate-500">/ <span x-text="cycleTarget"></span></span>
                                    </p>
                                </div>
                                <span class="rounded-full border border-white/10 bg-slate-950/60 px-3 py-1 text-xs text-slate-300">Auto save</span>
                            </div>

                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full transition-all duration-300" :style="`width: ${dailyProgressPercent}%; background: linear-gradient(90deg, var(--accent), rgba(255,255,255,0.9));`"></div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3">
                                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Berikutnya</p>
                                    <p class="mt-2 font-outfit text-lg leading-tight text-white" x-text="upcomingModeLabel"></p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3">
                                    <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Long break</p>
                                    <p class="mt-2 font-outfit text-lg leading-tight text-white" x-text="`${sessionsUntilLongBreak} sesi`"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[24px] border border-white/10 bg-white/[0.03] p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-outfit text-lg text-white">Atur Durasi</h3>
                                    <p class="mt-1 text-xs text-slate-400">Ubah durasi tanpa bikin layout kepanjangan.</p>
                                </div>
                                <span class="text-xs uppercase tracking-[0.24em] text-slate-500">menit</span>
                            </div>

                            <div class="mt-4 space-y-3">
                                <template x-for="field in [
                                    { key: 'focus', label: 'Fokus', min: 10, max: 90 },
                                    { key: 'shortBreak', label: 'Istirahat Pendek', min: 1, max: 30 },
                                    { key: 'longBreak', label: 'Istirahat Panjang', min: 5, max: 60 }
                                ]" :key="field.key">
                                    <label class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-2.5">
                                        <div>
                                            <p class="font-medium text-white" x-text="field.label"></p>
                                            <p class="mt-1 text-[10px] text-slate-500" x-text="`${field.min}-${field.max} menit`"></p>
                                        </div>

                                        <div class="flex w-28 items-center gap-2 rounded-xl border border-white/10 bg-slate-950/80 px-3 py-1.5">
                                            <input
                                                type="number"
                                                :min="field.min"
                                                :max="field.max"
                                                x-model.number="durations[field.key]"
                                                @change="updateDuration(field.key, durations[field.key])"
                                                class="pomodoro-input w-full border-0 bg-transparent text-right text-white outline-none focus:outline-none focus:ring-0"
                                            >
                                            <span class="text-xs text-slate-400">m</span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
