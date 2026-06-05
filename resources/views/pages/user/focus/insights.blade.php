<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-violet-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M7 16V9m5 7V5m5 11v-4"></path>
                </svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-violet-100/90">Focus Section</p>
                <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text">Focus Insights</h2>
            </div>
        </div>
    </x-slot>

    <div x-data="focusInsights()" x-init="init()" class="readable-study-page space-y-6">
        <section class="feature-hero overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-56 rounded-full bg-violet-400/15 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="user-kicker text-violet-100/90">Progress Analytics</p>
                    <h1 class="mt-3 font-outfit text-3xl font-semibold tracking-tight text-white md:text-4xl">
                        Baca ritme fokus dari planner dan Pomodoro.
                    </h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-200/80">
                        Saya sarankan insight difokuskan ke sinyal yang benar-benar berguna: berapa sesi yang selesai, berapa task yang masih tertinggal, dan apakah blok belajar hari ini sudah cukup padat.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="rounded-full border border-white/10 bg-white/[0.05] px-4 py-2 text-xs font-medium text-slate-200">
                        Sinkron terakhir <span x-text="lastSyncLabel"></span>
                    </span>
                    <button type="button" @click="refresh()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-5 text-sm font-medium text-slate-100 transition hover:bg-white/[0.06]">
                        Refresh
                    </button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Focus score</p>
                <p class="mt-3 font-outfit text-4xl text-white" x-text="focusScore"></p>
                <p class="mt-2 text-sm text-slate-400" x-text="focusScoreLabel"></p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Sesi selesai</p>
                <p class="mt-3 font-outfit text-4xl text-white" x-text="completedFocusSessions"></p>
                <p class="mt-2 text-sm text-slate-400">Target harian <span x-text="cycleTarget"></span> sesi</p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Task selesai</p>
                <p class="mt-3 font-outfit text-4xl text-white"><span x-text="completedTaskCount"></span>/<span x-text="taskCount"></span></p>
                <p class="mt-2 text-sm text-slate-400"><span x-text="taskCompletionPercent"></span>% task planner tuntas</p>
            </div>
            <div class="glass-panel-strong rounded-[28px] p-5">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Blok selesai</p>
                <p class="mt-3 font-outfit text-4xl text-white"><span x-text="completedBlockCount"></span>/<span x-text="blockCount"></span></p>
                <p class="mt-2 text-sm text-slate-400"><span x-text="blockCompletionPercent"></span>% eksekusi blok</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
            <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="user-kicker">Focus Health</p>
                        <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">Ringkasan performa hari ini</h3>
                    </div>
                    <span class="rounded-full border border-violet-400/20 bg-violet-400/10 px-4 py-2 text-xs font-medium text-violet-100">
                        Target harian <span x-text="progressPercent"></span>%
                    </span>
                </div>

                <div class="mt-6 grid gap-4">
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Target Pomodoro</span>
                            <span><span x-text="progressPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(167,139,250,0.95),rgba(34,211,238,0.95))] transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Task Completion</span>
                            <span><span x-text="taskCompletionPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(34,211,238,0.95),rgba(59,130,246,0.95))] transition-all duration-300" :style="`width: ${taskCompletionPercent}%`"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Block Execution</span>
                            <span><span x-text="blockCompletionPercent"></span>%</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(16,185,129,0.95),rgba(34,211,238,0.95))] transition-all duration-300" :style="`width: ${blockCompletionPercent}%`"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Rekomendasi</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200" x-text="recommendation"></p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Status planner</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200">
                            <span x-show="taskCount || blockCount">Planner aktif dengan <span x-text="taskCount"></span> task dan <span x-text="blockCount"></span> blok fokus.</span>
                            <span x-show="!taskCount && !blockCount">Belum ada data planner. Susun dulu dari halaman Focus Planner.</span>
                        </p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Coach note</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200" x-text="coachMessage"></p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Strongest mode</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200"><span class="uppercase" x-text="strongestMode"></span> paling sering dipakai di planner.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">Deep Breakdown</p>
                    <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">Pembacaan ritme fokus</h3>
                    <div class="mt-5 space-y-3">
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">Planned vs done</p>
                            <p class="mt-2 text-sm text-slate-400"><span x-text="completedTaskSessions"></span> dari <span x-text="totalPlannedSessions"></span> estimasi sesi task sudah benar-benar selesai.</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">Focused minutes</p>
                            <p class="mt-2 text-sm text-slate-400"><span x-text="completedMinutes"></span> menit fokus tereksekusi dari <span x-text="plannedMinutes"></span> menit yang direncanakan.</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-sm font-medium text-white">Interpretasi cepat</p>
                            <p class="mt-2 text-sm text-slate-400">Kalau block execution lebih tinggi dari task completion, berarti kamu sibuk belajar tapi output task belum cukup jelas.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">Next Move</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white">Langkah paling masuk akal sekarang</h3>
                    <p class="mt-4 text-sm leading-6 text-slate-300/85">
                        Dari sisi UX, insight sekarang sudah jauh lebih berguna. Langkah berikut yang paling bernilai adalah streak mingguan, perbandingan hari ke hari, dan rekomendasi AI berdasarkan materi yang sedang aktif dipelajari.
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
