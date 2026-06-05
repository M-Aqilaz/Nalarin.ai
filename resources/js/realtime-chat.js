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

const NALA_VOICE_STORAGE_KEY = 'nalarin:nalaVoiceEnabled';

const normalizeForIntent = (value = '') => value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

const stripMarkdownForSpeech = (value = '') => value
    .replace(/```[\s\S]*?```/g, ' bagian kode ')
    .replace(/`([^`]+)`/g, '$1')
    .replace(/^#{1,6}\s+/gm, '')
    .replace(/^\s*[-*]\s+/gm, '')
    .replace(/\*\*([^*]+)\*\*/g, '$1')
    .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
    .replace(/\s+/g, ' ')
    .trim();

const chooseNalaVoice = () => {
    if (!('speechSynthesis' in window)) {
        return null;
    }

    const voices = window.speechSynthesis.getVoices();

    return voices.find((voice) => voice.lang === 'id-ID' && /female|wanita|google|zira|samantha/i.test(voice.name))
        || voices.find((voice) => voice.lang === 'id-ID')
        || voices.find((voice) => voice.lang?.startsWith('id'))
        || voices.find((voice) => /female|zira|samantha|google/i.test(voice.name))
        || voices[0]
        || null;
};

const compressImageFile = (file, maxDimension = 1280, quality = 0.82) => {
    if (!file.type.startsWith('image/')) {
        return Promise.resolve(file);
    }

    return new Promise((resolve) => {
        const image = new Image();
        const objectUrl = URL.createObjectURL(file);

        image.onload = () => {
            const scale = Math.min(1, maxDimension / Math.max(image.width, image.height));
            const width = Math.max(1, Math.round(image.width * scale));
            const height = Math.max(1, Math.round(image.height * scale));
            const canvas = document.createElement('canvas');

            canvas.width = width;
            canvas.height = height;

            const context = canvas.getContext('2d');

            if (!context) {
                URL.revokeObjectURL(objectUrl);
                resolve(file);
                return;
            }

            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, width, height);
            context.drawImage(image, 0, 0, width, height);

            canvas.toBlob((blob) => {
                URL.revokeObjectURL(objectUrl);

                if (!blob) {
                    resolve(file);
                    return;
                }

                resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                }));
            }, 'image/jpeg', quality);
        };

        image.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            resolve(file);
        };

        image.src = objectUrl;
    });
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
        image: null,
        imagePreviewUrl: '',
        imageName: '',
        imageLoading: false,
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
        const image = this.form.image;

        if ((content === '' && !image) || this.isSubmitting || this.isAiWorking) {
            return;
        }

        this.isSubmitting = true;
        this.error = null;

        try {
            const payload = image ? new FormData() : { content };

            if (image) {
                payload.append('content', content);
                payload.append('image', image);
            }

            const { data } = await window.axios.post(this.sendUrl, payload, {
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
            this.clearImage();
            this.typingUsers = {};
            this.typingText = '';
        } catch (error) {
            this.error = extractErrorMessage(error);
        } finally {
            this.isSubmitting = false;
        }
    },

    selectImage(event) {
        const file = event.target.files?.[0] ?? null;

        this.setImageFile(file, false);
    },

    handlePaste(event) {
        const item = [...(event.clipboardData?.items ?? [])]
            .find((entry) => entry.kind === 'file' && entry.type.startsWith('image/'));

        if (!item) {
            return;
        }

        const file = item.getAsFile();

        if (!file) {
            return;
        }

        event.preventDefault();
        this.setImageFile(file, true);
    },

    async setImageFile(file, resetInput = true) {
        this.clearImage(resetInput);

        if (!file) {
            return;
        }

        this.form.imageLoading = true;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxOriginalSize = 12 * 1024 * 1024;
        const maxUploadSize = 3 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            this.error = 'Format gambar harus JPG, PNG, atau WEBP.';
            this.form.imageLoading = false;
            return;
        }

        if (file.size > maxOriginalSize) {
            this.error = 'Ukuran gambar terlalu besar. Maksimal 12MB sebelum dikompres.';
            this.form.imageLoading = false;
            return;
        }

        const compressedFile = await compressImageFile(file);

        if (compressedFile.size > maxUploadSize) {
            this.error = 'Gambar masih terlalu besar setelah dikompres. Coba crop atau kirim bagian yang penting saja.';
            this.form.imageLoading = false;
            return;
        }

        const extension = {
            'image/jpeg': 'jpg',
            'image/png': 'png',
            'image/webp': 'webp',
        }[compressedFile.type] || 'jpg';
        const safeName = file.name && file.name !== 'image.png'
            ? file.name.replace(/\.[^.]+$/, `.${extension}`)
            : `screenshot-${Date.now()}.${extension}`;

        window.setTimeout(() => {
            this.form.image = new File([compressedFile], safeName, { type: compressedFile.type });
            this.form.imageName = safeName;
            this.form.imagePreviewUrl = URL.createObjectURL(this.form.image);
            this.form.imageLoading = false;
            this.error = null;
        }, 120);
    },

    clearImage(resetInput = true) {
        if (this.form.imagePreviewUrl) {
            URL.revokeObjectURL(this.form.imagePreviewUrl);
        }

        this.form.image = null;
        this.form.imagePreviewUrl = '';
        this.form.imageName = '';
        this.form.imageLoading = false;

        if (resetInput && this.$refs.imageInput) {
            this.$refs.imageInput.value = '';
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

        if (typeof this.afterMessageUpserted === 'function') {
            this.afterMessageUpserted(normalizedMessage, existingIndex === -1);
        }

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
        threadTitle: options.thread.title ?? '',
        hasAiNotice: false,
        isAiWorking: false,
        aiStatusText: '',
        aiStatusClasses: 'border-blue-500/30 bg-blue-500/10 text-blue-200',
        nalaFaces: options.nalaFaces ?? {},
        nalaMood: 'flat',
        nalaLine: 'Aku Nala. Aku bantu kamu belajar... bukan karena aku khawatir, ya.',
        nalaVoiceEnabled: false,
        nalaVoiceSupported: false,
        nalaVoiceName: '',
        nalaVoice: null,
        lastSpokenMessageId: 0,
        lastSpokenError: '',
        latestAssistantText: '',
        threadContextMenu: {
            open: false,
            id: null,
        },

        init() {
            this.initializeNalaVoice();
            this.syncNalaFromMessages(false);
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
            this.threadTitle = thread.title ?? this.threadTitle;
            this.syncAiPresentation();
        },

        roleLabel(message) {
            return message.role === 'assistant' ? 'Nala' : 'Anda';
        },

        openThreadMenu(threadId) {
            this.threadContextMenu = {
                open: true,
                id: Number(threadId),
            };
        },

        closeThreadMenu() {
            this.threadContextMenu = {
                open: false,
                id: null,
            };
        },

        get nalaImage() {
            return this.nalaFaces[this.nalaMood] || this.nalaFaces.flat || this.nalaFaces.happy || '';
        },

        get nalaMoodLabel() {
            return {
                happy: 'Senang',
                flat: 'Fokus',
                angry: 'Jutek mode',
                sad: 'Khawatir',
                cute: 'Gemes',
                shy: 'Malu-malu',
                silly: 'Bingung mode',
                sorry: 'Minta maaf',
            }[this.nalaMood] || 'Fokus';
        },

        get voiceStatusLabel() {
            if (!this.nalaVoiceSupported) {
                return 'Voice tidak didukung browser ini';
            }

            return this.nalaVoiceEnabled
                ? `Voice on${this.nalaVoiceName ? ` - ${this.nalaVoiceName}` : ''}`
                : 'Voice off';
        },

        initializeNalaVoice() {
            this.nalaVoiceSupported = 'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;

            if (!this.nalaVoiceSupported) {
                this.nalaVoiceEnabled = false;
                return;
            }

            this.nalaVoiceEnabled = window.localStorage.getItem(NALA_VOICE_STORAGE_KEY) === 'true';

            const refreshVoice = () => {
                this.nalaVoice = chooseNalaVoice();
                this.nalaVoiceName = this.nalaVoice?.name || 'Default browser';
            };

            refreshVoice();
            window.speechSynthesis.onvoiceschanged = refreshVoice;
        },

        toggleNalaVoice() {
            if (!this.nalaVoiceSupported) {
                return;
            }

            this.nalaVoiceEnabled = !this.nalaVoiceEnabled;
            window.localStorage.setItem(NALA_VOICE_STORAGE_KEY, this.nalaVoiceEnabled ? 'true' : 'false');

            if (!this.nalaVoiceEnabled) {
                window.speechSynthesis.cancel();
                return;
            }

            this.speakNalaLine('Hmph. Baiklah, mulai sekarang Nala bacakan jawabannya. Dengar baik-baik, ya.');
        },

        replayNalaVoice() {
            if (!this.nalaVoiceSupported) {
                return;
            }

            const text = this.latestAssistantText || this.nalaLine;
            this.speakNalaLine(text, true);
        },

        speakNalaLine(text, force = false) {
            if (!this.nalaVoiceSupported || (!this.nalaVoiceEnabled && !force)) {
                return;
            }

            const cleanText = stripMarkdownForSpeech(text);

            if (!cleanText) {
                return;
            }

            const utterance = new SpeechSynthesisUtterance(cleanText);
            const voice = this.nalaVoice || chooseNalaVoice();

            if (voice) {
                utterance.voice = voice;
            }

            utterance.lang = 'id-ID';
            utterance.pitch = 1.28;
            utterance.rate = 1.03;
            utterance.volume = 1;

            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utterance);
        },

        setNalaMood(mood, line) {
            this.nalaMood = ['happy', 'flat', 'angry', 'sad', 'cute', 'shy', 'silly', 'sorry'].includes(mood) ? mood : 'flat';
            this.nalaLine = line;
        },

        classifyUserMood(content = '') {
            const text = normalizeForIntent(content);

            if (/(bingung|susah|sulit|gak ngerti|ga ngerti|tidak ngerti|tidak paham|gagal|stress|stres|pusing)/.test(text)) {
                return {
                    mood: /(gagal|stress|stres|pusing)/.test(text) ? 'sorry' : 'sad',
                    line: 'Aduh... kamu bingung, ya? Nala bantu pelan-pelan. Jangan nyerah dulu.',
                };
            }

            if (/(typo|salah ketik|kok aneh|maksudnya apa|ini apa|hah|lah|loh|bingungin)/.test(text)) {
                return {
                    mood: 'silly',
                    line: 'Eh... Nala perlu baca pelan-pelan dulu. Coba kasih konteksnya sedikit lagi, ya.',
                };
            }

            if (/(malas|males|terserah|gatau|ga tau|nggak tau|tidak tahu|skip aja|bodo)/.test(text) || text.length < 8) {
                return {
                    mood: 'angry',
                    line: 'Hmph, jangan malas. Kasih Nala pertanyaan yang jelas biar aku bisa bantu.',
                };
            }

            if (/(makasih|terima kasih|thanks|thank you|paham|mengerti|berhasil|bisa sekarang|sip)/.test(text)) {
                return {
                    mood: /(makasih|terima kasih|thanks|thank you)/.test(text) ? 'shy' : 'cute',
                    line: 'Y-ya bagus kalau kamu paham. Bukan berarti Nala senang banget, sih.',
                };
            }

            if (/(cantik|imut|lucu|keren|bagus banget|pinter|pintar|nala hebat)/.test(text)) {
                return {
                    mood: 'shy',
                    line: 'H-hmph... pujian begitu tidak bikin Nala salah tingkah kok. Lanjut belajar.',
                };
            }

            return {
                mood: 'flat',
                line: 'Oke, Nala cek dulu. Kamu tinggal ikuti penjelasanku baik-baik.',
            };
        },

        syncNalaFromMessages(shouldSpeak = false) {
            const latestUser = [...this.messages].reverse().find((message) => message.role === 'user');
            const latestAssistant = [...this.messages].reverse().find((message) => message.role === 'assistant');

            if (latestAssistant?.content) {
                this.latestAssistantText = latestAssistant.content;
            }

            if (latestUser?.content) {
                const result = this.classifyUserMood(latestUser.content);
                this.setNalaMood(result.mood, result.line);
            }

            if (latestAssistant?.content && Number(latestAssistant.id) > Number(this.lastSpokenMessageId)) {
                this.setNalaMood('cute', 'Sudah Nala jawab. Dibaca pelan-pelan, jangan cuma diskip.');

                if (shouldSpeak) {
                    this.lastSpokenMessageId = Number(latestAssistant.id);
                    this.speakNalaLine(latestAssistant.content);
                }
            }
        },

        afterMessageUpserted(message, isNew) {
            if (message.role === 'user') {
                const result = this.classifyUserMood(message.content);
                this.setNalaMood(result.mood, result.line);
                return;
            }

            if (message.role === 'assistant') {
                this.latestAssistantText = message.content || '';
                this.setNalaMood('cute', 'Sudah Nala jawab. Dibaca pelan-pelan, jangan cuma diskip.');

                if (isNew && Number(message.id) > Number(this.lastSpokenMessageId)) {
                    this.lastSpokenMessageId = Number(message.id);
                    this.speakNalaLine(message.content);
                }
            }
        },

        syncAiPresentation() {
            this.isAiWorking = this.aiStatus === 'queued' || this.aiStatus === 'processing';
            this.hasAiNotice = this.aiStatus === 'failed' || Boolean(this.aiError);

            if (this.aiStatus === 'queued') {
                this.aiStatusText = 'Nala sedang menyiapkan jawaban...';
                this.aiStatusClasses = 'border-blue-500/30 bg-blue-500/10 text-blue-200';
                this.lastSpokenError = '';
                this.setNalaMood('flat', 'Hmph, tunggu sebentar. Nala lagi mikir.');
                return;
            }

            if (this.aiStatus === 'processing') {
                this.aiStatusText = 'Nala sedang mengetik...';
                this.aiStatusClasses = 'border-blue-500/30 bg-blue-500/10 text-blue-200';
                this.lastSpokenError = '';
                this.setNalaMood('flat', 'Hmph, tunggu sebentar. Nala lagi mikir.');
                return;
            }

            if (this.aiStatus === 'failed') {
                this.aiStatusText = this.aiError || 'Nala gagal menjawab. Coba kirim ulang pesan.';
                this.aiStatusClasses = 'border-red-500/30 bg-red-500/10 text-red-200';
                this.setNalaMood('sorry', 'Aduh... Nala gagal jawab. Coba kirim ulang ya.');

                if (this.aiStatusText !== this.lastSpokenError) {
                    this.lastSpokenError = this.aiStatusText;
                    this.speakNalaLine(this.nalaLine);
                }

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
