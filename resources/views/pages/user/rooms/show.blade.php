<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">{{ $room->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">{{ $room->topic }} | {{ $room->visibility }} | {{ $room->members->count() }} anggota</p>
        </div>
    </x-slot>

    <div
        x-data="roomChat({
            initialMessages: {{ \Illuminate\Support\Js::from(
                $messages
                    ->map(fn ($message) => \App\Support\RealtimePayloads::roomMessage($message))
                    ->values()
            ) }},
            sendUrl: '{{ route('rooms.messages.store', $room) }}',
            pollUrl: '{{ route('rooms.messages.index', $room) }}',
            typingUrl: '{{ route('rooms.typing', $room) }}',
            channelName: 'room.{{ $room->id }}',
            currentUserId: {{ auth()->id() }},
            currentUserName: {{ \Illuminate\Support\Js::from(auth()->user()->name) }},
        })"
        class="grid grid-cols-1 lg:grid-cols-[1.4fr_0.6fr] gap-6"
    >
        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 space-y-4 order-2 lg:order-1">
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif

            <div class="rounded-2xl border border-white/5 bg-gray-950/40 px-4 py-3 text-xs text-gray-400">
                <span x-text="connectionState"></span>
            </div>

            <div x-ref="messageList" class="max-h-[28rem] overflow-y-auto space-y-4 pr-1">
                <div x-show="!booted" class="space-y-4">
                    @forelse ($messages as $message)
                        <div class="{{ $message->user_id === auth()->id() ? 'ml-auto bg-purple-600/20 border-purple-500/20' : 'mr-auto bg-white/5 border-white/10' }} max-w-full md:max-w-3xl rounded-2xl border p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $message->user->name }}</p>
                            <p class="text-sm leading-7 text-gray-200 whitespace-pre-line break-words">{{ $message->content }}</p>
                        </div>
                    @empty
                        <div class="text-sm text-gray-400">Belum ada pesan di room ini.</div>
                    @endforelse
                </div>

                <div x-cloak x-show="booted" class="space-y-4">
                    <template x-if="messages.length === 0">
                        <div class="text-sm text-gray-400">Belum ada pesan di room ini.</div>
                    </template>

                    <template x-for="message in messages" :key="message.id">
                        <div :class="`${bubbleClasses(message, currentUserId)} max-w-full md:max-w-3xl rounded-2xl border p-4`">
                            <p class="text-xs uppercase tracking-wide text-gray-400 mb-2" x-text="message.user_name"></p>
                            <p class="text-sm leading-7 text-gray-200 whitespace-pre-line break-words" x-text="message.content"></p>
                        </div>
                    </template>

                    <div x-cloak x-show="typingText" class="mr-auto max-w-full md:max-w-3xl rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Aktivitas</p>
                        <div class="flex items-center gap-3 text-sm text-gray-300">
                            <p class="leading-7" x-text="typingText"></p>
                            <span class="typing-dots" aria-hidden="true">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isMember)
                <form action="{{ route('rooms.messages.store', $room) }}" method="POST" class="space-y-4" @submit.prevent="submitMessage">
                    @csrf
                    <textarea x-model="form.content" @input="notifyTyping" name="content" rows="4" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required></textarea>
                    <div x-cloak x-show="error" class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200" x-text="error"></div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-xs text-gray-500">Kirim pesan ke seluruh anggota aktif room.</span>
                        <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-white font-medium disabled:opacity-60" :disabled="isSubmitting">
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim'"></span>
                        </button>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('rooms.join', $room) }}" class="space-y-4">
                    @csrf
                    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                        Kamu bisa membaca percakapan room publik ini, tetapi harus join dulu untuk ikut mengirim pesan.
                    </div>
                    <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-white font-medium">Gabung Room</button>
                </form>
            @endif
        </section>

        <aside class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6 order-1 lg:order-2">
            <div class="flex items-center justify-between gap-3">
                <h3 class="font-outfit text-lg font-semibold text-white">Anggota</h3>
                <form method="POST" action="{{ route('rooms.leave', $room) }}">@csrf<button class="text-sm text-red-300">Keluar</button></form>
            </div>
            <div class="mt-4 space-y-3 max-h-[28rem] overflow-y-auto">
                @foreach ($room->members as $member)
                    @if ($member->status === 'active')
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-white text-sm font-medium">{{ $member->user->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $member->role }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </aside>
    </div>
</x-app-layout>
