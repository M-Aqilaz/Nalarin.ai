import './bootstrap';

import Alpine from 'alpinejs';

const POMODORO_STORAGE_KEY = 'pelajarin-pomodoro-state-v1';

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

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
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
});

Alpine.start();
