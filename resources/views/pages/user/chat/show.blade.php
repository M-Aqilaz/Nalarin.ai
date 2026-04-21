<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-outfit font-bold text-2xl text-white leading-tight">{{ $thread->title }}</h2>
            <p class="text-sm text-gray-400 mt-1">{{ $thread->material?->title ?? 'Thread umum tanpa materi' }}</p>
        </div>
    </x-slot>
    <div
        x-data="threadChat({
            initialMessages: {{ \Illuminate\Support\Js::from(
                $thread->messages
                    ->map(fn ($message) => \App\Support\RealtimePayloads::threadMessage($message))
                    ->values()
            ) }},
            sendUrl: '{{ route('chat.messages.store', $thread) }}',
            pollUrl: '{{ route('chat.messages.index', $thread) }}',
            channelName: 'thread.{{ $thread->id }}',
            thread: {{ \Illuminate\Support\Js::from(\App\Support\RealtimePayloads::threadStatus($thread)) }},
        })"
        class="space-y-6"
    >
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        <div x-cloak x-show="hasAiNotice" :class="aiStatusClasses" class="rounded-2xl border p-4 text-sm">
            <p x-text="aiStatusText"></p>
        </div>

        <section class="glass-panel rounded-2xl border border-white/5 p-4 md:p-6 space-y-4">
            <div class="rounded-2xl border border-white/5 bg-gray-950/40 px-4 py-3 text-xs text-gray-400">
                <span x-text="connectionState"></span>
            </div>

            <div x-ref="messageList" class="max-h-[30rem] overflow-y-auto space-y-4 pr-1">
                <div x-show="!booted" class="space-y-4">
                    @forelse ($thread->messages as $message)
                        <div class="{{ $message->role === 'user' ? 'ml-auto bg-purple-600/20 border-purple-500/20' : 'mr-auto bg-white/5 border-white/10' }} max-w-full md:max-w-3xl rounded-2xl border p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $message->role }}</p>
                            <p class="text-sm leading-7 text-gray-200 whitespace-pre-line break-words">{{ $message->content }}</p>
                        </div>
                    @empty
                        <div class="text-sm text-gray-400">Belum ada pesan di thread ini.</div>
                    @endforelse
                </div>

                <div x-cloak x-show="booted" class="space-y-4">
                    <template x-if="messages.length === 0">
                        <div class="text-sm text-gray-400">Belum ada pesan di thread ini.</div>
                    </template>

                    <template x-for="message in messages" :key="message.id">
                        <div :class="`${bubbleClasses(message)} max-w-full md:max-w-3xl rounded-2xl border p-4`">
                            <p class="text-xs uppercase tracking-wide text-gray-400 mb-2" x-text="roleLabel(message)"></p>
                            <p class="text-sm leading-7 text-gray-200 whitespace-pre-line break-words" x-text="message.content"></p>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <section class="glass-panel rounded-2xl border border-white/5 p-5 md:p-6">
            <form action="{{ route('chat.messages.store', $thread) }}" method="POST" class="space-y-4" @submit.prevent="submitMessage">
                @csrf
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Pesan Baru</label>
                    <textarea x-model="form.content" name="content" rows="5" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white" required></textarea>
                </div>
                <div x-cloak x-show="error" class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200" x-text="error"></div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3">
                    <a href="{{ route('feature.chat') }}" class="text-sm text-gray-400">Kembali ke daftar thread</a>
                    <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-white font-medium disabled:opacity-60" :disabled="isSubmitting">
                        <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim'"></span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
