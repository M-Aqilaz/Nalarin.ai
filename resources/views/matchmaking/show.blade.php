<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">Study Match</h2>
            <p class="text-sm text-gray-400 mt-1">Topik: {{ $match->topic }}</p>
        </div>
    </x-slot>
    @php($partner = $match->partnerFor(auth()->user()))
    <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_0.7fr] gap-6">
        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 space-y-4 order-2 lg:order-1">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            <div class="max-h-[28rem] overflow-y-auto space-y-4 pr-1">
                @foreach ($match->messages->reverse() as $message)
                    <div class="{{ $message->user_id === auth()->id() ? 'ml-auto bg-purple-600/20 border-purple-500/20' : 'mr-auto bg-white/5 border-white/10' }} max-w-full md:max-w-3xl rounded-2xl border p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $message->user->name }}</p>
                        <p class="text-sm leading-7 text-gray-200 whitespace-pre-line break-words">{{ $message->content }}</p>
                    </div>
                @endforeach
            </div>
            <form action="{{ route('matches.messages.store', $match) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="content" rows="4" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required></textarea>
                <button class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-white font-medium">Kirim</button>
            </form>
        </section>
        <aside class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 space-y-4 order-1 lg:order-2">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-purple-300">Partner</p>
                <h3 class="font-outfit text-xl font-semibold text-white mt-2">{{ $partner?->name ?? 'Partner tidak tersedia' }}</h3>
                <p class="text-sm text-gray-400 mt-1">{{ $partner?->studyProfile?->primary_subject ?? 'Belum isi mapel utama' }}</p>
                <p class="text-sm text-gray-500 mt-3 break-words">{{ $partner?->studyProfile?->bio }}</p>
            </div>
            <form method="POST" action="{{ route('matches.end', $match) }}">@csrf<button class="w-full rounded-xl bg-white/10 px-4 py-3 text-sm text-white">Akhiri Sesi</button></form>
            <form method="POST" action="{{ route('matches.block', $match) }}">@csrf<button class="w-full rounded-xl bg-red-500/10 px-4 py-3 text-sm text-red-200">Block Partner</button></form>
            <form method="POST" action="{{ route('matches.report', $match) }}" class="space-y-3">
                @csrf
                <textarea name="reason" rows="4" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" placeholder="Alasan report" required></textarea>
                <button class="w-full rounded-xl bg-amber-500/10 px-4 py-3 text-sm text-amber-100">Laporkan</button>
            </form>
        </aside>
    </div>
</x-app-layout>
