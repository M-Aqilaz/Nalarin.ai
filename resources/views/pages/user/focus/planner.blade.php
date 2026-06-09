<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-cyan-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M8 12h8M8 17h5M6 3h12a1 1 0 011 1v16l-3-2-4 2-4-2-3 2V4a1 1 0 011-1z"></path>
                </svg>
            </div>
            <div>
                <p class="user-kicker text-[11px] text-cyan-100/90">{{ __('ui.focus_section') }}</p>
                <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text">{{ __('ui.focus_planner') }}</h2>
            </div>
        </div>
    </x-slot>

    @php
        $plannerTranslations = collect([
            'defaultPlanTitle', 'defaultEnergyLabel', 'defaultNotice', 'defaultTaskSummary', 'defaultTaskQuiz',
            'defaultBlockCore', 'defaultBlockFlashcard', 'focusLoadHeavy', 'focusLoadBalanced', 'focusLoadLight',
            'planHealthOverplanned', 'planHealthIncomplete', 'planHealthFocused', 'readinessReady', 'readinessAlmost',
            'readinessNeedsWork', 'recommendationComplete', 'recommendationPriorities', 'recommendationBlocks',
            'recommendationHighPriority', 'recommendationAddItem', 'recommendationHealthy', 'noticeTaskTitle',
            'noticeTaskAdded', 'noticeTaskUpdated', 'noticeTaskRemoved', 'noticeTaskReordered', 'noticeBlockTitle',
            'noticeBlockAdded', 'noticeBlockUpdated', 'noticeBlockRemoved', 'noticeBlockReordered', 'noticeReset',
            'noticeTemplateApplied', 'examPlanTitle', 'morningPrimeTime', 'examTaskWeakTopic', 'examTaskPractice',
            'examBlockConcept', 'examBlockPractice', 'revisionPlanTitle', 'revisionTaskNotes', 'revisionTaskFlashcard',
            'revisionBlockSummary', 'revisionBlockDifficult', 'projectPlanTitle', 'afternoonPrimeTime',
            'projectTaskBreakdown', 'projectTaskCore', 'projectBlockImplementation', 'projectBlockReview',
            'priority_high', 'priority_medium', 'priority_low', 'category_concept', 'category_practice',
            'category_review', 'category_project', 'mode_deep-work', 'mode_review', 'mode_practice',
            'energy_prime', 'energy_steady', 'energy_light',
        ])->mapWithKeys(fn ($key) => [$key => __("ui.planner_{$key}")])->all();

        $plannerPresetAliases = [
            ['aliases' => ['Sprint Fokus Hari Ini', 'Today\'s Focus Sprint'], 'value' => __('ui.planner_defaultPlanTitle')],
            ['aliases' => ['Prime time malam', 'Evening prime time'], 'value' => __('ui.planner_defaultEnergyLabel')],
            ['aliases' => ['Baca ulang ringkasan materi utama', 'Review the main material summary'], 'value' => __('ui.planner_defaultTaskSummary')],
            ['aliases' => ['Kerjakan 1 sesi latihan kuis', 'Complete one quiz practice session'], 'value' => __('ui.planner_defaultTaskQuiz')],
            ['aliases' => ['Deep work materi inti', 'Deep work on core material'], 'value' => __('ui.planner_defaultBlockCore')],
            ['aliases' => ['Review cepat flashcard', 'Quick Flashcard review'], 'value' => __('ui.planner_defaultBlockFlashcard')],
            ['aliases' => ['Susun prioritas singkat dan blok belajar yang realistis.', 'Set concise priorities and realistic study blocks.'], 'value' => __('ui.planner_defaultNotice')],
            ['aliases' => ['Sprint Ujian', 'Exam Sprint'], 'value' => __('ui.planner_examPlanTitle')],
            ['aliases' => ['Prime time pagi', 'Morning prime time'], 'value' => __('ui.planner_morningPrimeTime')],
            ['aliases' => ['Pahami ulang topik paling lemah', 'Review your weakest topic'], 'value' => __('ui.planner_examTaskWeakTopic')],
            ['aliases' => ['Latihan soal inti', 'Complete the main practice questions'], 'value' => __('ui.planner_examTaskPractice')],
            ['aliases' => ['Deep work konsep utama', 'Deep work on key concepts'], 'value' => __('ui.planner_examBlockConcept')],
            ['aliases' => ['Practice set singkat', 'Short practice set'], 'value' => __('ui.planner_examBlockPractice')],
            ['aliases' => ['Sprint Review Materi', 'Material Review Sprint'], 'value' => __('ui.planner_revisionPlanTitle')],
            ['aliases' => ['Review catatan dan summary', 'Review notes and summaries'], 'value' => __('ui.planner_revisionTaskNotes')],
            ['aliases' => ['Uji pemahaman dengan flashcard', 'Test your understanding with Flashcard'], 'value' => __('ui.planner_revisionTaskFlashcard')],
            ['aliases' => ['Review ringkasan aktif', 'Active summary review'], 'value' => __('ui.planner_revisionBlockSummary')],
            ['aliases' => ['Deep work poin sulit', 'Deep work on difficult points'], 'value' => __('ui.planner_revisionBlockDifficult')],
            ['aliases' => ['Sprint Tugas / Project', 'Assignment / Project Sprint'], 'value' => __('ui.planner_projectPlanTitle')],
            ['aliases' => ['Prime time siang', 'Afternoon prime time'], 'value' => __('ui.planner_afternoonPrimeTime')],
            ['aliases' => ['Pecah deliverable jadi subtask kecil', 'Break the deliverable into smaller tasks'], 'value' => __('ui.planner_projectTaskBreakdown')],
            ['aliases' => ['Kerjakan bagian inti project', 'Complete the core project work'], 'value' => __('ui.planner_projectTaskCore')],
            ['aliases' => ['Deep work implementasi', 'Deep work on implementation'], 'value' => __('ui.planner_projectBlockImplementation')],
            ['aliases' => ['Review hasil dan revisi', 'Review the result and revise it'], 'value' => __('ui.planner_projectBlockReview')],
        ];
    @endphp

    <div x-data="focusPlanner({ translations: @js($plannerTranslations), presetAliases: @js($plannerPresetAliases) })" x-init="init()" class="readable-study-page space-y-6">
        <section class="feature-hero overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-48 rounded-full bg-cyan-400/15 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="user-kicker text-cyan-100/90">{{ __('ui.planner_daily_planning') }}</p>
                    <h1 class="mt-3 font-outfit text-3xl font-semibold tracking-tight text-white md:text-4xl">
                        {{ __('ui.planner_hero_title') }}
                    </h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-200/80">
                        {{ __('ui.planner_hero_description') }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:w-[28rem]">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.planner_readiness') }}</p>
                        <p class="mt-3 font-outfit text-3xl text-white"><span x-text="readinessPercent"></span>%</p>
                        <p class="mt-2 text-xs text-slate-400" x-text="readinessLabel"></p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.planner_plan_health') }}</p>
                        <p class="mt-3 font-outfit text-3xl text-white" x-text="plannerHealthLabel"></p>
                        <p class="mt-2 text-xs text-slate-400"><span x-text="highPriorityTasksCount"></span> {{ __('ui.planner_high_priority_active') }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/45 p-4">
                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('ui.planner_focus_load') }}</p>
                        <p class="mt-3 font-outfit text-3xl text-white" x-text="focusLoadLabel"></p>
                        <p class="mt-2 text-xs text-slate-400"><span x-text="totalBlockMinutes"></span> {{ __('ui.planner_scheduled_minutes') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.8fr)]">
            <div class="space-y-6">
                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div class="space-y-2">
                            <p class="user-kicker">{{ __('ui.planner_plan_setup') }}</p>
                            <h3 class="font-outfit text-2xl font-semibold text-white">{{ __('ui.planner_daily_framework') }}</h3>
                            <p class="text-sm text-slate-300/80">{{ __('ui.planner_daily_framework_description') }}</p>
                        </div>
                        <button
                            type="button"
                            @click="resetPlanner()"
                            class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-5 text-sm font-medium text-slate-100 transition hover:bg-white/[0.06]"
                        >
                            {{ __('ui.planner_reset') }}
                        </button>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <label class="space-y-2">
                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">{{ __('ui.planner_focus_title') }}</span>
                            <input x-model="planTitle" @input.debounce.300ms="persistState()" type="text" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_defaultPlanTitle') }}">
                        </label>
                        <label class="space-y-2">
                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">{{ __('ui.planner_session_target') }}</span>
                            <input x-model.number="targetSessions" @change="persistState()" type="number" min="1" max="12" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                        </label>
                        <label class="space-y-2">
                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">{{ __('ui.planner_prime_time') }}</span>
                            <input x-model="energyLabel" @input.debounce.300ms="persistState()" type="text" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_prime_time_placeholder') }}">
                        </label>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,220px)_1fr]">
                        <label class="space-y-2">
                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">{{ __('ui.planner_distraction_limit') }}</span>
                            <input x-model.number="distractionCap" @change="persistState()" type="number" min="0" max="8" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                        </label>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_quick_template') }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="button" @click="applyTemplate('exam')" class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-xs font-medium text-cyan-100 transition hover:bg-cyan-400/15">{{ __('ui.planner_exam_template') }}</button>
                                <button type="button" @click="applyTemplate('revision')" class="rounded-full border border-violet-400/20 bg-violet-400/10 px-4 py-2 text-xs font-medium text-violet-100 transition hover:bg-violet-400/15">{{ __('ui.planner_revision_template') }}</button>
                                <button type="button" @click="applyTemplate('project')" class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-4 py-2 text-xs font-medium text-emerald-100 transition hover:bg-emerald-400/15">{{ __('ui.planner_project_template') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="user-kicker">{{ __('ui.planner_priority_tasks') }}</p>
                            <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">{{ __('ui.planner_top_tasks') }}</h3>
                        </div>
                        <p class="text-sm text-slate-400">
                            <span x-text="completedTasksCount"></span> {{ __('ui.planner_completed_of') }} <span x-text="tasks.length"></span> {{ __('ui.planner_tasks') }}
                        </p>
                    </div>

                    <div class="mt-6 grid gap-3 xl:grid-cols-[minmax(0,1fr)_110px_160px_160px_130px]">
                        <input x-model="newTaskTitle" type="text" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_task_placeholder') }}">
                        <input x-model.number="newTaskEstimate" type="number" min="1" max="6" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_sessions') }}">
                        <select x-model="newTaskPriority" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                            <option value="high">{{ __('ui.planner_priority_high') }}</option>
                            <option value="medium">{{ __('ui.planner_priority_medium') }}</option>
                            <option value="low">{{ __('ui.planner_priority_low') }}</option>
                        </select>
                        <select x-model="newTaskCategory" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                            <option value="concept">{{ __('ui.planner_category_concept') }}</option>
                            <option value="practice">{{ __('ui.planner_category_practice') }}</option>
                            <option value="review">{{ __('ui.planner_category_review') }}</option>
                            <option value="project">{{ __('ui.planner_category_project') }}</option>
                        </select>
                        <button type="button" @click="addTask()" class="user-primary-button h-12 rounded-2xl px-5 text-sm font-semibold text-slate-950">
                            {{ __('ui.planner_add_task') }}
                        </button>
                    </div>

                    <div class="mt-6 space-y-3">
                        <template x-if="!activeTasks.length">
                            <div class="rounded-3xl border border-dashed border-white/10 bg-slate-950/35 px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('ui.planner_no_tasks') }}
                            </div>
                        </template>

                        <template x-for="task in activeTasks" :key="task.id">
                            <div
                                draggable="true"
                                @dragstart="startTaskDrag(task.id)"
                                @dragend="endTaskDrag()"
                                @dragover.prevent
                                @drop.prevent="reorderTask(task.id); endTaskDrag()"
                                class="flex cursor-grab flex-col gap-3 rounded-3xl border border-white/10 bg-slate-950/40 p-4 transition active:cursor-grabbing sm:flex-row sm:items-start sm:justify-between"
                                :class="draggingTaskId === task.id ? 'scale-[0.99] border-cyan-400/25 bg-cyan-400/8 opacity-70 shadow-[0_18px_40px_rgba(34,211,238,0.08)]' : ''"
                            >
                                <div class="flex items-start gap-3">
                                    <button type="button" draggable="false" @mousedown.stop @click.stop="toggleTask(task.id)" class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border transition" :class="task.completed ? 'border-emerald-400/40 bg-emerald-400/15 text-emerald-200' : 'border-white/15 bg-white/[0.03] text-slate-500'">
                                        <svg x-show="task.completed" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <div>
                                        <p class="text-sm font-medium" :class="task.completed ? 'text-slate-400 line-through' : 'text-white'" x-text="task.title"></p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-slate-400" x-text="categoryLabel(task.category)"></span>
                                            <span class="rounded-full px-3 py-1 text-[11px] uppercase tracking-[0.18em]"
                                                :class="task.priority === 'high' ? 'border border-rose-400/20 bg-rose-400/10 text-rose-100' : (task.priority === 'medium' ? 'border border-amber-400/20 bg-amber-400/10 text-amber-100' : 'border border-emerald-400/20 bg-emerald-400/10 text-emerald-100')"
                                                x-text="priorityLabel(task.priority)">
                                            </span>
                                            <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-cyan-100">
                                                <span x-text="task.estimate"></span> {{ __('ui.planner_sessions_lower') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center">
                                    <button type="button" draggable="false" @mousedown.stop @click.stop="moveTask(task.id, 'up')" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] text-slate-300 transition hover:bg-white/[0.06] hover:text-white" title="{{ __('ui.planner_move_up') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" draggable="false" @mousedown.stop @click.stop="moveTask(task.id, 'down')" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] text-slate-300 transition hover:bg-white/[0.06] hover:text-white" title="{{ __('ui.planner_move_down') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" draggable="false" @mousedown.stop @click.stop="removeTask(task.id)" class="inline-flex h-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-4 text-xs font-medium text-slate-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                                        {{ __('ui.delete') }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6" x-show="completedTasks.length">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <p class="user-kicker">{{ __('ui.planner_completed_tasks') }}</p>
                            <p class="text-xs text-slate-500"><span x-text="completedTasks.length"></span> {{ __('ui.planner_items_completed') }}</p>
                        </div>
                        <div class="space-y-3">
                            <template x-for="task in completedTasks" :key="task.id">
                                <div class="flex flex-col gap-3 rounded-3xl border border-emerald-400/10 bg-emerald-400/[0.05] p-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <button type="button" draggable="false" @click.stop="toggleTask(task.id)" class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-400/15 text-emerald-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <div>
                                            <p class="text-sm font-medium text-slate-300 line-through" x-text="task.title"></p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-slate-400" x-text="categoryLabel(task.category)"></span>
                                                <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-emerald-100">{{ __('ui.planner_done') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">{{ __('ui.planner_focus_blocks') }}</p>
                    <h3 class="mt-2 font-outfit text-2xl font-semibold text-white">{{ __('ui.planner_short_session_schedule') }}</h3>
                    <p class="mt-2 text-sm text-slate-300/80">{{ __('ui.planner_short_session_description') }}</p>

                    <div class="mt-6 grid gap-3">
                        <input x-model="newBlockTitle" type="text" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_block_placeholder') }}">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <input x-model.number="newBlockDuration" type="number" min="15" max="120" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white" placeholder="{{ __('ui.planner_duration') }}">
                            <select x-model="newBlockMode" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                                <option value="deep-work">{{ __('ui.planner_mode_deep-work') }}</option>
                                <option value="review">{{ __('ui.planner_mode_review') }}</option>
                                <option value="practice">{{ __('ui.planner_mode_practice') }}</option>
                            </select>
                            <select x-model="newBlockEnergy" class="glass-input h-12 w-full rounded-2xl px-4 text-sm text-white">
                                <option value="prime">{{ __('ui.planner_energy_prime') }}</option>
                                <option value="steady">{{ __('ui.planner_energy_steady') }}</option>
                                <option value="light">{{ __('ui.planner_energy_light') }}</option>
                            </select>
                        </div>
                        <button type="button" @click="addBlock()" class="user-primary-button h-12 rounded-2xl px-5 text-sm font-semibold text-slate-950">
                            {{ __('ui.planner_add_block') }}
                        </button>
                    </div>

                    <div class="mt-6 space-y-3">
                        <template x-if="!activeBlocks.length">
                            <div class="rounded-3xl border border-dashed border-white/10 bg-slate-950/35 px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('ui.planner_no_blocks') }}
                            </div>
                        </template>

                        <template x-for="block in activeBlocks" :key="block.id">
                            <div
                                draggable="true"
                                @dragstart="startBlockDrag(block.id)"
                                @dragend="endBlockDrag()"
                                @dragover.prevent
                                @drop.prevent="reorderBlock(block.id); endBlockDrag()"
                                class="cursor-grab rounded-3xl border border-white/10 bg-slate-950/40 p-4 transition active:cursor-grabbing"
                                :class="draggingBlockId === block.id ? 'scale-[0.99] border-violet-400/25 bg-violet-400/8 opacity-70 shadow-[0_18px_40px_rgba(167,139,250,0.08)]' : ''"
                            >
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <button type="button" draggable="false" @mousedown.stop @click.stop="toggleBlock(block.id)" class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border transition" :class="block.completed ? 'border-cyan-400/40 bg-cyan-400/15 text-cyan-200' : 'border-white/15 bg-white/[0.03] text-slate-500'">
                                            <svg x-show="block.completed" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <div>
                                            <p class="text-sm font-medium" :class="block.completed ? 'text-slate-400 line-through' : 'text-white'" x-text="block.title"></p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-slate-400" x-text="modeLabel(block.mode)"></span>
                                                <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-cyan-100">
                                                    <span x-text="block.duration"></span> {{ __('ui.planner_minutes') }}
                                                </span>
                                                <span class="rounded-full border border-violet-400/20 bg-violet-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-violet-100" x-text="energyLevelLabel(block.energy)"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center">
                                        <button type="button" draggable="false" @mousedown.stop @click.stop="moveBlock(block.id, 'up')" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] text-slate-300 transition hover:bg-white/[0.06] hover:text-white" title="{{ __('ui.planner_move_up') }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                        <button type="button" draggable="false" @mousedown.stop @click.stop="moveBlock(block.id, 'down')" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] text-slate-300 transition hover:bg-white/[0.06] hover:text-white" title="{{ __('ui.planner_move_down') }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <button type="button" draggable="false" @mousedown.stop @click.stop="removeBlock(block.id)" class="inline-flex h-10 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03] px-4 text-xs font-medium text-slate-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                                            {{ __('ui.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6" x-show="completedBlocks.length">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <p class="user-kicker">{{ __('ui.planner_completed_sessions') }}</p>
                            <p class="text-xs text-slate-500"><span x-text="completedBlocks.length"></span> {{ __('ui.planner_sessions_completed') }}</p>
                        </div>
                        <div class="space-y-3">
                            <template x-for="block in completedBlocks" :key="block.id">
                                <div class="rounded-3xl border border-cyan-400/10 bg-cyan-400/[0.05] p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex items-start gap-3">
                                            <button type="button" draggable="false" @click.stop="toggleBlock(block.id)" class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border border-cyan-400/40 bg-cyan-400/15 text-cyan-200">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <div>
                                                <p class="text-sm font-medium text-slate-300 line-through" x-text="block.title"></p>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-slate-400" x-text="modeLabel(block.mode)"></span>
                                                    <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-cyan-100"><span x-text="block.duration"></span> {{ __('ui.planner_minutes') }}</span>
                                                    <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-emerald-100">{{ __('ui.planner_done') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="glass-panel-strong rounded-[30px] p-5 md:p-6">
                    <p class="user-kicker">{{ __('ui.planner_coach') }}</p>
                    <h3 class="mt-2 font-outfit text-xl font-semibold text-white" x-text="planTitle"></h3>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_prime_time') }}</p>
                            <p class="mt-3 text-sm text-white" x-text="energyLabel"></p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_total_estimate') }}</p>
                            <p class="mt-3 text-sm text-white"><span x-text="totalEstimatedSessions"></span> {{ __('ui.planner_sessions_lower') }} {{ __('ui.planner_and') }} <span x-text="totalBlockMinutes"></span> {{ __('ui.planner_minutes') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_max_distractions') }}</p>
                            <p class="mt-3 text-sm text-white"><span x-text="distractionCap"></span> {{ __('ui.planner_major_distractions') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_active_tasks') }}</p>
                            <p class="mt-3 text-sm text-white"><span x-text="activeTasksCount"></span> {{ __('ui.planner_tasks_remaining') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_completed_tasks') }}</p>
                            <p class="mt-3 text-sm text-white"><span x-text="completedTasksCount"></span> {{ __('ui.planner_tasks_completed') }}</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">{{ __('ui.planner_completed_sessions') }}</p>
                            <p class="mt-3 text-sm text-white"><span x-text="completedBlockMinutes"></span> {{ __('ui.planner_of') }} <span x-text="totalBlockMinutes"></span> {{ __('ui.planner_minutes') }}</p>
                        </div>
                    </div>

                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/5">
                        <div class="h-full rounded-full bg-[linear-gradient(90deg,rgba(34,211,238,0.95),rgba(167,139,250,0.95))] transition-all duration-300" :style="`width: ${completionPercent}%`"></div>
                    </div>
                    <div class="mt-5 rounded-3xl border border-cyan-400/15 bg-cyan-400/8 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-100/80">{{ __('ui.planner_recommendation') }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200" x-text="plannerRecommendation"></p>
                    </div>
                    <p class="mt-3 text-sm text-slate-400" x-text="notice"></p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
