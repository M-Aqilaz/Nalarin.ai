<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center border border-green-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-emerald-100/90">{{ __('ui.quiz_arena') }}</p>
                <h2 class="mt-2 font-outfit font-bold text-2xl leading-tight soft-gradient-text">
                    {{ $quiz ? __('ui.quiz_title_for', ['material' => $selectedMaterial->title]) : __('ui.quiz_practice') }}
                </h2>
                <p class="mt-2 text-sm text-slate-300/80">
                    @if ($currentQuestion && $quiz)
                        {{ __('ui.question_progress', ['current' => (int) ($attempt['current_index'] ?? 0) + 1, 'total' => $quiz->questions->count()]) }}
                    @elseif ($quiz)
                        {{ trans_choice('ui.questions_ready', $quiz->question_count, ['count' => $quiz->question_count]) }}
                    @else
                        {{ __('ui.quiz_page_description') }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    @if ($currentQuestion && $quiz)
        <x-slot name="headerActions">
            <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-medium text-sm transition">
                    {{ __('ui.end_quiz') }}
                </button>
            </form>
        </x-slot>
    @endif

    <div class="readable-study-page space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-sm text-green-200">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif

        @php
            $nalaMood = 'flat';
            $nalaTitle = __('ui.quiz_guide_select_title');
            $nalaMessage = __('ui.quiz_guide_select_description');

            if ($currentQuestion && $quiz) {
                $nalaMood = 'angry';
                $nalaTitle = __('ui.quiz_guide_active_title');
                $nalaMessage = __('ui.quiz_guide_active_description');
            } elseif ($results) {
                $nalaMood = $results['score'] >= ceil($results['total'] * 0.7) ? 'happy' : 'sad';
                $nalaTitle = $results['score'] >= ceil($results['total'] * 0.7)
                    ? __('ui.quiz_guide_pass_title')
                    : __('ui.quiz_guide_retry_title');
                $nalaMessage = __('ui.quiz_guide_result_description');
            } elseif ($quiz) {
                $nalaMood = 'happy';
                $nalaTitle = __('ui.quiz_guide_ready_title');
                $nalaMessage = __('ui.quiz_guide_ready_description');
            } elseif ($selectedMaterial) {
                $nalaMood = 'flat';
                $nalaTitle = __('ui.quiz_guide_material_title');
                $nalaMessage = __('ui.quiz_guide_material_description');
            }
        @endphp

        <x-nala-guide :mood="$nalaMood" :title="$nalaTitle" :message="$nalaMessage" compact />

        <section class="feature-hero">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <p class="user-kicker text-[11px] text-emerald-100/90">{{ __('ui.material_source') }}</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white">{{ __('ui.select_material_for_quiz') }}</h3>
                    <p class="mt-2 text-sm text-slate-100/80">{{ __('ui.quiz_source_description') }}</p>
                </div>

                <form method="GET" action="{{ route('feature.quiz') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
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
                <p class="text-sm text-gray-400 mt-2">{{ __('ui.no_quiz_material_description') }}</p>
            </section>
        @elseif (! $quiz)
            <section class="glass-panel accent-card-emerald rounded-3xl p-6 sm:p-8">
                <p class="user-kicker text-[11px] text-emerald-100/90">{{ __('ui.selected_material') }}</p>
                <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ $selectedMaterial->title }}</h3>
                <p class="mt-3 text-slate-200/75">{{ __('ui.quiz_missing_description') }}</p>
                @unless (auth()->user()->isPremium())
                    <p class="mt-3 text-xs text-slate-300/55">{{ __('ui.free_content_limit', ['count' => config('services.openai.limits.content_free_per_day', 6)]) }}</p>
                @endunless
                <form method="POST" action="{{ route('quiz.generate') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                    <button type="submit" class="user-primary-button px-6 py-3 text-sm">{{ __('ui.create_quiz') }}</button>
                </form>
            </section>
        @elseif ($results)
            <section class="glass-panel accent-card-emerald rounded-[2rem] p-6 md:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">{{ __('ui.quiz_results') }}</p>
                        <h3 class="font-outfit text-3xl font-bold text-white mt-2">{{ $results['score'] }} / {{ $results['total'] }}</h3>
                        <p class="text-sm text-gray-400 mt-2">{{ __('ui.quiz_results_description') }}</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <form method="POST" action="{{ route('quiz.reset', $quiz) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.14]">{{ __('ui.reset') }}</button>
                        </form>
                        <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">{{ __('ui.retry_quiz') }}</button>
                        </form>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    @foreach ($results['items'] as $index => $item)
                        <div class="rounded-3xl border {{ $item['is_correct'] ? 'border-emerald-500/20 bg-emerald-500/10' : 'border-rose-500/20 bg-rose-500/10' }} p-5">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $item['is_correct'] ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">{{ $index + 1 }}</div>
                                <div class="flex-1">
                                    <p class="font-semibold text-white">{{ $item['prompt'] }}</p>
                                    <p class="mt-3 text-sm text-gray-200">{{ __('ui.your_answer') }}: {{ $item['selected'] ?? __('ui.not_answered') }}</p>
                                    <p class="mt-1 text-sm text-gray-100">{{ __('ui.correct_answer') }}: {{ $item['correct'] }}</p>
                                    @if ($item['explanation'])
                                        <p class="mt-3 text-sm leading-7 {{ $item['is_correct'] ? 'text-emerald-100/90' : 'text-rose-100/90' }}">{{ $item['explanation'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @elseif ($currentQuestion)
            @php($currentIndex = (int) ($attempt['current_index'] ?? 0))
            @php($totalQuestions = $quiz->questions->count())
            @php($progress = $totalQuestions > 0 ? round(($currentIndex / $totalQuestions) * 100) : 0)
            <div class="mx-auto max-w-4xl py-4">
                <div class="mb-8 h-2 w-full overflow-hidden rounded-full bg-slate-900/80">
                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-400 rounded-full relative" style="width: {{ $progress }}%">
                        <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/20 blur-[2px]"></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('quiz.answer', $quiz) }}" class="glass-panel accent-card-emerald relative overflow-hidden rounded-3xl p-5 shadow-2xl sm:p-8 md:p-10">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                    <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 font-outfit text-xl font-bold text-gray-300">
                            {{ $currentIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl md:text-2xl font-bold text-white mb-6 leading-relaxed font-outfit">{{ $currentQuestion->prompt }}</h4>

                            <div class="mt-8 space-y-3">
                                @foreach (($currentQuestion->choices ?? []) as $choiceIndex => $choice)
                                    <label class="block relative cursor-pointer group">
                                        <input type="radio" name="choice" value="{{ $choiceIndex }}" class="peer sr-only" required>
                                        <div class="flex w-full items-start gap-4 rounded-xl border-2 border-white/10 bg-white/5 p-4 font-medium text-gray-300 transition-all group-hover:border-white/20 group-hover:bg-white/10 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-white">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-gray-800 text-sm font-bold">{{ chr(65 + $choiceIndex) }}</span>
                                            <span class="min-w-0 break-words">{{ $choice }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-6 py-2.5 text-sm font-medium text-white opacity-60 sm:justify-start">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    {{ __('ui.previous') }}
                                </div>
                                <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-green-500 px-8 py-2.5 text-sm font-semibold text-white shadow-[0_0_15px_rgba(34,197,94,0.4)] transition hover:bg-green-400">
                                    {{ $currentIndex + 1 === $totalQuestions ? __('ui.finish') : __('ui.next') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <section class="glass-panel accent-card-emerald rounded-[2rem] p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">{{ __('ui.quiz_ready') }}</p>
                        <h3 class="font-outfit text-2xl font-bold text-white mt-2">{{ __('ui.quiz_title_for', ['material' => $selectedMaterial->title]) }}</h3>
                        <p class="text-sm text-gray-400 mt-2">{{ __('ui.quiz_ready_description') }}</p>
                    </div>

                    <form method="POST" action="{{ route('quiz.generate') }}" class="w-full lg:w-auto">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $selectedMaterial->id }}">
                        <button type="submit" class="rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-2.5 text-sm text-white transition hover:bg-white/[0.14]">{{ __('ui.regenerate') }}</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-8">
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.total_questions') }}</p>
                        <p class="mt-3 text-2xl font-outfit font-bold text-white">{{ $quiz->question_count }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.material') }}</p>
                        <p class="mt-3 text-sm font-semibold text-white">{{ $selectedMaterial->title }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('ui.format') }}</p>
                        <p class="mt-3 text-sm font-semibold text-white">{{ __('ui.multiple_choice') }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('quiz.start', $quiz) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">{{ __('ui.start_quiz') }}</button>
                    </form>
                    <form method="POST" action="{{ route('quiz.reset', $quiz) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/[0.08] px-6 py-3 text-sm font-medium text-white transition hover:bg-white/[0.14]">{{ __('ui.reset_progress') }}</button>
                    </form>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
