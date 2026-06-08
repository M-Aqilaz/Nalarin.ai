import './bootstrap';

import Alpine from 'alpinejs';
import { registerRealtimeChat } from './realtime-chat';

const POMODORO_STORAGE_KEY = 'nalarin-pomodoro-state-v1';
const FOCUS_PLANNER_STORAGE_KEY = 'nalarin-focus-planner-v1';

const clamp = (value, min, max) => {
    const number = Number(value);

    if (!Number.isFinite(number)) {
        return min;
    }

    return Math.min(max, Math.max(min, Math.round(number)));
};

const formatDateKey = (date = new Date()) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const createLocalId = () => `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

const parseStoredJson = (storageKey) => {
    const rawValue = window.localStorage.getItem(storageKey);

    if (!rawValue) {
        return null;
    }

    try {
        return JSON.parse(rawValue);
    } catch (error) {
        window.localStorage.removeItem(storageKey);

        return null;
    }
};

const initPageLoader = () => {
    const loader = document.querySelector('[data-page-loader]');
    let safetyTimer = null;

    if (!loader) {
        return;
    }

    const showLoader = () => {
        if (safetyTimer) {
            window.clearTimeout(safetyTimer);
        }

        loader.classList.add('is-active');
        safetyTimer = window.setTimeout(hideLoader, 8000);
    };

    const hideLoader = () => {
        if (safetyTimer) {
            window.clearTimeout(safetyTimer);
            safetyTimer = null;
        }

        loader.classList.remove('is-active');
    };

    window.addEventListener('beforeunload', showLoader);
    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', () => {
        window.setTimeout(hideLoader, 180);
    });

    window.showPageLoader = showLoader;
    window.hidePageLoader = hideLoader;
};

initPageLoader();

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    registerRealtimeChat(Alpine);

    Alpine.data('pomodoroTimer', () => ({
        storageKey: POMODORO_STORAGE_KEY,
        cycleTarget: 4,
        durations: {
            focus: 25,
            shortBreak: 5,
            longBreak: 15,
        },
        mode: 'focus',
        remainingSeconds: 25 * 60,
        isRunning: false,
        endsAt: null,
        timerId: null,
        completedFocusSessions: 0,
        dailyProgressDate: formatDateKey(),
        notice: 'Siap untuk memulai sesi fokus.',

        init() {
            this.restoreState();
            this.syncDailyProgress();

            if (this.isRunning && this.endsAt) {
                if (this.secondsUntilEnd() <= 0) {
                    this.completeCurrentMode();
                    return;
                }

                this.startTicker();
                this.tick();
            } else if (!this.remainingSeconds) {
                this.remainingSeconds = this.durationFor(this.mode);
            }
        },

        get formattedTime() {
            const minutes = String(Math.floor(this.remainingSeconds / 60)).padStart(2, '0');
            const seconds = String(this.remainingSeconds % 60).padStart(2, '0');

            return `${minutes}:${seconds}`;
        },

        get currentModeLabel() {
            return {
                focus: 'Fokus',
                shortBreak: 'Istirahat Pendek',
                longBreak: 'Istirahat Panjang',
            }[this.mode];
        },

        get currentModeDescription() {
            return {
                focus: 'Satu sesi fokus penuh tanpa distraksi.',
                shortBreak: 'Jeda singkat untuk rehat sebentar.',
                longBreak: 'Waktu recharge yang lebih panjang setelah 4 sesi.',
            }[this.mode];
        },

        get progressPercent() {
            const totalSeconds = this.durationFor(this.mode);

            if (!totalSeconds) {
                return 0;
            }

            return ((totalSeconds - this.remainingSeconds) / totalSeconds) * 100;
        },

        get primaryActionLabel() {
            if (this.isRunning) {
                return 'Jeda';
            }

            return this.remainingSeconds < this.durationFor(this.mode) ? 'Lanjutkan' : 'Mulai';
        },

        get dailyProgressPercent() {
            return Math.min(100, (this.completedFocusSessions / this.cycleTarget) * 100);
        },

        get completedCycleSessions() {
            if (this.completedFocusSessions <= 0) {
                return 0;
            }

            const completedInCycle = this.completedFocusSessions % this.cycleTarget;

            return completedInCycle === 0 ? this.cycleTarget : completedInCycle;
        },

        get sessionsUntilLongBreak() {
            const completedInCycle = this.completedFocusSessions % this.cycleTarget;

            return completedInCycle === 0 ? this.cycleTarget : this.cycleTarget - completedInCycle;
        },

        get upcomingModeLabel() {
            if (this.mode !== 'focus') {
                return 'Fokus';
            }

            const completedAfterCurrent = this.completedFocusSessions + 1;

            return completedAfterCurrent % this.cycleTarget === 0 ? 'Istirahat Panjang' : 'Istirahat Pendek';
        },

        get timerStatusLabel() {
            if (this.isRunning) {
                return 'Sedang berjalan';
            }

            return this.remainingSeconds < this.durationFor(this.mode) ? 'Dijeda' : 'Siap dimulai';
        },

        durationFor(mode) {
            return (this.durations[mode] ?? 0) * 60;
        },

        syncDailyProgress() {
            const today = formatDateKey();

            if (this.dailyProgressDate === today) {
                return;
            }

            this.dailyProgressDate = today;
            this.completedFocusSessions = 0;

            if (!this.isRunning) {
                this.notice = 'Hari baru dimulai. Target fokus direset.';
            }

            this.persistState();
        },

        selectMode(mode) {
            this.stopTicker();
            this.mode = mode;
            this.isRunning = false;
            this.endsAt = null;
            this.remainingSeconds = this.durationFor(mode);
            this.notice = `${this.currentModeLabel} siap dimulai.`;
            this.persistState();
        },

        toggleTimer() {
            this.syncDailyProgress();

            if (this.isRunning) {
                this.pauseTimer();
                return;
            }

            this.startTimer();
        },

        startTimer() {
            if (this.remainingSeconds <= 0) {
                this.remainingSeconds = this.durationFor(this.mode);
            }

            this.isRunning = true;
            this.endsAt = Date.now() + (this.remainingSeconds * 1000);
            this.notice = `${this.currentModeLabel} sedang berjalan.`;
            this.startTicker();
            this.persistState();
        },

        pauseTimer() {
            this.remainingSeconds = this.secondsUntilEnd();
            this.isRunning = false;
            this.endsAt = null;
            this.stopTicker();
            this.notice = 'Timer dijeda. Lanjutkan kapan saja.';
            this.persistState();
        },

        resetTimer() {
            this.stopTicker();
            this.isRunning = false;
            this.endsAt = null;
            this.remainingSeconds = this.durationFor(this.mode);
            this.notice = `${this.currentModeLabel} direset ke awal.`;
            this.persistState();
        },

        skipMode() {
            this.stopTicker();
            this.isRunning = false;
            this.endsAt = null;
            this.moveToNextMode(true);
        },

        startTicker() {
            this.stopTicker();
            this.timerId = window.setInterval(() => this.tick(), 250);
        },

        stopTicker() {
            if (!this.timerId) {
                return;
            }

            window.clearInterval(this.timerId);
            this.timerId = null;
        },

        secondsUntilEnd() {
            if (!this.endsAt) {
                return this.remainingSeconds;
            }

            return Math.max(0, Math.ceil((this.endsAt - Date.now()) / 1000));
        },

        tick() {
            if (!this.isRunning) {
                return;
            }

            this.remainingSeconds = this.secondsUntilEnd();

            if (this.remainingSeconds <= 0) {
                this.completeCurrentMode();
                return;
            }

            this.persistState();
        },

        completeCurrentMode() {
            this.stopTicker();
            this.isRunning = false;
            this.endsAt = null;
            this.remainingSeconds = 0;
            this.moveToNextMode(false);
        },

        moveToNextMode(skipped = false) {
            const finishedMode = this.mode;

            if (finishedMode === 'focus') {
                if (!skipped) {
                    this.syncDailyProgress();
                    this.completedFocusSessions += 1;
                }

                const nextMode = !skipped && this.completedFocusSessions > 0 && this.completedFocusSessions % this.cycleTarget === 0
                    ? 'longBreak'
                    : 'shortBreak';

                this.mode = nextMode;
                this.remainingSeconds = this.durationFor(nextMode);
                this.notice = skipped
                    ? 'Sesi fokus dilewati. Kamu bisa mulai ulang atau ambil jeda.'
                    : 'Sesi fokus selesai. Saatnya istirahat.';
            } else {
                this.mode = 'focus';
                this.remainingSeconds = this.durationFor('focus');
                this.notice = skipped
                    ? 'Istirahat dilewati. Waktunya kembali fokus.'
                    : 'Istirahat selesai. Siap masuk sesi fokus berikutnya.';
            }

            this.persistState();
        },

        updateDuration(mode, value) {
            const limits = {
                focus: [10, 90],
                shortBreak: [1, 30],
                longBreak: [5, 60],
            };
            const [min, max] = limits[mode];

            this.durations[mode] = clamp(value, min, max);

            if (!this.isRunning && this.mode === mode) {
                this.remainingSeconds = this.durationFor(mode);
            }

            this.notice = `Durasi ${{
                focus: 'fokus',
                shortBreak: 'istirahat pendek',
                longBreak: 'istirahat panjang',
            }[mode]} diperbarui.`;
            this.persistState();
        },

        persistState() {
            const payload = {
                durations: this.durations,
                mode: this.mode,
                remainingSeconds: this.remainingSeconds,
                isRunning: this.isRunning,
                endsAt: this.endsAt,
                completedFocusSessions: this.completedFocusSessions,
                dailyProgressDate: this.dailyProgressDate,
                notice: this.notice,
            };

            window.localStorage.setItem(this.storageKey, JSON.stringify(payload));
        },

        restoreState() {
            const savedState = window.localStorage.getItem(this.storageKey);

            if (!savedState) {
                return;
            }

            try {
                const parsedState = JSON.parse(savedState);

                this.durations = {
                    focus: clamp(parsedState?.durations?.focus, 10, 90),
                    shortBreak: clamp(parsedState?.durations?.shortBreak, 1, 30),
                    longBreak: clamp(parsedState?.durations?.longBreak, 5, 60),
                };
                this.mode = ['focus', 'shortBreak', 'longBreak'].includes(parsedState?.mode)
                    ? parsedState.mode
                    : 'focus';
                this.remainingSeconds = Number.isFinite(parsedState?.remainingSeconds)
                    ? Math.max(0, Math.round(parsedState.remainingSeconds))
                    : this.durationFor(this.mode);
                this.isRunning = Boolean(parsedState?.isRunning);
                this.endsAt = Number.isFinite(parsedState?.endsAt) ? parsedState.endsAt : null;
                this.completedFocusSessions = Number.isFinite(parsedState?.completedFocusSessions)
                    ? Math.max(0, Math.round(parsedState.completedFocusSessions))
                    : 0;
                this.dailyProgressDate = parsedState?.dailyProgressDate || formatDateKey();
                this.notice = parsedState?.notice || this.notice;
            } catch (error) {
                window.localStorage.removeItem(this.storageKey);
            }
        },
    }));

    Alpine.data('focusPlanner', (options = {}) => ({
        storageKey: FOCUS_PLANNER_STORAGE_KEY,
        translations: options.translations || {},
        presetAliases: options.presetAliases || [],
        planTitle: options.translations?.defaultPlanTitle || 'Sprint Fokus Hari Ini',
        targetSessions: 4,
        energyLabel: options.translations?.defaultEnergyLabel || 'Prime time malam',
        distractionCap: 2,
        tasks: [],
        blocks: [],
        draggingTaskId: null,
        draggingBlockId: null,
        newTaskTitle: '',
        newTaskEstimate: 1,
        newTaskPriority: 'high',
        newTaskCategory: 'concept',
        newBlockTitle: '',
        newBlockDuration: 50,
        newBlockMode: 'deep-work',
        newBlockEnergy: 'prime',
        notice: options.translations?.defaultNotice || 'Susun prioritas singkat dan blok belajar yang realistis.',

        t(key, fallback = '') {
            return this.translations[key] || fallback;
        },

        localizePresetText(value) {
            const preset = this.presetAliases.find((item) => item.aliases.includes(value));

            return preset?.value || value;
        },

        priorityLabel(priority) {
            return this.t(`priority_${priority}`, priority);
        },

        categoryLabel(category) {
            return this.t(`category_${category}`, category);
        },

        modeLabel(mode) {
            return this.t(`mode_${mode}`, mode);
        },

        energyLevelLabel(energy) {
            return this.t(`energy_${energy}`, energy);
        },

        init() {
            this.restoreState();

            if (!this.tasks.length && !this.blocks.length) {
                this.tasks = [
                    { id: createLocalId(), title: this.t('defaultTaskSummary'), estimate: 1, priority: 'high', category: 'concept', completed: false },
                    { id: createLocalId(), title: this.t('defaultTaskQuiz'), estimate: 1, priority: 'medium', category: 'practice', completed: false },
                ];
                this.blocks = [
                    { id: createLocalId(), title: this.t('defaultBlockCore'), duration: 50, mode: 'deep-work', energy: 'prime', completed: false },
                    { id: createLocalId(), title: this.t('defaultBlockFlashcard'), duration: 20, mode: 'review', energy: 'steady', completed: false },
                ];
                this.persistState();
            }
        },

        get completedTasksCount() {
            return this.tasks.filter((task) => task.completed).length;
        },

        get activeTasks() {
            return this.tasks.filter((task) => !task.completed);
        },

        get completedTasks() {
            return this.tasks.filter((task) => task.completed);
        },

        get activeTasksCount() {
            return this.activeTasks.length;
        },

        get totalEstimatedSessions() {
            return this.tasks.reduce((total, task) => total + task.estimate, 0);
        },

        get highPriorityTasksCount() {
            return this.tasks.filter((task) => task.priority === 'high' && !task.completed).length;
        },

        get totalBlockMinutes() {
            return this.blocks.reduce((total, block) => total + block.duration, 0);
        },

        get activeBlocks() {
            return this.blocks.filter((block) => !block.completed);
        },

        get completedBlocks() {
            return this.blocks.filter((block) => block.completed);
        },

        get activeBlockMinutes() {
            return this.activeBlocks.reduce((total, block) => total + block.duration, 0);
        },

        get completedBlockMinutes() {
            return this.blocks.filter((block) => block.completed).reduce((total, block) => total + block.duration, 0);
        },

        get focusLoadLabel() {
            if (this.totalEstimatedSessions >= this.targetSessions + 3 || this.totalBlockMinutes >= 220) {
                return this.t('focusLoadHeavy');
            }

            if (this.totalEstimatedSessions >= this.targetSessions || this.totalBlockMinutes >= 140) {
                return this.t('focusLoadBalanced');
            }

            return this.t('focusLoadLight');
        },

        get plannerHealthLabel() {
            if (this.highPriorityTasksCount > 3) {
                return this.t('planHealthOverplanned');
            }

            if (!this.tasks.length || !this.blocks.length) {
                return this.t('planHealthIncomplete');
            }

            return this.t('planHealthFocused');
        },

        get readinessPercent() {
            let score = 36;

            if (this.tasks.length >= 2) score += 16;
            if (this.blocks.length >= 2) score += 16;
            if (this.highPriorityTasksCount <= 3) score += 14;
            if (this.totalBlockMinutes >= 60 && this.totalBlockMinutes <= 180) score += 10;
            if (this.totalEstimatedSessions <= this.targetSessions + 2) score += 8;

            return Math.min(100, score);
        },

        get readinessLabel() {
            if (this.readinessPercent >= 85) {
                return this.t('readinessReady');
            }

            if (this.readinessPercent >= 65) {
                return this.t('readinessAlmost');
            }

            return this.t('readinessNeedsWork');
        },

        get plannerRecommendation() {
            if (!this.activeTasksCount && !this.activeBlocks.length && (this.completedTasksCount || this.completedBlocks.length)) {
                return this.t('recommendationComplete');
            }

            if (this.highPriorityTasksCount > 3) {
                return this.t('recommendationPriorities');
            }

            if (this.activeBlockMinutes > 200) {
                return this.t('recommendationBlocks');
            }

            if (this.activeTasksCount && this.activeTasks.every((task) => task.priority !== 'high')) {
                return this.t('recommendationHighPriority');
            }

            if ((this.activeTasks.reduce((total, task) => total + task.estimate, 0)) < this.targetSessions && this.activeBlocks.length < 2) {
                return this.t('recommendationAddItem');
            }

            return this.t('recommendationHealthy');
        },

        get completionPercent() {
            const totalItems = this.tasks.length + this.blocks.length;

            if (!totalItems) {
                return 0;
            }

            return Math.round(((this.completedTasksCount + this.blocks.filter((block) => block.completed).length) / totalItems) * 100);
        },

        addTask() {
            const title = this.newTaskTitle.trim();

            if (!title) {
                this.notice = this.t('noticeTaskTitle');
                return;
            }

            this.tasks.unshift({
                id: createLocalId(),
                title,
                estimate: clamp(this.newTaskEstimate, 1, 6),
                priority: ['high', 'medium', 'low'].includes(this.newTaskPriority) ? this.newTaskPriority : 'high',
                category: ['concept', 'practice', 'review', 'project'].includes(this.newTaskCategory) ? this.newTaskCategory : 'concept',
                completed: false,
            });

            this.newTaskTitle = '';
            this.newTaskEstimate = 1;
            this.newTaskPriority = 'high';
            this.newTaskCategory = 'concept';
            this.notice = this.t('noticeTaskAdded');
            this.persistState();
        },

        toggleTask(taskId) {
            this.tasks = this.tasks.map((task) => task.id === taskId ? { ...task, completed: !task.completed } : task);
            this.notice = this.t('noticeTaskUpdated');
            this.persistState();
        },

        removeTask(taskId) {
            this.tasks = this.tasks.filter((task) => task.id !== taskId);
            this.notice = this.t('noticeTaskRemoved');
            this.persistState();
        },

        startTaskDrag(taskId) {
            this.draggingTaskId = taskId;
        },

        endTaskDrag() {
            this.draggingTaskId = null;
        },

        moveTask(taskId, direction) {
            const currentIndex = this.tasks.findIndex((task) => task.id === taskId);

            if (currentIndex < 0) {
                return;
            }

            const targetIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;

            if (targetIndex < 0 || targetIndex >= this.tasks.length) {
                return;
            }

            const reorderedTasks = [...this.tasks];
            const [task] = reorderedTasks.splice(currentIndex, 1);
            reorderedTasks.splice(targetIndex, 0, task);
            this.tasks = reorderedTasks;
            this.notice = this.t('noticeTaskReordered');
            this.persistState();
        },

        reorderTask(targetTaskId) {
            if (!this.draggingTaskId || this.draggingTaskId === targetTaskId) {
                return;
            }

            const sourceIndex = this.tasks.findIndex((task) => task.id === this.draggingTaskId);
            const targetIndex = this.tasks.findIndex((task) => task.id === targetTaskId);

            if (sourceIndex < 0 || targetIndex < 0) {
                return;
            }

            const reorderedTasks = [...this.tasks];
            const [task] = reorderedTasks.splice(sourceIndex, 1);
            reorderedTasks.splice(targetIndex, 0, task);
            this.tasks = reorderedTasks;
            this.notice = this.t('noticeTaskReordered');
            this.persistState();
        },

        addBlock() {
            const title = this.newBlockTitle.trim();

            if (!title) {
                this.notice = this.t('noticeBlockTitle');
                return;
            }

            this.blocks.unshift({
                id: createLocalId(),
                title,
                duration: clamp(this.newBlockDuration, 15, 120),
                mode: ['deep-work', 'review', 'practice'].includes(this.newBlockMode) ? this.newBlockMode : 'deep-work',
                energy: ['prime', 'steady', 'light'].includes(this.newBlockEnergy) ? this.newBlockEnergy : 'prime',
                completed: false,
            });

            this.newBlockTitle = '';
            this.newBlockDuration = 50;
            this.newBlockMode = 'deep-work';
            this.newBlockEnergy = 'prime';
            this.notice = this.t('noticeBlockAdded');
            this.persistState();
        },

        toggleBlock(blockId) {
            this.blocks = this.blocks.map((block) => block.id === blockId ? { ...block, completed: !block.completed } : block);
            this.notice = this.t('noticeBlockUpdated');
            this.persistState();
        },

        removeBlock(blockId) {
            this.blocks = this.blocks.filter((block) => block.id !== blockId);
            this.notice = this.t('noticeBlockRemoved');
            this.persistState();
        },

        startBlockDrag(blockId) {
            this.draggingBlockId = blockId;
        },

        endBlockDrag() {
            this.draggingBlockId = null;
        },

        moveBlock(blockId, direction) {
            const currentIndex = this.blocks.findIndex((block) => block.id === blockId);

            if (currentIndex < 0) {
                return;
            }

            const targetIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;

            if (targetIndex < 0 || targetIndex >= this.blocks.length) {
                return;
            }

            const reorderedBlocks = [...this.blocks];
            const [block] = reorderedBlocks.splice(currentIndex, 1);
            reorderedBlocks.splice(targetIndex, 0, block);
            this.blocks = reorderedBlocks;
            this.notice = this.t('noticeBlockReordered');
            this.persistState();
        },

        reorderBlock(targetBlockId) {
            if (!this.draggingBlockId || this.draggingBlockId === targetBlockId) {
                return;
            }

            const sourceIndex = this.blocks.findIndex((block) => block.id === this.draggingBlockId);
            const targetIndex = this.blocks.findIndex((block) => block.id === targetBlockId);

            if (sourceIndex < 0 || targetIndex < 0) {
                return;
            }

            const reorderedBlocks = [...this.blocks];
            const [block] = reorderedBlocks.splice(sourceIndex, 1);
            reorderedBlocks.splice(targetIndex, 0, block);
            this.blocks = reorderedBlocks;
            this.notice = this.t('noticeBlockReordered');
            this.persistState();
        },

        resetPlanner() {
            this.planTitle = this.t('defaultPlanTitle');
            this.targetSessions = 4;
            this.energyLabel = this.t('defaultEnergyLabel');
            this.distractionCap = 2;
            this.tasks = [];
            this.blocks = [];
            this.notice = this.t('noticeReset');
            this.persistState();
        },

        applyTemplate(template) {
            const templates = {
                exam: {
                    planTitle: this.t('examPlanTitle'),
                    targetSessions: 5,
                    energyLabel: this.t('morningPrimeTime'),
                    distractionCap: 1,
                    tasks: [
                        { title: this.t('examTaskWeakTopic'), estimate: 2, priority: 'high', category: 'concept' },
                        { title: this.t('examTaskPractice'), estimate: 2, priority: 'high', category: 'practice' },
                    ],
                    blocks: [
                        { title: this.t('examBlockConcept'), duration: 50, mode: 'deep-work', energy: 'prime' },
                        { title: this.t('examBlockPractice'), duration: 30, mode: 'practice', energy: 'steady' },
                    ],
                },
                revision: {
                    planTitle: this.t('revisionPlanTitle'),
                    targetSessions: 4,
                    energyLabel: this.t('defaultEnergyLabel'),
                    distractionCap: 2,
                    tasks: [
                        { title: this.t('revisionTaskNotes'), estimate: 1, priority: 'high', category: 'review' },
                        { title: this.t('revisionTaskFlashcard'), estimate: 1, priority: 'medium', category: 'review' },
                    ],
                    blocks: [
                        { title: this.t('revisionBlockSummary'), duration: 25, mode: 'review', energy: 'light' },
                        { title: this.t('revisionBlockDifficult'), duration: 45, mode: 'deep-work', energy: 'prime' },
                    ],
                },
                project: {
                    planTitle: this.t('projectPlanTitle'),
                    targetSessions: 6,
                    energyLabel: this.t('afternoonPrimeTime'),
                    distractionCap: 1,
                    tasks: [
                        { title: this.t('projectTaskBreakdown'), estimate: 1, priority: 'high', category: 'project' },
                        { title: this.t('projectTaskCore'), estimate: 3, priority: 'high', category: 'project' },
                    ],
                    blocks: [
                        { title: this.t('projectBlockImplementation'), duration: 60, mode: 'deep-work', energy: 'prime' },
                        { title: this.t('projectBlockReview'), duration: 30, mode: 'review', energy: 'steady' },
                    ],
                },
            };

            const selectedTemplate = templates[template];

            if (!selectedTemplate) {
                return;
            }

            this.planTitle = selectedTemplate.planTitle;
            this.targetSessions = selectedTemplate.targetSessions;
            this.energyLabel = selectedTemplate.energyLabel;
            this.distractionCap = selectedTemplate.distractionCap;
            this.tasks = selectedTemplate.tasks.map((task) => ({
                id: createLocalId(),
                title: task.title,
                estimate: task.estimate,
                priority: task.priority,
                category: task.category,
                completed: false,
            }));
            this.blocks = selectedTemplate.blocks.map((block) => ({
                id: createLocalId(),
                title: block.title,
                duration: block.duration,
                mode: block.mode,
                energy: block.energy,
                completed: false,
            }));
            this.notice = this.t('noticeTemplateApplied');
            this.persistState();
        },

        persistState() {
            const payload = {
                planTitle: this.planTitle.trim() || this.t('defaultPlanTitle'),
                targetSessions: clamp(this.targetSessions, 1, 12),
                energyLabel: this.energyLabel.trim() || this.t('defaultEnergyLabel'),
                distractionCap: clamp(this.distractionCap, 0, 8),
                tasks: this.tasks.map((task) => ({
                    id: task.id,
                    title: task.title,
                    estimate: clamp(task.estimate, 1, 6),
                    priority: ['high', 'medium', 'low'].includes(task.priority) ? task.priority : 'high',
                    category: ['concept', 'practice', 'review', 'project'].includes(task.category) ? task.category : 'concept',
                    completed: Boolean(task.completed),
                })),
                blocks: this.blocks.map((block) => ({
                    id: block.id,
                    title: block.title,
                    duration: clamp(block.duration, 15, 120),
                    mode: ['deep-work', 'review', 'practice'].includes(block.mode) ? block.mode : 'deep-work',
                    energy: ['prime', 'steady', 'light'].includes(block.energy) ? block.energy : 'prime',
                    completed: Boolean(block.completed),
                })),
                notice: this.notice,
            };

            this.planTitle = payload.planTitle;
            this.targetSessions = payload.targetSessions;
            this.energyLabel = payload.energyLabel;
            this.distractionCap = payload.distractionCap;
            window.localStorage.setItem(this.storageKey, JSON.stringify(payload));
        },

        restoreState() {
            const savedState = parseStoredJson(this.storageKey);

            if (!savedState) {
                return;
            }

            this.planTitle = typeof savedState.planTitle === 'string' && savedState.planTitle.trim()
                ? this.localizePresetText(savedState.planTitle.trim())
                : this.planTitle;
            this.targetSessions = clamp(savedState.targetSessions, 1, 12);
            this.energyLabel = typeof savedState.energyLabel === 'string' && savedState.energyLabel.trim()
                ? this.localizePresetText(savedState.energyLabel.trim())
                : this.energyLabel;
            this.distractionCap = clamp(savedState.distractionCap, 0, 8);
            this.tasks = Array.isArray(savedState.tasks)
                ? savedState.tasks.map((task) => ({
                    id: task.id || createLocalId(),
                    title: this.localizePresetText(String(task.title || '').trim()),
                    estimate: clamp(task.estimate, 1, 6),
                    priority: ['high', 'medium', 'low'].includes(task.priority) ? task.priority : 'high',
                    category: ['concept', 'practice', 'review', 'project'].includes(task.category) ? task.category : 'concept',
                    completed: Boolean(task.completed),
                })).filter((task) => task.title)
                : [];
            this.blocks = Array.isArray(savedState.blocks)
                ? savedState.blocks.map((block) => ({
                    id: block.id || createLocalId(),
                    title: this.localizePresetText(String(block.title || '').trim()),
                    duration: clamp(block.duration, 15, 120),
                    mode: ['deep-work', 'review', 'practice'].includes(block.mode) ? block.mode : 'deep-work',
                    energy: ['prime', 'steady', 'light'].includes(block.energy) ? block.energy : 'prime',
                    completed: Boolean(block.completed),
                })).filter((block) => block.title)
                : [];
            this.notice = this.t('defaultNotice');
        },
    }));

    Alpine.data('focusInsights', (options = {}) => ({
        pomodoroKey: POMODORO_STORAGE_KEY,
        plannerKey: FOCUS_PLANNER_STORAGE_KEY,
        locale: options.locale || 'id',
        translations: options.translations || {},
        pomodoro: null,
        planner: null,
        lastSyncLabel: '',

        t(key, fallback = '') {
            return this.translations[key] || fallback;
        },

        modeLabel(mode) {
            return this.t(`mode_${mode}`, mode);
        },

        init() {
            this.refresh();
            window.addEventListener('storage', () => this.refresh());
        },

        get completedFocusSessions() {
            return Number.isFinite(this.pomodoro?.completedFocusSessions)
                ? Math.max(0, Math.round(this.pomodoro.completedFocusSessions))
                : 0;
        },

        get cycleTarget() {
            return Number.isFinite(this.pomodoro?.cycleTarget)
                ? Math.max(1, Math.round(this.pomodoro.cycleTarget))
                : 4;
        },

        get progressPercent() {
            return Math.min(100, Math.round((this.completedFocusSessions / this.cycleTarget) * 100));
        },

        get taskCompletionPercent() {
            if (!this.taskCount) {
                return 0;
            }

            return Math.round((this.completedTaskCount / this.taskCount) * 100);
        },

        get blockCompletionPercent() {
            if (!this.blockCount) {
                return 0;
            }

            return Math.round((this.completedBlockCount / this.blockCount) * 100);
        },

        get taskCount() {
            return Array.isArray(this.planner?.tasks) ? this.planner.tasks.length : 0;
        },

        get completedTaskCount() {
            return Array.isArray(this.planner?.tasks)
                ? this.planner.tasks.filter((task) => task.completed).length
                : 0;
        },

        get blockCount() {
            return Array.isArray(this.planner?.blocks) ? this.planner.blocks.length : 0;
        },

        get completedBlockCount() {
            return Array.isArray(this.planner?.blocks)
                ? this.planner.blocks.filter((block) => block.completed).length
                : 0;
        },

        get plannedMinutes() {
            return Array.isArray(this.planner?.blocks)
                ? this.planner.blocks.reduce((total, block) => total + clamp(block.duration, 15, 120), 0)
                : 0;
        },

        get completedMinutes() {
            return Array.isArray(this.planner?.blocks)
                ? this.planner.blocks
                    .filter((block) => block.completed)
                    .reduce((total, block) => total + clamp(block.duration, 15, 120), 0)
                : 0;
        },

        get completedTaskSessions() {
            return Array.isArray(this.planner?.tasks)
                ? this.planner.tasks
                    .filter((task) => task.completed)
                    .reduce((total, task) => total + clamp(task.estimate, 1, 6), 0)
                : 0;
        },

        get totalPlannedSessions() {
            return Array.isArray(this.planner?.tasks)
                ? this.planner.tasks.reduce((total, task) => total + clamp(task.estimate, 1, 6), 0)
                : 0;
        },

        get focusScore() {
            const sessionScore = this.progressPercent * 0.35;
            const taskScore = this.taskCompletionPercent * 0.35;
            const blockScore = this.blockCompletionPercent * 0.2;
            const minuteScore = this.plannedMinutes ? Math.min(100, Math.round((this.completedMinutes / this.plannedMinutes) * 100)) * 0.1 : 0;

            return Math.round(sessionScore + taskScore + blockScore + minuteScore);
        },

        get focusScoreLabel() {
            if (this.focusScore >= 80) {
                return this.t('scoreVeryStable');
            }

            if (this.focusScore >= 60) {
                return this.t('scoreStable');
            }

            if (this.focusScore >= 40) {
                return this.t('scoreUnstable');
            }

            return this.t('scoreNotFormed');
        },

        get strongestMode() {
            if (!Array.isArray(this.planner?.blocks) || !this.planner.blocks.length) {
                return this.t('noMode');
            }

            const modeCount = this.planner.blocks.reduce((result, block) => {
                const key = ['deep-work', 'review', 'practice'].includes(block.mode) ? block.mode : 'deep-work';
                result[key] = (result[key] || 0) + 1;
                return result;
            }, {});

            return this.modeLabel(Object.entries(modeCount).sort((a, b) => b[1] - a[1])[0][0]);
        },

        get coachMessage() {
            if (this.focusScore >= 80) {
                return this.t('coachStrong');
            }

            if (this.completedFocusSessions < Math.max(1, Math.ceil(this.cycleTarget / 2))) {
                return this.t('coachMomentum');
            }

            if (this.taskCompletionPercent < this.blockCompletionPercent) {
                return this.t('coachOutput');
            }

            return this.t('coachConsistency');
        },

        get recommendation() {
            if (this.completedFocusSessions >= this.cycleTarget) {
                return this.t('recommendationTargetReached');
            }

            if (this.completedTaskCount < this.taskCount) {
                return this.t('recommendationFinishPriority');
            }

            if (this.completedMinutes < this.plannedMinutes) {
                return this.t('recommendationContinueBlock');
            }

            return this.t('recommendationStartPomodoro');
        },

        refresh() {
            this.pomodoro = parseStoredJson(this.pomodoroKey);
            this.planner = parseStoredJson(this.plannerKey);
            this.lastSyncLabel = new Intl.DateTimeFormat(this.locale === 'en' ? 'en-US' : 'id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            }).format(new Date());
        },
    }));
});

Alpine.start();
