<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-violet-200/90">{{ __('ui.chat_page_kicker') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ __('ui.chat_page_title') }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.chat_page_description') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-nala-guide
            mood="happy"
            :title="__('ui.chat_guide_title')"
            :message="__('ui.chat_guide_description')"
            compact
        />

        <section class="feature-hero">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-violet-100/90">{{ __('ui.chat_benefit_kicker') }}</p>
                <h3 class="mt-3 font-outfit text-2xl font-semibold text-white">{{ __('ui.chat_benefit_title') }}</h3>
                <p class="mt-3 text-sm text-slate-100/80">{{ __('ui.chat_benefit_description') }}</p>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="glass-panel accent-card-violet overflow-hidden rounded-[1.75rem]">
            <div class="border-b border-white/10 p-5">
                <h3 class="font-outfit text-lg font-semibold text-white">{{ __('ui.thread_list') }}</h3>
            </div>
            <div class="divide-y divide-white/10">
                @forelse ($threads as $thread)
                    <a href="{{ route('chat.show', $thread) }}" class="block p-4 transition hover:bg-white/[0.06]">
                        <p class="text-white font-medium">{{ $thread->title }}</p>
                        <p class="mt-1 text-sm text-slate-300/70">{{ $thread->material?->title ?? __('ui.without_material') }} | {{ __('ui.messages', ['count' => $thread->messages_count]) }}</p>
                    </a>
                @empty
                    <div class="p-4 text-sm text-slate-300/70">{{ __('ui.no_threads') }}</div>
                @endforelse
            </div>
        </section>

        <section class="glass-panel accent-card-cyan rounded-[1.75rem] p-5 md:p-6">
            <h3 class="font-outfit text-lg font-semibold text-white mb-5">{{ __('ui.create_thread') }}</h3>
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200 mb-4">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('chat.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm text-slate-200">{{ __('ui.thread_title') }}</label>
                    <input name="title" type="text" class="glass-input w-full px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-200">{{ __('ui.related_material') }}</label>
                    <select name="material_id" class="glass-input w-full px-4 py-3">
                        <option value="">{{ __('ui.without_material') }}</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->title }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-slate-300/75">{{ __('ui.chat_summary_context_hint') }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-200">{{ __('ui.opening_message') }}</label>
                    <textarea name="opening_message" rows="5" class="glass-input w-full px-4 py-3"></textarea>
                </div>
                <button type="submit" class="user-primary-button w-full py-3">{{ __('ui.create_thread') }}</button>
            </form>
        </section>
        </div>
    </div>
</x-app-layout>
