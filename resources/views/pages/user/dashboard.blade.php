<x-app-layout>
    @php
        $user = auth()->user();
        $shortcuts = [
            ['label' => __('ui.upload_material'), 'desc' => __('ui.upload_description'), 'href' => route('feature.upload'), 'tone' => 'from-sky-100 to-white', 'icon' => 'upload'],
            ['label' => __('ui.summary'), 'desc' => __('ui.summary_description'), 'href' => route('feature.summary'), 'tone' => 'from-rose-100 to-pink-50', 'icon' => 'summary'],
            ['label' => __('ui.ai_tutor'), 'desc' => __('ui.tutor_description'), 'href' => route('feature.chat'), 'tone' => 'from-violet-100 to-fuchsia-50', 'icon' => 'tutor'],
            ['label' => __('ui.flashcards'), 'desc' => __('ui.flashcards_description'), 'href' => route('feature.flashcards'), 'tone' => 'from-cyan-100 to-teal-50', 'icon' => 'flashcards'],
            ['label' => __('ui.quiz'), 'desc' => __('ui.quiz_description'), 'href' => route('feature.quiz'), 'tone' => 'from-amber-100 to-yellow-50', 'icon' => 'quiz'],
            ['label' => __('ui.study_matching'), 'desc' => __('ui.matching_description'), 'href' => route('matchmaking.index'), 'tone' => 'from-emerald-100 to-cyan-50', 'icon' => 'matching'],
        ];

        $recommendation = match (true) {
            $materialCount === 0 => [
                'title' => __('ui.recommend_upload_title'),
                'description' => __('ui.recommend_upload_description'),
                'action' => __('ui.recommend_upload_action'),
                'href' => route('feature.upload'),
                'icon' => 'upload',
            ],
            $summaryCount === 0 => [
                'title' => __('ui.recommend_summary_title'),
                'description' => __('ui.recommend_summary_description'),
                'action' => __('ui.recommend_summary_action'),
                'href' => route('feature.summary'),
                'icon' => 'summary',
            ],
            $threadCount === 0 => [
                'title' => __('ui.recommend_tutor_title'),
                'description' => __('ui.recommend_tutor_description'),
                'action' => __('ui.recommend_tutor_action'),
                'href' => route('feature.chat'),
                'icon' => 'tutor',
            ],
            $roomCount === 0 => [
                'title' => __('ui.recommend_room_title'),
                'description' => __('ui.recommend_room_description'),
                'action' => __('ui.recommend_room_action'),
                'href' => route('matchmaking.index'),
                'icon' => 'matching',
            ],
            default => [
                'title' => __('ui.recommend_flashcard_title'),
                'description' => __('ui.recommend_flashcard_description'),
                'action' => __('ui.recommend_flashcard_action'),
                'href' => route('feature.flashcards'),
                'icon' => 'flashcards',
            ],
        };

        $displayPlan = $user->plan === 'pro' ? __('ui.premium') : ucfirst($user->plan);
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-800">{{ __('ui.learning_hub') }}</p>
            <h2 class="mt-2 font-outfit text-3xl font-extrabold leading-tight text-slate-950 md:text-4xl">{{ __('ui.learning_dashboard') }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-800">{{ __('ui.dashboard_description') }}</p>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('feature.upload') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-500/25 transition hover:bg-sky-600 md:w-auto">{{ __('ui.new_material') }}</a>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-[1.75rem] border border-sky-200 bg-white/88 p-5 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            <div class="grid gap-5 lg:grid-cols-[1fr_220px] lg:items-center">
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">{{ __('ui.nala_guide') }}</p>
                    <h3 class="mt-2 font-outfit text-2xl font-extrabold text-slate-950">{{ __('ui.where_to_start') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('ui.launcher_description') }}</p>
                </div>
                <div class="flex justify-center lg:justify-end">
                    <div class="flex h-72 w-56 items-end justify-center overflow-hidden rounded-[2rem] bg-white/75 shadow-inner ring-1 ring-sky-100">
                        <img src="{{ asset('images/nala_teacher.png') }}" class="animate-nala-float h-full w-full object-cover object-top drop-shadow-[0_18px_28px_rgba(14,116,144,0.18)]" alt="Nala menyapa di dashboard">
                    </div>
                </div>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                @foreach ($shortcuts as $shortcut)
                    <a href="{{ $shortcut['href'] }}" class="group min-h-[126px] rounded-[1.4rem] border border-sky-200 bg-gradient-to-br {{ $shortcut['tone'] }} p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-[0_18px_35px_rgba(14,116,144,0.14)]">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/80 text-sky-700 shadow-sm">
                            <x-feature-icon :name="$shortcut['icon']" class="h-6 w-6" />
                        </span>
                        <h4 class="mt-4 font-outfit text-lg font-extrabold text-slate-950">{{ $shortcut['label'] }}</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $shortcut['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => __('ui.materials'), 'value' => $materialCount, 'tone' => 'from-sky-100 to-white', 'icon' => 'material'],
                ['label' => __('ui.summary'), 'value' => $summaryCount, 'tone' => 'from-rose-100 to-pink-50', 'icon' => 'summary'],
                ['label' => __('ui.ai_conversations'), 'value' => $threadCount, 'tone' => 'from-violet-100 to-fuchsia-50', 'icon' => 'tutor'],
                ['label' => __('ui.class_room'), 'value' => $roomCount, 'tone' => 'from-cyan-100 to-teal-50', 'icon' => 'room'],
                ['label' => __('ui.active_matches'), 'value' => $activeMatchCount, 'tone' => 'from-amber-100 to-yellow-50', 'icon' => 'matching'],
            ] as $stat)
                <article class="min-h-[112px] rounded-[1.65rem] border border-sky-200 bg-gradient-to-br {{ $stat['tone'] }} p-5 shadow-[0_18px_35px_rgba(14,116,144,0.12)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-700">{{ $stat['label'] }}</p>
                            <p class="mt-4 font-roboto text-3xl font-extrabold text-slate-950">{{ $stat['value'] }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/75 text-sky-700 shadow-sm">
                            <x-feature-icon :name="$stat['icon']" class="h-6 w-6" />
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <section class="rounded-[1.75rem] border border-sky-300 bg-gradient-to-r from-sky-500 to-teal-400 p-5 text-white shadow-[0_20px_45px_rgba(14,165,233,0.25)] md:flex md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-white/85">{{ __('ui.plan') }} {{ $displayPlan }}</p>
                <p class="mt-4 text-xl font-extrabold">{{ __('ui.remaining_match_quota', ['count' => $user->match_credits]) }}</p>
                <p class="mt-2 text-sm text-white/90">{{ __('ui.premium_description') }}</p>
            </div>
            <a href="{{ route('pricing') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-900/20 transition hover:bg-sky-700 md:mt-0 md:w-auto">{{ __('ui.view_pricing') }}</a>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">{{ __('ui.class_room') }}</h3>
                    <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-cyan-700">{{ __('ui.open_room') }}</a>
                </div>
                <div class="min-h-[112px] p-4">
                    @forelse ($recentRooms as $room)
                        <a href="{{ route('rooms.show', $room) }}" class="block rounded-xl bg-sky-100 px-4 py-3 transition hover:bg-sky-200">
                            <p class="font-bold text-slate-950">{{ $room->name }}</p>
                            <p class="mt-1 text-sm text-slate-700">{{ $room->topic }} | {{ __('ui.members', ['count' => $room->members_count]) }}</p>
                        </a>
                    @empty
                        <div class="text-sm text-slate-700">{{ __('ui.not_joined_room') }}</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">{{ __('ui.latest_materials') }}</h3>
                    <a href="{{ route('materials.index') }}" class="text-sm font-semibold text-cyan-700">{{ __('ui.view_all') }}</a>
                </div>
                <div class="min-h-[112px] divide-y divide-sky-100">
                    @forelse ($recentMaterials as $material)
                        <a href="{{ route('materials.show', $material) }}" class="flex items-center gap-4 p-4 transition hover:bg-sky-50">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                                <x-feature-icon name="material" class="h-6 w-6" />
                            </span>
                            <span>
                                <span class="block font-bold text-slate-950">{{ $material->title }}</span>
                                <span class="mt-1 block text-sm text-slate-700">{{ $material->status }} | {{ $material->summaries->count() }} ringkasan</span>
                            </span>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-slate-700">{{ __('ui.no_materials') }}</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_35px_rgba(14,116,144,0.12)] backdrop-blur">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <h3 class="font-outfit text-xl font-extrabold text-slate-950">{{ __('ui.ai_conversations') }}</h3>
                    <a href="{{ route('feature.chat') }}" class="text-sm font-semibold text-cyan-700">{{ __('ui.open_chat') }}</a>
                </div>
                <div class="min-h-[112px] p-4">
                    @forelse ($recentThreads as $thread)
                        <a href="{{ route('chat.show', $thread) }}" class="block rounded-xl px-1 py-2 transition hover:bg-sky-50">
                            <p class="font-bold text-slate-950">{{ $thread->title }}</p>
                            <p class="mt-1 text-sm text-slate-700">{{ __('ui.messages', ['count' => $thread->messages_count]) }} | {{ $thread->material?->title ?? __('ui.without_material') }}</p>
                        </a>
                    @empty
                        <div class="text-sm text-slate-700">{{ __('ui.no_conversations') }}</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-gradient-to-r from-white via-sky-50 to-cyan-50 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
            <div class="grid gap-5 p-5 md:grid-cols-[auto_1fr_auto] md:items-center md:p-6">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-500 text-white shadow-lg shadow-sky-500/20">
                    <x-feature-icon :name="$recommendation['icon']" class="h-7 w-7" />
                </span>
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">{{ __('ui.today_recommendation') }}</p>
                    <h3 class="mt-2 font-outfit text-xl font-extrabold text-slate-950 md:text-2xl">{{ $recommendation['title'] }}</h3>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $recommendation['description'] }}</p>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ __('ui.recommendation_local_note') }}</p>
                </div>
                <a href="{{ $recommendation['href'] }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-500 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-600 md:w-auto">
                    {{ $recommendation['action'] }}
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
