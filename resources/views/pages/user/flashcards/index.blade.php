<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center border border-pink-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-pink-200/90">{{ __('ui.flashcard_studio') }}</p>
                <h2 class="mt-2 font-outfit font-bold text-2xl leading-tight soft-gradient-text">
                    {{ $deck ? $deck->title : __('ui.smart_flashcard') }}
                </h2>
                <p class="mt-2 text-sm text-slate-300/80">
                    @if ($deck && $currentCard)
                        {{ __('ui.flashcard_progress', ['current' => $currentCard->sort_order, 'total' => $deck->card_count]) }}
                    @else
                        {{ __('ui.flashcard_page_description') }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <style>
        .flashcard-perspective { perspective: 1200px; }
        .flashcard-stack { transform-style: preserve-3d; }
        .flashcard-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flashcard-rotated { transform: rotateY(180deg); }
    </style>

    <div class="readable-study-page space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif

        @php
            $nalaMood = 'flat';
            $nalaTitle = __('ui.flashcard_guide_select_title');
            $nalaMessage = __('ui.flashcard_guide_select_description');

            if ($deck && $currentCard) {
                $nalaMood = 'happy';
                $nalaTitle = __('ui.flashcard_guide_review_title');
                $nalaMessage = __('ui.flashcard_guide_review_description');
            } elseif ($deck) {
                $nalaMood = 'happy';
                $nalaTitle = __('ui.flashcard_guide_ready_title');
                $nalaMessage = __('ui.flashcard_guide_ready_description');
            } elseif ($selectedMaterial) {
                $nalaMood = 'flat';
                $nalaTitle = __('ui.flashcard_guide_create_title');
                $nalaMessage = __('ui.flashcard_guide_create_description');
            }
        @endphp

        <x-nala-guide :mood="$nalaMood" :title="$nalaTitle" :message="$nalaMessage" compact />

        <section class="feature-hero">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <p class="user-kicker text-[11px] text-pink-100/90">{{ __('ui.material_source') }}</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white">{{ __('ui.use_uploaded_material') }}</h3>
                    <p class="mt-2 text-sm text-slate-100/80">{{ __('ui.flashcard_source_description') }}</p>
                </div>

                <form method="GET" action="{{ route('feature.flashcards') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
                    <select name="material_id" class="glass-input w-full px-4 py-3 text-sm sm:min-w-[260px]">
                        <option value="">{{ __('ui.select_material') }}</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" @selected($selectedMaterial?->id === $material->id)>{{ $material->title }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-5 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">{{ __('ui.open_material') }}</button>
                </form>
            </div>
        </section>

        @if (! $selectedMaterial)
            <section class="glass-panel rounded-3xl border border-dashed border-white/10 p-6 text-center sm:p-10">
                <p class="text-lg font-outfit text-white">{{ __('ui.no_material_selected') }}</p>
                <p class="text-sm text-gray-400 mt-2">{{ __('ui.no_material_selected_description') }}</p>
            </section>
        @elseif (! $deck)
            <section class="glass-panel accent-card-pink rounded-3xl p-6 sm:p-8">
                <p class="user-kicker text-[11px] text-pink-100/90">{{ __('ui.selected_material') }}</p>
                <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $selectedMaterial->title }}</h3>
                <p class="mt-3 text-slate-200/75">{{ __('ui.deck_missing_description') }}</p>
                @unless (auth()->user()->isPremium())
                    <p class="mt-3 text-xs text-slate-300/55">{{ __('ui.free_content_limit', ['count' => config('services.openai.limits.content_free_per_day', 6)]) }}</p>
                @endunless
                <form method="POST" action="{{ route('flashcards.generate') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                    <button type="submit" class="user-primary-button px-6 py-3 text-sm">{{ __('ui.create_flashcard') }}</button>
                </form>
            </section>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)] items-start">
                <section class="glass-panel accent-card-violet rounded-[2rem] p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] text-pink-300">{{ __('ui.active_deck') }}</p>
                            <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $deck->title }}</h3>
                            <p class="text-sm text-gray-400 mt-2">{{ $deck->description }}</p>
                        </div>

                        <form method="POST" action="{{ route('flashcards.generate') }}">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                            <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.14]">{{ __('ui.regenerate') }}</button>
                        </form>
                    </div>

                    <div class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="glass-panel rounded-2xl p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.total_cards') }}</p>
                            <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $deck->card_count }}</p>
                        </div>
                        <div class="glass-panel rounded-2xl p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.ready_for_review') }}</p>
                            <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $dueCount }}</p>
                        </div>
                        <div class="glass-panel rounded-2xl p-4">
                            <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.materials') }}</p>
                            <p class="mt-3 text-sm font-semibold text-white">{{ $selectedMaterial->title }}</p>
                        </div>
                    </div>

                    @if ($currentCard && $dueCount > 0)
                        @php
                            $flashcardCards = $deck->cards->map(fn ($card) => [
                                'id' => $card->id,
                                'front' => $card->front,
                                'back' => $card->back,
                                'example' => $card->example,
                                'difficulty' => $card->difficulty,
                                'sort_order' => $card->sort_order,
                                'is_due' => $card->next_review_at === null || $card->next_review_at->isPast(),
                                'next_review_label' => $card->next_review_at ? $card->next_review_at->locale(app()->getLocale())->diffForHumans() : __('ui.ready_now'),
                            ])->values();
                            $currentCardIndex = $flashcardCards->search(fn ($card) => $card['id'] === $currentCard->id);
                            $currentCardIndex = $currentCardIndex === false ? 0 : $currentCardIndex;
                        @endphp
                        <div
                            x-data="{
                                cards: @js($flashcardCards),
                                currentIndex: {{ $currentCardIndex }},
                                flipped: false,
                                get card() { return this.cards[this.currentIndex] || this.cards[0]; },
                                previous() {
                                    if (this.currentIndex > 0) {
                                        this.currentIndex--;
                                        this.flipped = false;
                                    }
                                },
                                next() {
                                    if (this.currentIndex < this.cards.length - 1) {
                                        this.currentIndex++;
                                        this.flipped = false;
                                    }
                                },
                            }"
                            class="mx-auto flex max-w-3xl flex-col items-center py-4 sm:py-6"
                        >
                            <div class="flashcard-perspective h-72 w-full max-w-xl cursor-pointer sm:h-80" @click="flipped = !flipped">
                                <div class="flashcard-stack w-full h-full relative transition-transform duration-700 shadow-2xl" :class="flipped ? 'flashcard-rotated' : ''">
                                    <div class="flashcard-face absolute inset-0 w-full h-full glass-panel rounded-3xl border border-white/10 flex flex-col items-center justify-center p-8">
                                        <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-400 uppercase">{{ __('ui.term_front') }}</p>
                                        <svg class="absolute top-6 right-6 w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                                        <div class="text-center">
                                            <p class="text-xs uppercase tracking-[0.3em] text-pink-300" x-text="card.difficulty"></p>
                                            <h2 class="mt-5 text-center font-outfit text-3xl font-bold text-white sm:text-4xl md:text-5xl" x-text="card.front"></h2>
                                        </div>
                                    </div>

                                    <div class="flashcard-face flashcard-rotated absolute inset-0 w-full h-full bg-gradient-to-br from-pink-600 to-purple-700 rounded-3xl border border-white/10 flex flex-col items-center justify-center p-8 shadow-[0_0_30px_rgba(219,39,119,0.3)]">
                                        <p class="absolute top-6 left-6 text-xs font-bold tracking-wider text-pink-200 uppercase">{{ __('ui.definition_back') }}</p>
                                        <div class="text-center">
                                            <h3 class="mb-4 font-outfit text-xl font-bold italic text-white sm:text-2xl" x-text="`&quot;${card.back}&quot;`"></h3>
                                            <p x-show="card.example" class="text-pink-100/80 text-sm" x-text="card.example"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center gap-4 sm:gap-6">
                                <button type="button" @click.stop="previous()" :disabled="currentIndex === 0" class="w-12 h-12 rounded-full border border-white/10 bg-white/5 text-white flex items-center justify-center shadow-lg transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-35">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <span class="text-gray-200 font-semibold font-outfit text-lg w-24 text-center"><span x-text="card.sort_order"></span> / {{ $deck->card_count }}</span>
                                <button type="button" @click.stop="next()" :disabled="currentIndex === cards.length - 1" class="w-12 h-12 rounded-full bg-pink-600 text-white flex items-center justify-center shadow-[0_0_15px_rgba(219,39,119,0.4)] transition hover:bg-pink-500 disabled:cursor-not-allowed disabled:opacity-35">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                            <form x-show="card.is_due" method="POST" action="{{ route('flashcards.review', $deck) }}" class="mx-auto mt-8 grid w-full max-w-lg grid-cols-2 gap-3 lg:grid-cols-4">
                                @csrf
                                <input type="hidden" name="flashcard_id" :value="card.id">
                                <button type="submit" name="rating" value="again" class="min-h-14 rounded-xl border border-red-200 bg-red-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide !text-white shadow-lg shadow-red-950/30 ring-1 ring-white/20 transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 sm:text-base">{{ __('ui.rating_again') }}</button>
                                <button type="submit" name="rating" value="hard" class="min-h-14 rounded-xl border border-orange-100 bg-orange-500 px-4 py-3 text-sm font-extrabold uppercase tracking-wide !text-white shadow-lg shadow-orange-950/30 ring-1 ring-white/20 transition hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100 sm:text-base">{{ __('ui.rating_hard') }}</button>
                                <button type="submit" name="rating" value="good" class="min-h-14 rounded-xl border border-sky-100 bg-sky-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide !text-white shadow-lg shadow-sky-950/30 ring-1 ring-white/20 transition hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 sm:text-base">{{ __('ui.rating_good') }}</button>
                                <button type="submit" name="rating" value="easy" class="min-h-14 rounded-xl border border-emerald-100 bg-emerald-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide !text-white shadow-lg shadow-emerald-950/30 ring-1 ring-white/20 transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100 sm:text-base">{{ __('ui.rating_easy') }}</button>
                            </form>

                            <div x-show="!card.is_due" class="mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-100" x-text="@js(__('ui.cards_safe', ['time' => '__TIME__'])).replace('__TIME__', card.next_review_label)">
                            </div>
                        </div>
                    @elseif ($deck)
                        @php
                            $reviewedCount = $deck->cards->whereNotNull('last_reviewed_at')->count();
                            $nextReviewAt = $deck->cards
                                ->filter(fn ($card) => $card->next_review_at !== null)
                                ->sortBy('next_review_at')
                                ->first()?->next_review_at;
                        @endphp
                        <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-400/25 bg-emerald-500/10 p-6 text-center shadow-xl shadow-emerald-950/10 sm:p-8">
                            <p class="user-kicker text-[11px] text-emerald-100/90">{{ __('ui.flashcard_review_complete_kicker') }}</p>
                            <h3 class="mt-3 font-outfit text-2xl font-bold text-white">{{ __('ui.flashcard_review_complete_title') }}</h3>
                            <p class="mt-3 text-sm leading-6 text-emerald-50/85">
                                {{ __('ui.flashcard_review_complete_summary', [
                                    'reviewed' => $reviewedCount,
                                    'total' => $deck->card_count,
                                    'time' => $nextReviewAt ? $nextReviewAt->locale(app()->getLocale())->diffForHumans() : __('ui.later'),
                                ]) }}
                            </p>
                        </div>
                    @endif
                </section>

                <aside class="glass-panel accent-card-pink rounded-[2rem] p-5 sm:p-6">
                    <p class="user-kicker text-[11px] text-pink-100/90">{{ __('ui.card_list') }}</p>
                    <div class="mt-4 space-y-3 max-h-[42rem] overflow-y-auto pr-1">
                        @foreach ($deck->cards as $card)
                            <div class="glass-panel rounded-2xl p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ $card->front }}</p>
                                        <p class="mt-2 text-sm text-gray-400">{{ \Illuminate\Support\Str::limit($card->back, 110) }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $card->next_review_at === null || $card->next_review_at->isPast() ? 'bg-pink-500/20 text-pink-200' : 'bg-white/10 text-gray-300' }}">
                                        {{ $card->next_review_at === null || $card->next_review_at->isPast() ? __('ui.due') : __('ui.scheduled') }}
                                    </span>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $card->difficulty }}</span>
                                    <span>{{ $card->next_review_at ? $card->next_review_at->locale(app()->getLocale())->diffForHumans() : __('ui.ready_now') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        @endif
    </div>
</x-app-layout>
