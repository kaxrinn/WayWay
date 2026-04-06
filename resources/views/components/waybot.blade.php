<div
    x-data="waybotApp()"
    x-init="init()"
    @keydown.escape.window="if(open) closeChat()"
    class="waybot-root"
>
    {{-- ========== FAB TRIGGER BUTTON ========== --}}
    <button
        @click="toggleChat()"
        :class="open ? 'waybot-fab--open' : ''"
        class="waybot-fab"
        aria-label="Buka Waybot"
    >
        {{-- Icon Chat --}}
        <span x-show="!open" x-transition.opacity class="waybot-fab-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                <circle cx="9" cy="10" r="1" fill="currentColor" stroke="none"/>
                <circle cx="12" cy="10" r="1" fill="currentColor" stroke="none"/>
                <circle cx="15" cy="10" r="1" fill="currentColor" stroke="none"/>
            </svg>
        </span>
        {{-- Icon Close --}}
        <span x-show="open" x-transition.opacity class="waybot-fab-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </span>

        {{-- Notif dot --}}
        <span x-show="!open && hasUnread" class="waybot-notif-dot"></span>
    </button>

    {{-- ========== CHAT WINDOW ========== --}}
    <div
        x-show="open"
        x-transition:enter="waybot-window-enter"
        x-transition:enter-start="waybot-window-enter-from"
        x-transition:enter-end="waybot-window-enter-to"
        x-transition:leave="waybot-window-leave"
        x-transition:leave-start="waybot-window-leave-from"
        x-transition:leave-end="waybot-window-leave-to"
        class="waybot-window"
    >
        {{-- HEADER --}}
        <div class="waybot-header">
            <div class="waybot-header-left">
                <div class="waybot-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span class="waybot-online-dot"></span>
                </div>
                <div>
                    <p class="waybot-name">Waybot</p>
                    <p class="waybot-status">
                        <span class="waybot-status-dot"></span>
                        Asisten Wisata Batam
                    </p>
                </div>
            </div>
            <div class="waybot-header-actions">
                <button @click="resetChat()" title="Reset percakapan" class="waybot-action-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.09"/>
                    </svg>
                </button>
                <button @click="closeChat()" title="Tutup" class="waybot-action-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- MESSAGES AREA --}}
        <div class="waybot-messages" x-ref="messagesContainer">

            {{-- Welcome message (muncul saat kosong) --}}
            <template x-if="messages.length === 0">
                <div class="waybot-welcome">
    <div class="waybot-welcome-emoji">🗺️</div>
    <p class="waybot-welcome-title">Hello! I'm Waybot</p>
    <p class="waybot-welcome-sub">Your smart travel assistant in Batam. Ask anything — place recommendations, price info, or the best routes!</p>
    <div class="waybot-suggestions">
        <button @click="sendSuggestion('Recommend tourist destinations in Batam')" class="waybot-suggestion-chip">
            🏝️ Travel recommendations
        </button>
        <button @click="sendSuggestion('Which destinations are suitable for families?')" class="waybot-suggestion-chip">
            👨‍👩‍👧 Family-friendly places
        </button>
        <button @click="sendSuggestion('What are the best beaches in Batam?')" class="waybot-suggestion-chip">
            🌊 Best beaches
        </button>
        <button @click="sendSuggestion('Good food recommendations in Batam')" class="waybot-suggestion-chip">
            🍽️ Food recommendations
        </button>
                    </div>
                </div>
            </template>

            {{-- Message bubbles --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'waybot-msg-row--user' : 'waybot-msg-row'" class="waybot-msg-row-base">

                    {{-- Bot avatar (hanya untuk bot) --}}
                    <div x-show="msg.role === 'assistant'" class="waybot-msg-bot-avatar">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>

                    <div class="waybot-msg-group">
                        {{-- Bubble --}}
                        <div
                            :class="msg.role === 'user' ? 'waybot-bubble--user' : 'waybot-bubble--bot'"
                            class="waybot-bubble"
                            x-html="formatMessage(msg.content)"
                        ></div>

                        {{-- Option chips (untuk preference eliciting) --}}
                        <template x-if="msg.options && msg.options.length > 0">
                            <div class="waybot-options-grid">
                                <template x-for="opt in msg.options" :key="opt">
                                    <button
                                        @click="selectOption(opt)"
                                        :disabled="msg.answered"
                                        :class="msg.answered && msg.selected === opt ? 'waybot-option--selected' : ''"
                                        class="waybot-option-chip"
                                        x-text="opt"
                                    ></button>
                                </template>
                            </div>
                        </template>

                        {{-- Destinasi cards --}}
                        <template x-if="msg.destinasi_cards && msg.destinasi_cards.length > 0">
                            <div class="waybot-cards-scroll">
                                <template x-for="card in msg.destinasi_cards" :key="card.id">
                                    <a :href="'/destinasi/' + card.id" target="_blank" class="waybot-dest-card">
                                        <div class="waybot-dest-card-img">
                                            <img
                                                x-show="card.foto"
                                                :src="'/storage/' + card.foto"
                                                :alt="card.nama"
                                                loading="lazy"
                                            >
                                            <div x-show="!card.foto" class="waybot-dest-card-placeholder">🗺️</div>
                                        </div>
                                        <div class="waybot-dest-card-body">
                                            <p class="waybot-dest-card-name" x-text="card.nama"></p>
                                            <p class="waybot-dest-card-price"
                                               x-text="card.harga > 0 ? 'Rp ' + formatRupiah(card.harga) : 'Gratis'">
                                            </p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>

                        {{-- Timestamp --}}
                        <p class="waybot-msg-time" x-text="msg.time"></p>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="waybot-msg-row-base waybot-msg-row">
                <div class="waybot-msg-bot-avatar">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div class="waybot-bubble waybot-bubble--bot waybot-typing">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        {{-- INPUT AREA --}}
        <div class="waybot-input-area">
            <div class="waybot-input-wrapper">
                <textarea
                    x-model="inputMessage"
                    @keydown.enter.prevent="handleEnter($event)"
                    x-ref="inputField"
                    placeholder="Tanya Waybot..."
                    rows="1"
                    class="waybot-input"
                    :disabled="isTyping"
                ></textarea>
                <button
                    @click="sendMessage()"
                    :disabled="!inputMessage.trim() || isTyping"
                    class="waybot-send-btn"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
            <p class="waybot-footer-text">Powered by WayWay AI · Khusus wisata Batam 🏝️</p>
        </div>
    </div>
</div>


{{-- ========== STYLES ========== --}}
<style>
    /* === ROOT & FAB === */
    .waybot-root {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 99999;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    /* Mobile: sesuaikan posisi agar tidak ketutup bottom nav */
    @media (max-width: 1023px) {
        .waybot-root {
            bottom: 80px;
            right: 16px;
        }
    }

    .waybot-fab {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5b9ac7 0%, #3d7aab 100%);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(91, 154, 199, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .waybot-fab:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 28px rgba(91, 154, 199, 0.55);
    }

    .waybot-fab--open {
        background: linear-gradient(135deg, #496d9e 0%, #3a5a85 100%);
        transform: rotate(0deg);
    }

    .waybot-fab-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
    }

    .waybot-notif-dot {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid white;
        animation: waybot-pulse 2s infinite;
    }

    @keyframes waybot-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    /* === CHAT WINDOW === */
    .waybot-window {
        position: absolute;
        bottom: 68px;
        right: 0;
        width: 380px;
        max-height: 580px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(158, 204, 219, 0.3);
    }

    @media (max-width: 440px) {
        .waybot-window {
            width: calc(100vw - 32px);
            right: -8px;
            max-height: 70vh;
        }
    }

    /* Transitions */
    .waybot-window-enter { transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .waybot-window-enter-from { opacity: 0; transform: scale(0.8) translateY(20px); transform-origin: bottom right; }
    .waybot-window-enter-to { opacity: 1; transform: scale(1) translateY(0); }
    .waybot-window-leave { transition: all 0.2s ease-in; }
    .waybot-window-leave-from { opacity: 1; transform: scale(1); }
    .waybot-window-leave-to { opacity: 0; transform: scale(0.85) translateY(12px); transform-origin: bottom right; }

    /* === HEADER === */
    .waybot-header {
        padding: 14px 16px;
        background: linear-gradient(135deg, #5b9ac7 0%, #3d7aab 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .waybot-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .waybot-avatar {
        position: relative;
        width: 38px;
        height: 38px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .waybot-online-dot {
        position: absolute;
        bottom: 1px;
        right: 1px;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid white;
    }

    .waybot-name {
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.2;
    }

    .waybot-status {
        color: rgba(255,255,255,0.8);
        font-size: 0.72rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .waybot-status-dot {
        width: 6px;
        height: 6px;
        background: #86efac;
        border-radius: 50%;
        display: inline-block;
    }

    .waybot-header-actions {
        display: flex;
        gap: 4px;
    }

    .waybot-action-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: none;
        cursor: pointer;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .waybot-action-btn:hover {
        background: rgba(255,255,255,0.25);
    }

    /* === MESSAGES === */
    .waybot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        scroll-behavior: smooth;
    }

    .waybot-messages::-webkit-scrollbar {
        width: 4px;
    }
    .waybot-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    .waybot-messages::-webkit-scrollbar-thumb {
        background: #d1e8f5;
        border-radius: 4px;
    }

    /* === WELCOME === */
    .waybot-welcome {
        text-align: center;
        padding: 12px 8px;
    }

    .waybot-welcome-emoji {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .waybot-welcome-title {
        font-weight: 700;
        color: #1e40af;
        font-size: 1rem;
        margin: 0 0 6px;
    }

    .waybot-welcome-sub {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.5;
        margin: 0 0 14px;
    }

    .waybot-suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
    }

    .waybot-suggestion-chip {
        background: #f0f7ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .waybot-suggestion-chip:hover {
        background: #dbeafe;
        border-color: #93c5fd;
    }

    /* === MESSAGE ROW === */
    .waybot-msg-row-base {
        display: flex;
        align-items: flex-end;
        gap: 7px;
        animation: waybot-msg-in 0.3s ease;
    }

    @keyframes waybot-msg-in {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .waybot-msg-row { justify-content: flex-start; }
    .waybot-msg-row--user { justify-content: flex-end; }

    .waybot-msg-bot-avatar {
        width: 26px;
        height: 26px;
        background: linear-gradient(135deg, #5b9ac7, #3d7aab);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .waybot-msg-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-width: 78%;
    }

    /* === BUBBLE === */
    .waybot-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 0.83rem;
        line-height: 1.55;
        word-break: break-word;
    }

    .waybot-bubble--bot {
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }

    .waybot-bubble--user {
        background: linear-gradient(135deg, #5b9ac7, #3d7aab);
        color: white;
        border-bottom-right-radius: 4px;
        margin-left: auto;
    }

    .waybot-bubble--bot strong { color: #1d4ed8; }
    .waybot-bubble--bot br { display: block; margin-top: 4px; }

    /* === TYPING === */
    .waybot-typing {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
    }

    .waybot-typing span {
        width: 7px;
        height: 7px;
        background: #94a3b8;
        border-radius: 50%;
        animation: waybot-bounce 1.2s infinite;
    }
    .waybot-typing span:nth-child(2) { animation-delay: 0.2s; }
    .waybot-typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes waybot-bounce {
        0%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-6px); }
    }

    /* === OPTIONS / PREFERENCE CHIPS === */
    .waybot-options-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 4px;
    }

    .waybot-option-chip {
        background: white;
        border: 1.5px solid #93c5fd;
        color: #1d4ed8;
        font-size: 0.75rem;
        padding: 5px 11px;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .waybot-option-chip:hover:not(:disabled) {
        background: #eff6ff;
        border-color: #3b82f6;
    }

    .waybot-option-chip:disabled {
        cursor: default;
        opacity: 0.5;
    }

    .waybot-option--selected {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
        opacity: 1 !important;
    }

    /* === DESTINASI CARDS === */
    .waybot-cards-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-top: 6px;
        scrollbar-width: none;
    }

    .waybot-cards-scroll::-webkit-scrollbar { display: none; }

    .waybot-dest-card {
        flex-shrink: 0;
        width: 130px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .waybot-dest-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(91,154,199,0.2);
        border-color: #93c5fd;
    }

    .waybot-dest-card-img {
        width: 100%;
        height: 80px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .waybot-dest-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .waybot-dest-card-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .waybot-dest-card-body {
        padding: 7px 9px;
    }

    .waybot-dest-card-name {
        font-weight: 600;
        font-size: 0.72rem;
        color: #1e293b;
        margin: 0 0 2px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .waybot-dest-card-price {
        font-size: 0.68rem;
        color: #3d7aab;
        font-weight: 600;
        margin: 0;
    }

    /* === TIMESTAMP === */
    .waybot-msg-time {
        font-size: 0.65rem;
        color: #94a3b8;
        margin: 0;
        padding: 0 4px;
        align-self: flex-end;
    }

    .waybot-msg-row--user .waybot-msg-time {
        text-align: right;
    }

    /* === INPUT AREA === */
    .waybot-input-area {
        padding: 12px 14px;
        border-top: 1px solid #f1f5f9;
        background: white;
        flex-shrink: 0;
    }

    .waybot-input-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 8px 8px 8px 14px;
        transition: border-color 0.2s;
    }

    .waybot-input-wrapper:focus-within {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(147,197,253,0.2);
    }

    .waybot-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        resize: none;
        font-size: 0.83rem;
        color: #1e293b;
        line-height: 1.5;
        max-height: 100px;
        overflow-y: auto;
        font-family: inherit;
    }

    .waybot-input::placeholder {
        color: #94a3b8;
    }

    .waybot-send-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #5b9ac7, #3d7aab);
        border: none;
        cursor: pointer;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .waybot-send-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #4a87b4, #2d6a99);
        transform: scale(1.05);
    }

    .waybot-send-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .waybot-footer-text {
        text-align: center;
        font-size: 0.63rem;
        color: #cbd5e1;
        margin: 7px 0 0;
    }
</style>


{{-- ========== ALPINE JS COMPONENT ========== --}}
<script>
function waybotApp() {
    return {
        open: false,
        messages: [],
        inputMessage: '',
        isTyping: false,
        hasUnread: false,
        sessionToken: localStorage.getItem('waybot_session') || null,
        currentOptions: null,

        init() {
            // Tampilkan notif dot setelah beberapa detik
            setTimeout(() => { this.hasUnread = true; }, 3000);
        },

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.hasUnread = false;
                this.$nextTick(() => {
                    this.$refs.inputField?.focus();
                    this.scrollToBottom();
                });
            }
        },

        closeChat() {
            this.open = false;
        },

        async sendMessage() {
            const msg = this.inputMessage.trim();
            if (!msg || this.isTyping) return;

            this.addMessage('user', msg);
            this.inputMessage = '';
            this.autoResizeInput();
            this.isTyping = true;

            try {
                const resp = await fetch('{{ route("waybot.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        message: msg,
                        session_token: this.sessionToken,
                    }),
                });

                const data = await resp.json();

                if (data.session_token) {
                    this.sessionToken = data.session_token;
                    localStorage.setItem('waybot_session', data.session_token);
                }

                this.isTyping = false;

                if (data.success) {
                    this.addMessage('assistant', data.message, {
                        options: data.options || null,
                        pref_key: data.pref_key || null,
                        destinasi_cards: data.destinasi_cards || null,
                    });
                } else {
                    this.addMessage('assistant', data.message || 'Maaf, ada error. Coba lagi ya! 🙏');
                }
            } catch (err) {
                this.isTyping = false;
                this.addMessage('assistant', 'Koneksi bermasalah. Pastikan internet kamu stabil ya! 📡');
            }
        },

        sendSuggestion(text) {
            this.inputMessage = text;
            this.sendMessage();
        },

        selectOption(option) {
            // Tandai pertanyaan sudah dijawab
            const lastBotMsg = [...this.messages].reverse().find(m => m.role === 'assistant' && m.options);
            if (lastBotMsg) {
                lastBotMsg.answered = true;
                lastBotMsg.selected = option;
            }

            this.inputMessage = option;
            this.sendMessage();
        },

        addMessage(role, content, extra = {}) {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            this.messages.push({
                role,
                content,
                time,
                options: extra.options || null,
                pref_key: extra.pref_key || null,
                destinasi_cards: extra.destinasi_cards || null,
                answered: false,
                selected: null,
            });

            this.$nextTick(() => this.scrollToBottom());
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        handleEnter(event) {
            if (!event.shiftKey) {
                this.sendMessage();
            }
        },

        autoResizeInput() {
            this.$nextTick(() => {
                const el = this.$refs.inputField;
                if (el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
                }
            });
        },

        async resetChat() {
            try {
                await fetch('{{ route("waybot.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ session_token: this.sessionToken }),
                });
            } catch {}

            this.messages = [];
            this.sessionToken = null;
            localStorage.removeItem('waybot_session');
        },

        formatMessage(text) {
            if (!text) return '';
            // Bold: **text**
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Newlines
            text = text.replace(/\n/g, '<br>');
            return text;
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },
    };
}
</script>