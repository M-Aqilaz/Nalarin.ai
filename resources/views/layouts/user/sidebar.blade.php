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
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">&#8962;</span>
                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>
        </div>

        <div class="mt-6">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">AI Learning</p>
            <div class="mt-4 space-y-2">
                <a href="{{ route('feature.upload') }}" title="Unggah Materi" data-feature="Unggah Materi" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.upload') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">&#8679;</span>
                    <span x-show="!sidebarCollapsed">Unggah Materi</span>
                </a>
                <a href="{{ route('feature.summary') }}" title="Ringkasan" data-feature="Ringkasan Otomatis" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.summary', 'summaries.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">AI</span>
                    <span x-show="!sidebarCollapsed">Ringkasan</span>
                </a>
                <a href="{{ route('feature.chat') }}" title="AI Tutor" data-feature="AI Tutor Khusus" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.chat', 'chat.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">&#9993;</span>
                    <span x-show="!sidebarCollapsed">AI Tutor</span>
                </a>
                <a href="{{ route('feature.flashcards') }}" title="Flashcards" data-feature="Smart Flashcards" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.flashcards', 'flashcards.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">&#9635;</span>
                    <span x-show="!sidebarCollapsed">Flashcards</span>
                </a>
            </div>
        </div>

        <div class="mt-6 space-y-1">
            <a href="{{ route('feature.quiz') }}" title="Kuis" data-feature="Latihan Kuis" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.quiz') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-white text-sm shadow-sm">?</span>
                <span x-show="!sidebarCollapsed">Kuis</span>
            </a>
        </div>

        <div class="mt-8">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">Focus Section</p>
            <div class="mt-4 space-y-2">
                <a href="{{ route('feature.pomodoro') }}" title="Pomodoro" data-feature="Pomodoro Timer" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.pomodoro') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="text-xl">&#9716;</span>
                    <span x-show="!sidebarCollapsed">Pomodoro</span>
                </a>
                <a href="{{ route('feature.focus-planner') }}" title="Focus Planner" data-feature="Focus Planner" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.focus-planner') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="text-xl">&#9638;</span>
                    <span x-show="!sidebarCollapsed">Focus Planner</span>
                </a>
                <a href="{{ route('feature.focus-insights') }}" title="Focus Insights" data-feature="Focus Insights" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('feature.focus-insights') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="text-xl">&#9636;</span>
                    <span x-show="!sidebarCollapsed">Focus Insights</span>
                </a>
               
            </div>
        </div>

        <div class="mt-8">
            <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">Social Learning</p>
            <div class="mt-4 space-y-2">

                <a href="{{ route('matchmaking.roulette') }}" title="Study Matching" data-feature="Study Matching" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="text-xl text-rose-500">&#9825;</span>
                    <span x-show="!sidebarCollapsed">Study Matching</span>
                </a>
                <a href="{{ route('rooms.index') }}" title="Group Chat Kelas" data-feature="Group Chat Kelas" class="track-feature flex items-center gap-3 rounded-2xl px-3 py-3 text-slate-950 transition hover:bg-white/60 {{ request()->routeIs('rooms.*') ? 'bg-white/70 shadow-sm' : '' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <span class="text-xl">&#9993;</span>
                    <span x-show="!sidebarCollapsed">Group Chat Kelas</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="border-t border-sky-200/80 p-4" :class="sidebarCollapsed ? 'px-3' : 'p-4'">
        <a href="{{ route('profile.edit') }}" title="Profil" class="block rounded-2xl border border-sky-200 bg-white/55 p-4 shadow-sm transition hover:bg-white/75 hover:shadow-md" :class="sidebarCollapsed ? 'px-2 py-3 text-center' : ''">
            <span x-show="sidebarCollapsed" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-sky-500 text-sm font-extrabold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            <p x-show="!sidebarCollapsed" class="truncate text-sm font-extrabold text-slate-950">{{ Auth::user()->name }}</p>
            <p x-show="!sidebarCollapsed" class="mt-1 truncate text-xs text-slate-700">{{ Auth::user()->email }}</p>
            <p x-show="!sidebarCollapsed" class="mt-3 text-[11px] font-extrabold uppercase text-slate-950">Plan {{ Auth::user()->plan }} | Match {{ Auth::user()->match_credits }}</p>
        </a>
        @if (Auth::user()->plan === 'free')
            <a href="{{ route('pricing') }}" x-show="!sidebarCollapsed" class="mt-3 block rounded-2xl border border-cyan-300 bg-gradient-to-br from-white via-sky-50 to-fuchsia-100 p-4 shadow-sm transition hover:-translate-y-0.5">
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-950">Free Plan</p>
                <p class="mt-3 text-sm font-extrabold text-slate-950">Upgrade to Pro</p>
                <p class="mt-1 text-xs leading-5 text-slate-700">Buka limit room lebih besar, kuota match lebih banyak, dan akses fitur premium.</p>
                <span class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-sky-500 px-4 py-2 text-sm font-bold text-white shadow-md shadow-sky-500/20">Upgrade to Pro</span>
            </a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-sky-200/80 pt-4">
            @csrf
            <button type="submit" title="Keluar" class="flex w-full items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-extrabold text-red-700 transition hover:bg-red-100" :class="sidebarCollapsed ? 'px-0' : ''">
                <span x-show="sidebarCollapsed">X</span>
                <span x-show="!sidebarCollapsed">Keluar</span>
            </button>
        </form>
    </div>
</aside>
