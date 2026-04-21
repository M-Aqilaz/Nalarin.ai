const POLL_INTERVAL = 2000;

const normalizeMessages = (messages = []) => {
    return [...messages]
        .filter((message) => message && message.id)
        .map((message) => ({
            ...message,
            id: Number(message.id),
        }))
        .sort((left, right) => left.id - right.id);
};

const buildTypingText = (names = []) => {
    if (names.length === 0) {
        return '';
    }

    if (names.length === 1) {
        return `${names[0]} sedang mengetik...`;
    }

    if (names.length === 2) {
        return `${names[0]} dan ${names[1]} sedang mengetik...`;
    }

    return `${names[0]}, ${names[1]}, dan lainnya sedang mengetik...`;
};

const timestampLabel = () => new Date().toLocaleTimeString();

const extractErrorMessage = (error) => {
    const message = error?.response?.data?.message;

    if (typeof message === 'string' && message.trim() !== '') {
        return message;
    }

    const validationMessage = Object.values(error?.response?.data?.errors ?? {})[0]?.[0];

    if (typeof validationMessage === 'string' && validationMessage.trim() !== '') {
        return validationMessage;
    }

    return 'Permintaan gagal diproses. Coba lagi.';
};

const buildBaseChat = (options) => {
    const {
        initialMessages,
        sendUrl,
        pollUrl,
        channelName,
        typingUrl,
        currentUserId,
        currentUserName,
        enableTyping,
    } = options;

    return {
    messages: normalizeMessages(initialMessages),
    sendUrl,
    pollUrl,
    channelName,
    typingUrl,
    form: {
        content: '',
    },
    channel: null,
    pollTimer: null,
    isPolling: false,
    isSubmitting: false,
    booted: false,
    error: null,
    connectionState: 'Menghubungkan realtime...',
    currentUserId: currentUserId ?? null,
    currentUserName: currentUserName ?? null,
    enableTyping: Boolean(enableTyping ?? false),
    typingUsers: {},
    typingTimers: {},
    typingCooldown: null,
    typingText: '',

    init() {
        this.booted = true;
        this.enablePolling(this.connectionState);
        this.connectRealtime();
        this.$nextTick(() => this.scrollToBottom());
    },

    bubbleClasses(message, currentUserId = null) {
        if (message.role === 'user' || (currentUserId !== null && Number(message.user_id) === Number(currentUserId))) {
            return 'ml-auto bg-purple-600/20 border-purple-500/20';
        }

        return 'mr-auto bg-white/5 border-white/10';
    },

    connectRealtime() {
        if (!window.Echo) {
            this.enablePolling('Realtime belum tersedia. Polling otomatis aktif.');
            return;
        }

        try {
            this.channel = window.Echo.private(this.channelName);
            this.channel.listen('.message.created', (event) => {
                this.upsertMessage(event.message);
            });
            this.channel.listen('.typing.updated', (event) => {
                this.receiveTyping(event.typing);
            });

            if (typeof this.registerExtraListeners === 'function') {
                this.registerExtraListeners(this.channel);
            }

            const connection = window.Echo.connector?.pusher?.connection;

            if (!connection) {
                this.enablePolling('Realtime tidak stabil. Polling otomatis aktif.');
                return;
            }

            const syncState = (state) => {
                if (state === 'connected') {
                    this.disablePolling('Realtime aktif.');
                    return;
                }

                if (state === 'connecting' || state === 'initialized') {
                    this.connectionState = 'Menghubungkan realtime...';
                    return;
                }

                this.enablePolling('Realtime terputus. Polling otomatis aktif.');
            };

            connection.bind('state_change', ({ current }) => syncState(current));
            window.setTimeout(() => syncState(connection.state), 1000);
        } catch (error) {
            this.enablePolling('Realtime gagal dimuat. Polling otomatis aktif.');
        }
    },

    notifyTyping() {
        if (!this.enableTyping || !this.typingUrl || !this.currentUserId || !this.currentUserName) {
            return;
        }

        if (this.typingCooldown) {
            return;
        }

        window.axios.post(this.typingUrl, {}, {
            headers: {
                Accept: 'application/json',
            },
        }).catch(() => {});

        this.typingCooldown = window.setTimeout(() => {
            this.typingCooldown = null;
        }, 900);
    },

    receiveTyping(event) {
        if (!this.enableTyping || !event?.user_id || Number(event.user_id) === Number(this.currentUserId)) {
            return;
        }

        const userKey = String(event.user_id);

        this.typingUsers = {
            ...this.typingUsers,
            [userKey]: event.user_name || 'Seseorang',
        };
        this.typingText = buildTypingText(Object.values(this.typingUsers));

        if (this.typingTimers[userKey]) {
            window.clearTimeout(this.typingTimers[userKey]);
        }

        this.typingTimers[userKey] = window.setTimeout(() => {
            const nextTypingUsers = { ...this.typingUsers };
            delete nextTypingUsers[userKey];
            this.typingUsers = nextTypingUsers;
            this.typingText = buildTypingText(Object.values(this.typingUsers));

            const nextTimers = { ...this.typingTimers };
            delete nextTimers[userKey];
            this.typingTimers = nextTimers;
        }, 2600);
    },

    applyTypingUsers(users = []) {
        const nextTypingUsers = {};

        users.forEach((entry) => {
            if (!entry?.user_id || Number(entry.user_id) === Number(this.currentUserId)) {
                return;
            }

            nextTypingUsers[String(entry.user_id)] = entry.user_name || 'Seseorang';
        });

        this.typingUsers = nextTypingUsers;
        this.typingText = buildTypingText(Object.values(this.typingUsers));
    },

    enablePolling(label) {
        this.connectionState = label;

        if (this.pollTimer) {
            return;
        }

        this.pollTimer = window.setInterval(() => this.pollMessages(), POLL_INTERVAL);
    },

    disablePolling(label = 'Realtime aktif.') {
        this.connectionState = label;

        if (!this.pollTimer) {
            return;
        }

        window.clearInterval(this.pollTimer);
        this.pollTimer = null;
    },

    async pollMessages() {
        if (this.isPolling) {
            return;
        }

        this.isPolling = true;

        try {
            const { data } = await window.axios.get(this.pollUrl, {
                params: {
                    after: this.lastMessageId(),
                },
                headers: {
                    Accept: 'application/json',
                },
            });

            (data.messages ?? []).forEach((message) => this.upsertMessage(message));
            this.applyTypingUsers(data.typing_users ?? []);

            if (typeof this.updateExtraState === 'function') {
                this.updateExtraState(data);
            }
        } catch (error) {
            this.connectionState = 'Realtime terputus. Polling otomatis aktif.';
        } finally {
            this.isPolling = false;
        }
    },

    async submitMessage() {
        const content = this.form.content.trim();

        if (content === '' || this.isSubmitting) {
            return;
        }

        this.isSubmitting = true;
        this.error = null;

        try {
            const { data } = await window.axios.post(this.sendUrl, {
                content,
            }, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (data.message) {
                this.upsertMessage(data.message);
            }

            if (typeof this.updateExtraState === 'function') {
                this.updateExtraState(data);
            }

            this.form.content = '';
            this.typingUsers = {};
            this.typingText = '';
        } catch (error) {
            this.error = extractErrorMessage(error);
        } finally {
            this.isSubmitting = false;
        }
    },

    upsertMessage(message) {
        if (!message?.id) {
            return;
        }

        const normalizedMessage = {
            ...message,
            id: Number(message.id),
        };
        const existingIndex = this.messages.findIndex((item) => Number(item.id) === normalizedMessage.id);

        if (existingIndex === -1) {
            this.messages.push(normalizedMessage);
        } else {
            this.messages.splice(existingIndex, 1, normalizedMessage);
        }

        this.messages = normalizeMessages(this.messages);
        this.$nextTick(() => this.scrollToBottom());
    },

    lastMessageId() {
        return this.messages.length > 0 ? Number(this.messages[this.messages.length - 1].id) : 0;
    },

    scrollToBottom() {
        const container = this.$refs.messageList;

        if (!container) {
            return;
        }

        container.scrollTop = container.scrollHeight;
    },
    };
};

export function registerRealtimeChat(Alpine) {
    Alpine.data('threadChat', (options) => ({
        ...buildBaseChat(options),
        aiStatus: options.thread.ai_status ?? 'idle',
        aiError: options.thread.ai_error ?? null,
        hasAiNotice: false,
        aiStatusText: '',
        aiStatusClasses: 'border-blue-500/30 bg-blue-500/10 text-blue-200',

        init() {
            this.syncAiPresentation();
            this.booted = true;
            this.enablePolling(this.connectionState);
            this.connectRealtime();
            this.$nextTick(() => this.scrollToBottom());
        },

        registerExtraListeners(channel) {
            channel.listen('.ai.status.updated', (event) => {
                this.applyThreadState(event.thread);
            });
        },

        updateExtraState(data) {
            if (data.thread) {
                this.applyThreadState(data.thread);
            }
        },

        applyThreadState(thread) {
            if (!thread) {
                return;
            }

            this.aiStatus = thread.ai_status ?? 'idle';
            this.aiError = thread.ai_error ?? null;
            this.syncAiPresentation();
        },

        roleLabel(message) {
            return message.role === 'assistant' ? 'AI Tutor' : 'Anda';
        },

        syncAiPresentation() {
            this.hasAiNotice = this.aiStatus !== 'idle' || Boolean(this.aiError);

            if (this.aiStatus === 'queued') {
                this.aiStatusText = 'AI sedang antre memproses jawaban...';
                this.aiStatusClasses = 'border-blue-500/30 bg-blue-500/10 text-blue-200';
                return;
            }

            if (this.aiStatus === 'processing') {
                this.aiStatusText = 'AI sedang menyusun jawaban...';
                this.aiStatusClasses = 'border-blue-500/30 bg-blue-500/10 text-blue-200';
                return;
            }

            if (this.aiStatus === 'failed') {
                this.aiStatusText = this.aiError || 'AI gagal menjawab. Coba kirim ulang pesan.';
                this.aiStatusClasses = 'border-red-500/30 bg-red-500/10 text-red-200';
                return;
            }

            this.aiStatusText = '';
            this.aiStatusClasses = 'border-blue-500/30 bg-blue-500/10 text-blue-200';
        },
    }));

    Alpine.data('roomChat', (options) => ({
        ...buildBaseChat(options),
        currentUserId: options.currentUserId,
        currentUserName: options.currentUserName,
        enableTyping: true,
    }));

    Alpine.data('matchChat', (options) => ({
        ...buildBaseChat(options),
        currentUserId: options.currentUserId,
        currentUserName: options.currentUserName,
        enableTyping: true,
    }));
}
