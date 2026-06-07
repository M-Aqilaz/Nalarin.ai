<aside class="z-20 hidden h-full shrink-0 flex-col border-r border-sky-200/80 bg-sky-100/70 text-slate-950 shadow-[10px_0_35px_rgba(14,116,144,0.08)] backdrop-blur-2xl transition-[width] duration-200 md:flex" :class="sidebarCollapsed ? 'w-20' : 'w-64'">
    <div class="relative flex h-20 items-center border-b border-sky-200/80 px-4" :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between'">
        <a href="{{ url('/') }}" class="flex min-w-0 items-center">
            <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-11 w-auto object-contain transition-all" :class="sidebarCollapsed ? 'max-w-[42px]' : 'max-w-[170px]'" alt="Nalarin.ai Logo">
        </a>
        <button type="button" class="hidden shrink-0 items-center justify-center border border-sky-200 bg-white/90 text-slate-700 shadow-sm transition hover:bg-white md:inline-flex" :class="sidebarCollapsed ? 'absolute -right-3 top-6 h-7 w-7 rounded-full' : 'h-8 w-8 rounded-xl'" @click="toggleSidebar" :aria-label="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'">
            <svg class="h-4 w-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 19l-7-7 7-7"></path></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-5" :class="sidebarCollapsed ? 'px-3' : 'px-4'">
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" title="Dashboard" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('dashboard') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="dashboard" class="h-4 w-4" /></span>
                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>
        </div>

        <div class="mt-6">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">AI Learning</p>
            <div class="mt-4 space-y-2">
                <a href="{{ route('feature.upload') }}" title="Unggah Materi" data-feature="Unggah Materi" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.upload') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="upload" class="h-4 w-4" /></span>
                    <span x-show="!sidebarCollapsed">Unggah Materi</span>
                </a>
                <a href="{{ route('feature.summary') }}" title="Ringkasan" data-feature="Ringkasan Otomatis" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.summary', 'summaries.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="summary" class="h-4 w-4" /></span>
                    <span x-show="!sidebarCollapsed">Ringkasan</span>
                </a>
                <a href="{{ route('feature.chat') }}" title="AI Tutor" data-feature="AI Tutor Khusus" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.chat', 'chat.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="tutor" class="h-4 w-4" /></span>
                    <span x-show="!sidebarCollapsed">AI Tutor</span>
                </a>
                <a href="{{ route('feature.flashcards') }}" title="Flashcards" data-feature="Smart Flashcards" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.flashcards', 'flashcards.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="flashcards" class="h-4 w-4" /></span>
                    <span x-show="!sidebarCollapsed">Flashcards</span>
                </a>
            </div>
        </div>

        <div class="mt-6 space-y-1">
            <a href="{{ route('feature.quiz') }}" title="Kuis" data-feature="Latihan Kuis" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.quiz') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="quiz" class="h-4 w-4" /></span>
                <span x-show="!sidebarCollapsed">Kuis</span>
            </a>
            <a href="{{ route('billing.index') }}" title="Billing" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('billing.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm"><x-feature-icon name="billing" class="h-4 w-4" /></span>
                <span x-show="!sidebarCollapsed">Billing</span>
            </a>
        </div>

        <div class="mt-8">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">Focus Section</p>
            <div class="mt-4 space-y-2">
                <a href="{{ route('feature.pomodoro') }}" title="Pomodoro" data-feature="Pomodoro Timer" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.pomodoro') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center text-orange-500"><x-feature-icon name="pomodoro" class="h-5 w-5" /></span>
                    <span x-show="!sidebarCollapsed">Pomodoro</span>
                </a>
                <a href="{{ route('feature.focus-planner') }}" title="Focus Planner" data-feature="Focus Planner" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.focus-planner') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center text-amber-500"><x-feature-icon name="planner" class="h-5 w-5" /></span>
                    <span x-show="!sidebarCollapsed">Focus Planner</span>
                </a>
                <a href="{{ route('feature.focus-insights') }}" title="Focus Insights" data-feature="Focus Insights" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.focus-insights') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center text-indigo-500"><x-feature-icon name="insights" class="h-5 w-5" /></span>
                    <span x-show="!sidebarCollapsed">Focus Insights</span>
                </a>
               
            </div>
        </div>

        <div class="mt-8">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">Social Learning</p>
            <div class="mt-4 space-y-2">

                <a href="{{ route('matchmaking.roulette') }}" title="Study Matching" data-feature="Study Matching" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center text-rose-500"><x-feature-icon name="matching" class="h-5 w-5" /></span>
                    <span x-show="!sidebarCollapsed">Study Matching</span>
                </a>
                <a href="{{ route('rooms.index') }}" title="Group Chat Kelas" data-feature="Group Chat Kelas" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('rooms.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center text-violet-500"><x-feature-icon name="room" class="h-5 w-5" /></span>
                    <span x-show="!sidebarCollapsed">Group Chat Kelas</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="border-t border-sky-200/80 p-3" :class="sidebarCollapsed ? 'px-3' : 'p-3'">
        @if (Auth::user()->plan === 'free')
            <a href="{{ route('pricing') }}" x-show="!sidebarCollapsed" class="block rounded-[18px] border border-cyan-200 bg-gradient-to-br from-white via-sky-50 to-cyan-50 p-3 shadow-sm transition duration-200 hover:bg-white hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-cyan-700">Free Plan</p>
                        <p class="mt-1 truncate text-sm font-extrabold leading-5 text-slate-950">Upgrade to Pro</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-sky-100 px-2.5 py-1 text-[11px] font-bold text-sky-700">Pro</span>
                </div>
                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-600">Kuota lebih lega untuk room, match, dan fitur premium.</p>
                <span class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-sky-500 px-3 py-2 text-xs font-bold text-white shadow-sm shadow-sky-500/20">Upgrade</span>
            </a>
        @endif
        <details class="group relative z-40 mt-2 flex flex-col-reverse">
            <summary title="Profil" style="list-style: none;" class="flex w-full cursor-pointer items-center gap-3 rounded-[18px] border border-sky-200/80 bg-sky-50/80 p-3 text-left shadow-sm transition duration-200 hover:bg-sky-100/80 [&::-webkit-details-marker]:hidden" :class="sidebarCollapsed ? 'justify-center p-2.5' : ''">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-500 text-sm font-extrabold text-white shadow-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <span x-show="!sidebarCollapsed" class="min-w-0 flex-1">
                    <span class="block truncate text-[15px] font-bold leading-5 text-slate-950">{{ Auth::user()->name }}</span>
                    <span class="mt-1 block truncate text-[13px] leading-5 text-slate-600">{{ Auth::user()->email }}</span>
                    <span class="mt-2 inline-flex max-w-full items-center rounded-full border border-sky-200 bg-white/80 px-2 py-0.5 text-[10px] font-semibold uppercase leading-4 tracking-wide text-slate-700">
                        Plan {{ Auth::user()->plan }} | Match {{ Auth::user()->match_credits }}
                    </span>
                </span>
                <svg x-show="!sidebarCollapsed" class="h-3.5 w-3.5 shrink-0 text-slate-500 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m6 9 6 6 6-6"></path></svg>
            </summary>
            <div class="mb-2 grid gap-2 rounded-[18px] border border-sky-200 bg-white/95 p-2 shadow-lg shadow-sky-900/10 backdrop-blur">
                <a href="{{ route('profile.edit') }}" class="flex w-full items-center justify-center rounded-xl border border-sky-200 bg-sky-50/70 px-3 py-2 text-xs font-bold text-slate-700 transition duration-150 hover:bg-sky-100">
                    {{-- <span x-show="sidebarCollapsed">Profil</span> --}}
                    <span x-show="!sidebarCollapsed">Lihat Profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="flex w-full items-center justify-center rounded-xl border border-red-100 bg-red-50/70 px-3 py-2 text-xs font-bold text-red-600 transition duration-150 hover:border-red-200 hover:bg-red-100">
                        Keluar
                    </button>
                </form>
            </div>
        </details>
    </div>
</aside>
