<aside class="z-20 hidden h-full shrink-0 flex-col border-r border-sky-200/80 bg-sky-100/70 text-slate-950 shadow-[10px_0_35px_rgba(14,116,144,0.08)] backdrop-blur-2xl transition-[width] duration-200 md:flex" :class="sidebarCollapsed ? 'w-20' : 'w-64'">
    <div class="relative flex h-20 items-center border-b border-sky-200/80 px-4" :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between'">
        <a href="{{ url('/') }}" class="flex min-w-0 items-center">
            <img src="{{ asset('images/nalarin_ai_logo_new.png') }}" class="h-11 w-auto object-contain transition-all" :class="sidebarCollapsed ? 'max-w-[42px]' : 'max-w-[170px]'" alt="Nalarin.ai Logo">
        </a>
        <button type="button" class="hidden shrink-0 items-center justify-center border border-sky-200 bg-white/90 text-slate-700 shadow-sm transition hover:bg-white md:inline-flex" :class="sidebarCollapsed ? 'absolute -right-3 top-6 h-7 w-7 rounded-full' : 'h-8 w-8 rounded-xl'" @click="toggleSidebar" :aria-label="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'">
            <svg class="h-4 w-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 19l-7-7 7-7"></path></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Dasbor</a>
        <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">AI Learning</p></div>
        <a href="{{ route('feature.upload') }}" data-feature="Unggah Materi" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.upload') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Unggah Materi</a>
        <a href="{{ route('feature.summary') }}" data-feature="Ringkasan Otomatis" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.summary') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Ringkasan</a>
        <a href="{{ route('feature.chat') }}" data-feature="Tutor AI Khusus" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.chat') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Tutor AI</a>
        <a href="{{ route('feature.flashcards') }}" data-feature="Smart Flashcards" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.flashcards') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}"><em>Flashcards</em></a>
        <a href="{{ route('feature.quiz') }}" data-feature="Latihan Kuis" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.quiz') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Kuis</a>
        <a href="{{ route('feature.pomodoro') }}" data-feature="Pomodoro Timer" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.pomodoro') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Pomodoro</a>
        <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Social Learning</p></div>
        <a href="{{ route('rooms.index') }}" data-feature="Obrolan Grup" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Obrolan Grup</a>
        <a href="{{ route('matchmaking.index') }}" data-feature="Study Matching" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}"><em>Study Matching</em></a>
        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('notifications.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
            <span>Notifikasi</span>
            @if ($unreadNotificationCount > 0)
                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
            @endif
        </a>
        <a href="{{ route('pricing') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('pricing') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Harga</a>
    </nav>

    <div class="p-4 border-t border-white/5">
        <div class="space-y-3">
            <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                <p class="text-xs font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                <p class="text-[10px] text-purple-300 mt-2 uppercase">Plan {{ Auth::user()->plan }} | Match {{ Auth::user()->match_credits }}</p>
            </div>
            @if (Auth::user()->plan === 'free')
                <a href="{{ route('pricing') }}" class="block rounded-xl border border-amber-500/20 bg-amber-500/10 p-3 hover:bg-amber-500/15 transition-all">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-amber-200">gratis</p>
                    <p class="mt-2 text-sm font-semibold text-white">Tingkatkan ke premium</p>
                    <p class="mt-1 text-xs text-amber-100/80">Buka batas ruang kelas lebih besar, kuota match lebih banyak, dan akses fitur premium.</p>
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Profil Saya</a>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-xs text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Dasbor Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all">Keluar</button>
            </form>
        </div>
    </div>
</aside>
