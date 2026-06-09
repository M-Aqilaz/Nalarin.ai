<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.room_collaborative_rooms') }}</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">{{ __('ui.room_group_chat') }}</h2>
            <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.room_page_description') }}</p>
        </div>
    </x-slot>
    <div class="space-y-6">
        <x-nala-guide
            mood="happy"
            :title="__('ui.room_guide_title')"
            :message="__('ui.room_guide_description')"
            compact
        />

        <section class="feature-hero">
            <div class="max-w-3xl">
                <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.room_study_together') }}</p>
                <h3 class="mt-3 font-outfit text-2xl font-semibold text-white">{{ __('ui.room_hero_title') }}</h3>
                <p class="mt-3 text-sm text-slate-100/80">{{ __('ui.room_hero_description') }}</p>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="glass-panel accent-card-cyan overflow-hidden rounded-[1.75rem]">
            <div class="border-b border-white/10 p-5"><h3 class="font-outfit text-lg font-semibold text-white">{{ __('ui.room_list') }}</h3></div>
            <div class="divide-y divide-white/10">
                @forelse ($rooms as $room)
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="min-w-0">
                                <a href="{{ route('rooms.show', $room) }}" class="text-white font-medium break-words">{{ $room->name }}</a>
                                <p class="mt-1 text-sm text-slate-300/70">{{ $room->topic }} | {{ __('ui.room_member_count', ['count' => $room->members_count]) }} | {{ __('ui.room_visibility_' . $room->visibility) }}</p>
                                <p class="mt-2 text-sm break-words text-slate-300/55">{{ $room->description }}</p>
                            </div>
                            <form method="POST" action="{{ route('rooms.join', $room) }}" class="sm:shrink-0">
                                @csrf
                                <button type="submit" class="user-primary-button w-full px-4 py-2.5 text-sm sm:w-auto">{{ __('ui.room_join') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-sm text-slate-300/70">{{ __('ui.room_empty') }}</div>
                @endforelse
            </div>
            <div class="p-4 border-t border-white/5">
                {{ $rooms->links() }}
            </div>
        </section>

        <section class="glass-panel accent-card-violet rounded-[1.75rem] p-5 md:p-6 space-y-6">
            <div>
                <h3 class="font-outfit text-lg font-semibold text-white">{{ __('ui.room_create_new') }}</h3>
                <p class="mt-1 text-sm text-slate-300/70">{{ __('ui.room_free_limit_description') }}</p>
            </div>
            @if (session('status'))
                <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('rooms.store') }}" method="POST" class="space-y-4">
                @csrf
                <input name="name" placeholder="{{ __('ui.room_name') }}" class="glass-input w-full px-4 py-3" required>
                <input name="topic" placeholder="{{ __('ui.room_main_topic') }}" class="glass-input w-full px-4 py-3" required>
                <textarea name="description" rows="4" placeholder="{{ __('ui.room_description') }}" class="glass-input w-full px-4 py-3"></textarea>
                <select name="visibility" class="glass-input w-full px-4 py-3"><option value="public">{{ __('ui.room_visibility_public') }}</option><option value="private">{{ __('ui.room_visibility_private') }}</option></select>
                <input name="max_members" type="number" min="5" max="100" value="30" class="glass-input w-full px-4 py-3">
                <button type="submit" class="user-primary-button w-full py-3">{{ __('ui.room_create') }}</button>
            </form>
        </section>
        </div>
    </div>
</x-app-layout>
