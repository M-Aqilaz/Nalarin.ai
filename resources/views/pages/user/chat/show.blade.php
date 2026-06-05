<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-violet-200/90">AI Tutor Thread</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ $thread->title }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ $thread->material?->title ?? 'Thread umum tanpa materi' }}</p>
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
            nalaFaces: {
                happy: '{{ asset('images/nalaFaces/nala_mentahan-happy.png') }}',
                flat: '{{ asset('images/nalaFaces/nala_mentahan-flat.png') }}',
                angry: '{{ asset('images/nalaFaces/nala_mentahan-angry.png') }}',
                sad: '{{ asset('images/nalaFaces/nala_mentahan-sad.png') }}',
                cute: '{{ asset('images/nalaFaces/nala_mentahan-cute.png') }}',
                shy: '{{ asset('images/nalaFaces/nala_mentahan-shy.png') }}',
                silly: '{{ asset('images/nalaFaces/nala_mentahan-silly.png') }}',
                sorry: '{{ asset('images/nalaFaces/nala_mentahan-sorry.png') }}',
            },
        })"
        class="mx-auto max-w-7xl"
    >
        @if (session('status'))
            <div class="mb-4 rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        <div x-cloak x-show="hasAiNotice" :class="aiStatusClasses" class="mb-4 rounded-2xl border p-4 text-sm">
            <p x-text="aiStatusText"></p>
        </div>

        <section class="rounded-[2rem] border border-sky-200 bg-white/88 shadow-[0_24px_70px_rgba(14,116,144,0.16)] backdrop-blur">
            <div class="border-b border-sky-100 bg-sky-50/90 p-4 lg:hidden">
                <details class="rounded-[1.5rem] border border-sky-200 bg-white">
                    <summary class="cursor-pointer list-none px-4 py-3 text-sm font-extrabold text-slate-950">Thread dan chat baru</summary>
                    <div class="space-y-4 border-t border-sky-100 p-4">
                        @if ($errors->any())
                            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
                        @endif

                        <form action="{{ route('chat.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input name="title" type="text" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Judul thread baru" required>
                            <select name="material_id" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                                <option value="">Tanpa materi</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->title }}</option>
                                @endforeach
                            </select>
                            <textarea name="opening_message" rows="3" class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Pesan pembuka opsional"></textarea>
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-4 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">Buat Thread</button>
                        </form>

                        <div class="max-h-64 space-y-2 overflow-y-auto">
                            @foreach ($threads as $item)
                                <article class="relative rounded-2xl border px-4 py-3 transition {{ $item->id === $thread->id ? 'border-sky-300 bg-sky-100' : 'border-sky-100 bg-white hover:bg-sky-50' }}" @contextmenu.prevent="openThreadMenu({{ $item->id }})">
                                    <a href="{{ route('chat.show', $item) }}" class="block" @click="closeThreadMenu">
                                        <p class="truncate text-sm font-bold text-slate-950" @if ($item->id === $thread->id) x-text="threadTitle" @endif>{{ $item->title }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $item->material?->title ?? 'Tanpa materi' }} | {{ $item->messages_count }} pesan</p>
                                    </a>
                                    <div x-cloak x-show="threadContextMenu.open && threadContextMenu.id === {{ $item->id }}" @click.outside="closeThreadMenu" class="absolute right-3 top-3 z-30 w-64 rounded-2xl border border-sky-200 bg-white p-2 shadow-[0_18px_42px_rgba(14,116,144,0.18)]">
                                        <details>
                                            <summary class="cursor-pointer list-none rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-sky-50">Edit thread</summary>
                                            <form action="{{ route('chat.update', $item) }}" method="POST" class="mt-2 space-y-2 rounded-2xl border border-sky-100 bg-sky-50/70 p-3">
                                                @csrf
                                                @method('PATCH')
                                                <input name="title" type="text" value="{{ $item->title }}" class="w-full rounded-xl border border-sky-200 px-3 py-2 text-xs text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
                                                <select name="material_id" class="w-full rounded-xl border border-sky-200 px-3 py-2 text-xs text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                                                    <option value="">Tanpa materi</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}" @selected($item->material_id === $material->id)>{{ $material->title }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="w-full rounded-xl bg-sky-500 px-3 py-2 text-xs font-extrabold text-white">Simpan</button>
                                            </form>
                                        </details>
                                        <form action="{{ route('chat.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus thread ini beserta seluruh percakapannya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="mt-1 w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-red-700 transition hover:bg-red-50">Hapus thread</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </details>
            </div>

            <div class="grid min-h-[calc(100vh-13rem)] items-start lg:grid-cols-[280px_minmax(0,1fr)] xl:grid-cols-[280px_minmax(0,1fr)_300px]">
                <aside class="hidden border-r border-sky-100 bg-sky-50/92 lg:sticky lg:top-4 lg:flex lg:max-h-[calc(100vh-2rem)] lg:self-start lg:flex-col lg:overflow-hidden">
                    <div class="border-b border-sky-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Nala Chat</p>
                                <h3 class="mt-1 font-outfit text-xl font-extrabold text-slate-950">Thread</h3>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600">{{ $threads->count() }}</span>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto p-3">
                        <div class="space-y-2">
                            @foreach ($threads as $item)
                                <article class="group relative rounded-2xl border px-4 py-3 transition {{ $item->id === $thread->id ? 'border-sky-300 bg-white shadow-sm' : 'border-transparent hover:border-sky-200 hover:bg-white/80' }}" @contextmenu.prevent="openThreadMenu({{ $item->id }})">
                                    <a href="{{ route('chat.show', $item) }}" class="block" @click="closeThreadMenu">
                                        <p class="truncate text-sm font-bold text-slate-950" @if ($item->id === $thread->id) x-text="threadTitle" @endif>{{ $item->title }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $item->material?->title ?? 'Tanpa materi' }}</p>
                                        <p class="mt-2 text-[11px] font-semibold text-slate-400">{{ $item->messages_count }} pesan</p>
                                    </a>
                                    <div x-cloak x-show="threadContextMenu.open && threadContextMenu.id === {{ $item->id }}" @click.outside="closeThreadMenu" class="absolute right-3 top-3 z-30 w-64 rounded-2xl border border-sky-200 bg-white p-2 shadow-[0_18px_42px_rgba(14,116,144,0.18)]">
                                        <details>
                                            <summary class="cursor-pointer list-none rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-sky-50">Edit thread</summary>
                                            <form action="{{ route('chat.update', $item) }}" method="POST" class="mt-2 space-y-2 rounded-2xl border border-sky-100 bg-sky-50/70 p-3 shadow-sm">
                                                @csrf
                                                @method('PATCH')
                                                <input name="title" type="text" value="{{ $item->title }}" class="w-full rounded-xl border border-sky-200 px-3 py-2 text-xs text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
                                                <select name="material_id" class="w-full rounded-xl border border-sky-200 px-3 py-2 text-xs text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                                                    <option value="">Tanpa materi</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}" @selected($item->material_id === $material->id)>{{ $material->title }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="w-full rounded-xl bg-sky-500 px-3 py-2 text-xs font-extrabold text-white transition hover:bg-sky-600">Simpan</button>
                                            </form>
                                        </details>
                                        <form action="{{ route('chat.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus thread ini beserta seluruh percakapannya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="mt-1 w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-red-700 transition hover:bg-red-50">Hapus thread</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-sky-100 p-4">
                        <details class="rounded-[1.35rem] border border-sky-200 bg-white">
                            <summary class="cursor-pointer list-none px-4 py-3 text-sm font-extrabold text-slate-950">Thread Baru</summary>
                            <div class="space-y-3 border-t border-sky-100 p-4">
                                @if ($errors->any())
                                    <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-3 text-xs text-red-700">{{ $errors->first() }}</div>
                                @endif

                                <form action="{{ route('chat.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input name="title" type="text" class="w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Judul thread" required>
                                    <select name="material_id" class="w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                                        <option value="">Tanpa materi</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->title }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="opening_message" rows="3" class="w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-sm text-slate-950 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Pesan pembuka"></textarea>
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-4 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600">Buat</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </aside>

                <main class="flex min-h-[calc(100vh-13rem)] flex-col border-sky-100 lg:border-r">
                    <header class="sticky top-0 z-20 border-b border-sky-100 bg-white/92 px-4 py-3 backdrop-blur md:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-sky-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-sky-700">Nala Chat</span>
                                    <span class="rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600" x-text="connectionState"></span>
                                </div>
                                <h3 class="mt-2 truncate font-outfit text-lg font-extrabold text-slate-950 md:text-xl" x-text="threadTitle">{{ $thread->title }}</h3>
                            </div>

                            <form action="{{ route('chat.store') }}" method="POST" class="hidden sm:block">
                                @csrf
                                <input type="hidden" name="title" value="Thread Baru">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-sky-50">
                                    Thread Otomatis
                                </button>
                            </form>
                        </div>
                    </header>

                    <div x-ref="messageList" class="min-h-0 flex-1 overflow-y-auto scroll-smooth bg-gradient-to-b from-sky-50/75 via-white to-white px-4 py-6 md:px-8">
                        <div class="mx-auto flex max-w-4xl flex-col gap-6">
                            <div x-show="!booted" class="flex flex-col gap-6">
                                @forelse ($thread->messages as $message)
                                    <article class="{{ $message->role === 'user' ? 'items-end' : 'items-start' }} flex flex-col gap-2">
                                        <div class="{{ $message->role === 'user' ? 'max-w-[82%] rounded-[1.35rem] rounded-br-md border-slate-950 bg-slate-950 px-5 py-4 text-white shadow-lg shadow-slate-950/10' : 'max-w-[92%] rounded-[1.35rem] border border-sky-100 bg-white px-5 py-4 text-slate-900 shadow-sm' }}">
                                            <div class="mb-3 flex items-center gap-3">
                                                @if ($message->role === 'assistant')
                                                    <img src="{{ asset('images/nalaFaces/nala_mentahan-happy.png') }}" class="h-9 w-9 shrink-0 rounded-full bg-sky-50 object-contain ring-1 ring-sky-100" alt="Nala">
                                                @else
                                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-sm font-extrabold text-white ring-1 ring-white/15">A</span>
                                                @endif
                                                <p class="{{ $message->role === 'user' ? 'text-white/70' : 'text-slate-500' }} text-xs font-extrabold uppercase tracking-[0.16em]">{{ $message->role === 'assistant' ? 'Nala' : 'Anda' }}</p>
                                            </div>
                                            @if ($message->attachments->isNotEmpty())
                                                <div class="mb-3 grid gap-2">
                                                    @foreach ($message->attachments as $attachment)
                                                        @if ($attachment->kind === 'image')
                                                            <a href="{{ $attachment->url() }}" target="_blank" class="block overflow-hidden rounded-2xl border {{ $message->role === 'user' ? 'border-white/15 bg-white/10' : 'border-sky-100 bg-sky-50' }}">
                                                                <img src="{{ $attachment->url() }}" alt="{{ $attachment->original_name ?? 'Gambar chat' }}" class="max-h-72 w-full object-contain">
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            <p class="whitespace-pre-line break-words text-sm leading-7">{{ $message->content }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <div class="mx-auto max-w-xl py-16 text-center">
                                        <img src="{{ asset('images/nalaFaces/nala_mentahan-happy.png') }}" class="mx-auto h-32 w-32 object-contain" alt="Nala">
                                        <h3 class="mt-4 font-outfit text-2xl font-extrabold text-slate-950">Mulai obrolan dengan Nala</h3>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">Tanyakan konsep, minta contoh soal, atau suruh Nala menjelaskan materi dengan bahasa yang lebih mudah.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div x-cloak x-show="booted" class="flex flex-col gap-6">
                                <template x-if="messages.length === 0">
                                    <div class="mx-auto max-w-xl py-16 text-center">
                                        <img :src="nalaImage" class="mx-auto h-32 w-32 object-contain" alt="Nala">
                                        <h3 class="mt-4 font-outfit text-2xl font-extrabold text-slate-950">Mulai obrolan dengan Nala</h3>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">Tanyakan konsep, minta contoh soal, atau suruh Nala menjelaskan materi dengan bahasa yang lebih mudah.</p>
                                    </div>
                                </template>

                                <template x-for="message in messages" :key="message.id">
                                    <article :class="message.role === 'user' ? 'items-end' : 'items-start'" class="flex flex-col gap-2">
                                        <div :class="message.role === 'user' ? 'max-w-[82%] rounded-[1.35rem] rounded-br-md border-slate-950 bg-slate-950 px-5 py-4 text-white shadow-lg shadow-slate-950/10' : 'max-w-[92%] rounded-[1.35rem] border border-sky-100 bg-white px-5 py-4 text-slate-900 shadow-sm'">
                                            <div class="mb-3 flex items-center gap-3">
                                                <template x-if="message.role === 'assistant'">
                                                    <img :src="nalaImage" class="h-9 w-9 shrink-0 rounded-full bg-sky-50 object-contain ring-1 ring-sky-100" alt="Nala">
                                                </template>
                                                <template x-if="message.role !== 'assistant'">
                                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-sm font-extrabold text-white ring-1 ring-white/15">A</span>
                                                </template>
                                            <p :class="message.role === 'user' ? 'text-white/70' : 'text-slate-500'" class="text-xs font-extrabold uppercase tracking-[0.16em]" x-text="roleLabel(message)"></p>
                                        </div>
                                            <template x-if="message.attachments && message.attachments.length">
                                                <div class="mb-3 grid gap-2">
                                                    <template x-for="attachment in message.attachments" :key="attachment.id">
                                                        <template x-if="attachment.kind === 'image'">
                                                            <a :href="attachment.url" target="_blank" class="block overflow-hidden rounded-2xl border" :class="message.role === 'user' ? 'border-white/15 bg-white/10' : 'border-sky-100 bg-sky-50'">
                                                                <img :src="attachment.url" :alt="attachment.original_name || 'Gambar chat'" class="max-h-72 w-full object-contain">
                                                            </a>
                                                        </template>
                                                    </template>
                                                </div>
                                            </template>
                                            <p class="whitespace-pre-line break-words text-sm leading-7" x-text="message.content"></p>
                                        </div>
                                    </article>
                                </template>

                                <div x-cloak x-show="isAiWorking" class="flex items-start">
                                    <div class="max-w-[92%] rounded-[1.35rem] border border-sky-100 bg-white px-5 py-4 text-slate-900 shadow-sm">
                                        <div class="flex gap-3">
                                            <img :src="nalaImage" class="h-9 w-9 shrink-0 rounded-full bg-sky-50 object-contain ring-1 ring-sky-100" alt="Nala">
                                            <div>
                                                <p class="mb-2 text-xs font-extrabold uppercase tracking-[0.16em] text-slate-500">Nala</p>
                                                <div class="flex items-center gap-2 text-sm leading-7 text-slate-700">
                                                    <span>Nala sedang mengetik</span>
                                                    <span class="typing-dots" aria-hidden="true">
                                                        <span></span><span></span><span></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="sticky bottom-0 z-30 border-t border-sky-100 bg-white/94 p-4 shadow-[0_-18px_42px_rgba(14,116,144,0.10)] backdrop-blur md:p-5">
                        <form action="{{ route('chat.messages.store', $thread) }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-4xl" @submit.prevent="submitMessage">
                            @csrf
                            <div class="rounded-[1.6rem] border border-sky-200 bg-white p-3 shadow-[0_18px_42px_rgba(14,116,144,0.12)]">
                                <label class="sr-only">Pesan Baru</label>
                                <div x-cloak x-show="form.imageLoading" class="mx-3 mb-3 rounded-2xl border border-sky-100 bg-sky-50 p-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">Memuat gambar...</p>
                                            <p class="mt-1 text-xs text-slate-500">Nala sedang menyiapkan preview screenshot.</p>
                                        </div>
                                        <div class="h-9 w-9 shrink-0 rounded-full border border-sky-200 bg-white p-2">
                                            <div class="h-full w-full animate-spin rounded-full border-2 border-sky-200 border-t-sky-500"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-sky-100">
                                        <div class="indeterminate-loader h-full rounded-full bg-sky-500"></div>
                                    </div>
                                </div>
                                <div x-cloak x-show="form.imagePreviewUrl" class="mx-3 mb-3 flex items-center gap-3 rounded-2xl border border-sky-100 bg-sky-50 p-3">
                                    <img :src="form.imagePreviewUrl" alt="Preview gambar" class="h-20 w-20 shrink-0 rounded-xl bg-white object-cover ring-1 ring-sky-100">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold text-slate-900" x-text="form.imageName"></p>
                                        <p class="mt-1 text-xs text-slate-500">Gambar akan dikirim ke Nala untuk dianalisis.</p>
                                    </div>
                                    <button type="button" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-50" @click="clearImage">
                                        Hapus
                                    </button>
                                </div>
                                <textarea
                                    x-model="form.content"
                                    name="content"
                                    rows="1"
                                    class="min-h-[58px] max-h-40 w-full resize-none border-0 bg-transparent px-3 py-3 text-sm leading-6 text-slate-900 outline-none placeholder:text-slate-400 focus:border-0 focus:outline-none focus:ring-0"
                                    placeholder="Tanya Nala tentang materi ini, atau lampirkan gambar soal..."
                                    @input="notifyTyping"
                                    @paste="handlePaste"
                                    @keydown.enter.exact.prevent="submitMessage"
                                ></textarea>
                                <input x-ref="imageInput" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" @change="selectImage">

                                <div x-cloak x-show="error" class="mx-3 mb-3 rounded-2xl border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-700" x-text="error"></div>

                                <div class="flex flex-col gap-3 border-t border-sky-100 px-3 pt-3 sm:flex-row sm:items-center sm:justify-between">
                                    @unless (auth()->user()->isPremium())
                                        <p class="text-xs leading-5 text-slate-500">
                                            Free: {{ config('services.openai.limits.free_per_day', 10) }} pesan/hari, {{ config('services.openai.limits.free_per_minute', 4) }} pesan/menit.
                                        </p>
                                    @else
                                        <p class="text-xs leading-5 text-slate-500">Enter untuk kirim, Shift+Enter untuk baris baru.</p>
                                    @endunless

                                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                                        <button type="button" class="inline-flex h-11 items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 text-sm font-extrabold text-sky-700 transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-60" @click="$refs.imageInput.click()" :disabled="isSubmitting || isAiWorking">
                                            + Gambar
                                        </button>
                                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-sky-500 px-6 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="isSubmitting || isAiWorking">
                                            <span x-text="isSubmitting ? 'Mengirim...' : (isAiWorking ? 'Tunggu Nala...' : 'Kirim')"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </footer>
                </main>

                <aside class="hidden bg-gradient-to-b from-sky-50 via-cyan-50 to-white p-5 xl:sticky xl:top-4 xl:block xl:max-h-[calc(100vh-2rem)] xl:self-start xl:overflow-y-auto">
                    <div class="sticky top-5 space-y-4">
                        <section class="rounded-[1.75rem] border border-sky-200 bg-white/85 p-5 text-slate-950 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                            <div class="flex justify-center">
                                <div class="relative h-48 w-48 overflow-hidden rounded-[2rem] bg-gradient-to-br from-white to-sky-100 ring-1 ring-sky-100">
                                    <img :src="nalaImage" class="absolute inset-0 h-full w-full object-contain p-1" alt="Nala expression">
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">Nala</h3>
                                    <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700" x-text="nalaMoodLabel"></span>
                                </div>
                                <p class="mt-3 rounded-2xl border border-sky-100 bg-sky-50/80 px-4 py-3 text-left text-sm leading-6 text-slate-700" x-text="nalaLine"></p>
                            </div>
                        </section>

                        <section class="rounded-[1.75rem] border border-sky-200 bg-white/85 p-5 shadow-[0_18px_38px_rgba(14,116,144,0.1)]">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Voice</p>
                            <div class="mt-4 grid gap-2">
                                <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-50" @click="toggleNalaVoice" :disabled="!nalaVoiceSupported">
                                    <span x-text="nalaVoiceEnabled ? 'Matikan Suara' : 'Suara Nala'"></span>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white/70 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-50" @click="replayNalaVoice" :disabled="!nalaVoiceSupported">
                                    Replay Jawaban
                                </button>
                            </div>
                            <p class="mt-3 text-xs leading-5 text-slate-500" x-text="voiceStatusLabel"></p>
                        </section>

                        <section class="rounded-[1.75rem] border border-sky-200 bg-white/70 p-5">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">Konteks</p>
                            <p class="mt-3 text-sm font-bold text-slate-950">{{ $thread->material?->title ?? 'Tanpa materi khusus' }}</p>
                            <p class="mt-2 text-xs leading-5 text-slate-600">Jawaban Nala mengikuti konteks thread ini dan riwayat pesan yang sedang terbuka.</p>
                        </section>
                    </div>
                </aside>
            </div>

            <section class="border-t border-sky-100 bg-sky-50/80 p-4 lg:hidden">
                <div class="flex items-center gap-4">
                    <img :src="nalaImage" class="h-20 w-20 shrink-0 rounded-3xl bg-white object-contain ring-1 ring-sky-100" alt="Nala expression">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-outfit text-lg font-extrabold text-slate-950">Nala</p>
                            <span class="rounded-full border border-sky-200 bg-white px-3 py-1 text-xs font-bold text-sky-700" x-text="nalaMoodLabel"></span>
                        </div>
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-700" x-text="nalaLine"></p>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 disabled:cursor-not-allowed disabled:opacity-50" @click="toggleNalaVoice" :disabled="!nalaVoiceSupported">
                        <span x-text="nalaVoiceEnabled ? 'Matikan Suara' : 'Suara Nala'"></span>
                    </button>
                    <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 disabled:cursor-not-allowed disabled:opacity-50" @click="replayNalaVoice" :disabled="!nalaVoiceSupported">
                        Replay
                    </button>
                </div>
            </section>
        </section>
    </div>
</x-app-layout>
