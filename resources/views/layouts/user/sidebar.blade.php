<aside class="w-64 glass-panel border-r border-white/5 flex flex-col h-full hidden md:flex shrink-0 z-20">
    <div class="h-20 flex items-center px-6 border-b border-white/5">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo_nalarin_ai.png') }}" class="w-8 h-8 object-contain" alt="Nalarin.ai Logo">
            <span class="font-outfit font-bold text-xl tracking-tight text-white gap-1 flex">Nalarin<span class="text-purple-400">.ai</span></span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Dashboard</a>
        <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">AI Learning</p></div>
        <a href="{{ route('feature.upload') }}" data-feature="Unggah Materi" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.upload') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Unggah Materi</a>
        <a href="{{ route('feature.summary') }}" data-feature="Ringkasan Otomatis" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.summary') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Ringkasan</a>
        <a href="{{ route('feature.chat') }}" data-feature="AI Tutor Khusus" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.chat') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">AI Tutor</a>
        <a href="{{ route('feature.flashcards') }}" data-feature="Smart Flashcards" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.flashcards') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Flashcards</a>
        <a href="{{ route('feature.quiz') }}" data-feature="Latihan Kuis" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.quiz') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Kuis</a>
        <a href="{{ route('feature.pomodoro') }}" data-feature="Pomodoro Timer" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('feature.pomodoro') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Pomodoro</a>
        <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Social Learning</p></div>
        <a href="{{ route('rooms.index') }}" data-feature="Group Chat Kelas" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Group Chat Kelas</a>
        <a href="{{ route('matchmaking.index') }}" data-feature="Study Matching" class="track-feature block px-3 py-2.5 rounded-xl {{ request()->routeIs('matchmaking.*', 'matches.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Study Matching</a>
        <a href="{{ route('pricing') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('pricing') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Pricing</a>
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
                    <p class="text-[10px] uppercase tracking-[0.2em] text-amber-200">Free Plan</p>
                    <p class="mt-2 text-sm font-semibold text-white">Upgrade to Pro</p>
                    <p class="mt-1 text-xs text-amber-100/80">Buka limit room lebih besar, quota match lebih banyak, dan akses fitur premium.</p>
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Profil Saya</a>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-xs text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Admin Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all">Keluar</button>
            </form>
        </div>
    </div>
</aside>
