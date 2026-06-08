<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-orange-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-orange-100/90">Study Rhythm</p>
                <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text">Pomodoro Timer</h2>
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

        .pomodoro-shell {
            background:
                radial-gradient(circle at 18% 12%, var(--accent-soft), transparent 30%),
                linear-gradient(145deg, rgba(255, 255, 255, 0.96), rgba(240, 249, 255, 0.9));
        }

        .pomodoro-timer-card {
            background:
                radial-gradient(circle at 50% 30%, var(--accent-faint), transparent 42%),
                rgba(255, 255, 255, 0.82);
        }

        .pomodoro-clock {
            background:
                radial-gradient(circle at center, #ffffff 0 57%, transparent 58%),
                conic-gradient(var(--accent) var(--progress), rgba(148, 163, 184, 0.18) 0);
            box-shadow:
                0 0 0 12px rgba(255, 255, 255, 0.55),
                0 28px 70px var(--accent-soft);
        }

        .pomodoro-clock::before {
            content: '';
            position: absolute;
            inset: 17px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: inherit;
        }

        @media (min-width: 1280px) {
            .pomodoro-page,
            .pomodoro-page > div:last-child,
            .pomodoro-shell {
                height: 100%;
            }

            .pomodoro-clock {
                width: min(15rem, 27vh) !important;
                height: min(15rem, 27vh) !important;
            }
        }

        @media (max-width: 767px) {
            .pomodoro-clock {
                width: min(74vw, 18rem) !important;
                height: min(74vw, 18rem) !important;
            }
        }
    </style>

    <div
        x-data="pomodoroTimer()"
        x-init="init()"
        :data-mode="mode"
        class="pomodoro-page readable-study-page relative min-h-full overflow-hidden"
    >
        <div class="pointer-events-none absolute inset-x-12 top-0 h-48 rounded-full blur-3xl opacity-60" :style="'background: radial-gradient(circle, var(--accent-soft) 0%, transparent 72%);'"></div>

        <div class="relative mx-auto max-w-7xl">
            <section class="pomodoro-shell overflow-hidden rounded-[2rem] border border-sky-200/80 p-3 shadow-[0_24px_70px_rgba(14,116,144,0.12)] md:p-4">
                <div class="mb-3">
                    <x-nala-guide
                        mood="flat"
                        title="Mode fokus dimulai dari niat kecil"
                        message="Pilih durasi yang realistis, lalu jalankan satu sesi penuh. Kalau baru mulai, jangan sok kuat ambil terlalu lama."
                        image-class="h-20 w-20 rounded-2xl object-cover sm:h-20 sm:w-20"
                        class="!rounded-3xl !p-3"
                        compact
                    />
                </div>

                <div class="grid items-start gap-3 xl:grid-cols-[minmax(0,1fr)_330px]">
                    <main class="pomodoro-timer-card overflow-hidden rounded-[2rem] border border-sky-200/80 p-4 shadow-[0_18px_50px_rgba(14,116,144,0.1)]">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="inline-flex w-full rounded-2xl border border-sky-200 bg-white/80 p-1 sm:w-auto">
                                <template x-for="option in [{ value: 'focus', label: 'Fokus' }, { value: 'shortBreak', label: 'Istirahat Pendek' }, { value: 'longBreak', label: 'Istirahat Panjang' }]" :key="option.value">
                                    <button
                                        type="button"
                                        @click="selectMode(option.value)"
                                        class="min-w-0 flex-1 rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition sm:flex-none"
                                        :class="mode === option.value ? 'text-slate-950 shadow-sm' : 'hover:bg-sky-50 hover:text-slate-950'"
                                        :style="mode === option.value ? 'background: var(--accent);' : ''"
                                        x-text="option.label"
                                    ></button>
                                </template>
                            </div>

                            <div class="inline-flex items-center justify-center gap-2 rounded-full border border-sky-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full" :style="'background: var(--accent); box-shadow: 0 0 12px var(--accent);'"></span>
                                <span x-text="timerStatusLabel"></span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-col items-center text-center">
                            <div class="flex w-full items-start justify-between gap-4 text-left">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.26em] text-sky-700">Focus Flow</p>
                                    <h3 class="mt-1 font-outfit text-xl font-extrabold text-slate-950">Satu sesi, satu tujuan.</h3>
                                </div>
                                <div class="rounded-2xl border border-sky-200 bg-white/80 px-3 py-2 text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Siklus</p>
                                    <p class="mt-1 font-outfit text-xl font-bold text-slate-950">
                                        <span x-text="completedCycleSessions"></span>
                                        <span class="text-sm text-slate-400">/ <span x-text="cycleTarget"></span></span>
                                    </p>
                                </div>
                            </div>

                            <div
                                class="pomodoro-clock relative mt-3 flex h-[19rem] w-[19rem] items-center justify-center rounded-full"
                                :style="`--progress: ${progressPercent}%`"
                            >
                                <div class="relative z-10 w-[68%]">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.34em] text-sky-700" x-text="currentModeLabel"></p>
                                    <h1 class="mt-2 font-outfit text-5xl font-extrabold tracking-[-0.07em] text-slate-950" x-text="formattedTime"></h1>
                                    <p class="mx-auto mt-2 text-xs leading-4 text-slate-500" x-text="currentModeDescription"></p>
                                </div>
                            </div>

                            <div class="mt-4 flex w-full flex-col justify-center gap-2 sm:flex-row">
                                <button
                                    type="button"
                                    @click="toggleTimer()"
                                    class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl px-8 text-sm font-extrabold text-slate-950 transition hover:-translate-y-0.5 hover:brightness-105"
                                    :style="'background: var(--accent); box-shadow: 0 14px 30px var(--accent-soft);'"
                                >
                                    <svg x-cloak x-show="!isRunning" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.5 5.5a1 1 0 011.53-.848l6 4a1 1 0 010 1.696l-6 4A1 1 0 016.5 13.5v-8z"></path>
                                    </svg>
                                    <svg x-cloak x-show="isRunning" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 5a1 1 0 011-1h1.5a1 1 0 011 1v10a1 1 0 01-1 1H7a1 1 0 01-1-1V5zm4.5 0a1 1 0 011-1H13a1 1 0 011 1v10a1 1 0 01-1 1h-1.5a1 1 0 01-1-1V5z"></path>
                                    </svg>
                                    <span x-text="primaryActionLabel"></span>
                                </button>
                                <button type="button" @click="resetTimer()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 text-sm font-bold text-slate-700 transition hover:bg-sky-50">Reset</button>
                                <button type="button" @click="skipMode()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 text-sm font-bold text-slate-700 transition hover:bg-sky-50">Lewati</button>
                            </div>

                            <p class="mt-2 min-h-5 text-xs font-medium text-slate-500" x-text="notice"></p>
                        </div>
                    </main>

                    <aside class="space-y-2">
                        <section class="rounded-[1.75rem] border border-sky-200 bg-white/85 p-3 shadow-[0_16px_40px_rgba(14,116,144,0.08)]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-sky-700">Hari Ini</p>
                                    <p class="mt-1 font-outfit text-3xl font-extrabold text-slate-950">
                                        <span x-text="completedFocusSessions"></span>
                                        <span class="text-lg text-slate-400">/ <span x-text="cycleTarget"></span></span>
                                    </p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700">Tersimpan otomatis</span>
                            </div>

                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full transition-all duration-300" :style="`width: ${dailyProgressPercent}%; background: linear-gradient(90deg, var(--accent), #38bdf8);`"></div>
                            </div>

                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <div class="rounded-2xl bg-sky-50 p-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-sky-700">Berikutnya</p>
                                    <p class="mt-1 font-outfit text-base font-bold leading-tight text-slate-950" x-text="upcomingModeLabel"></p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 p-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-700">Long Break</p>
                                    <p class="mt-1 font-outfit text-base font-bold leading-tight text-slate-950" x-text="`${sessionsUntilLongBreak} sesi`"></p>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-[1.75rem] border border-sky-200 bg-white/85 p-3 shadow-[0_16px_40px_rgba(14,116,144,0.08)]">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-sky-700">Pengaturan</p>
                                <h3 class="mt-1 font-outfit text-lg font-extrabold text-slate-950">Durasi Sesi</h3>
                                <p class="text-xs text-slate-500">Sesuaikan durasi setiap mode belajar.</p>
                            </div>

                            <div class="mt-2 space-y-1.5">
                                <template x-for="field in [
                                    { key: 'focus', label: 'Fokus', min: 10, max: 90 },
                                    { key: 'shortBreak', label: 'Istirahat Pendek', min: 1, max: 30 },
                                    { key: 'longBreak', label: 'Istirahat Panjang', min: 5, max: 60 }
                                ]" :key="field.key">
                                    <label class="flex items-center justify-between gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 px-3 py-1.5">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900" x-text="field.label"></p>
                                            <p class="text-[11px] text-slate-400" x-text="`${field.min}-${field.max} menit`"></p>
                                        </div>
                                        <div class="flex w-24 items-center gap-2 rounded-xl border border-sky-200 bg-white px-3 py-1">
                                            <input
                                                type="number"
                                                :min="field.min"
                                                :max="field.max"
                                                x-model.number="durations[field.key]"
                                                @change="updateDuration(field.key, durations[field.key])"
                                                class="pomodoro-input w-full border-0 bg-transparent text-right font-bold text-slate-950 outline-none focus:outline-none focus:ring-0"
                                            >
                                            <span class="text-xs font-bold text-slate-400">m</span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </section>
                    </aside>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
