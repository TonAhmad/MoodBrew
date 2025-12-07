{{--
    AI Chat Widget Component
    
    Floating chat widget untuk customer berinteraksi dengan AI Barista.
    Include di layout customer untuk menampilkan chat bubble.
    
    Usage:
    @include('components.aiChatWidget')
--}}

<div x-data="aiChatWidget()" x-cloak>
    {{-- Chat Bubble Button --}}
    <button @click="toggleChat"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-brew-gold rounded-full shadow-lg hover:bg-yellow-500 transition-all duration-300 flex items-center justify-center group"
        :class="{ 'scale-110': isOpen }">
        {{-- Chat Icon --}}
        <svg x-show="!isOpen" class="w-7 h-7 text-brew-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        {{-- Close Icon --}}
        <svg x-show="isOpen" class="w-7 h-7 text-brew-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{-- Notification Badge --}}
        <span x-show="hasUnread && !isOpen"
            class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-pulse"></span>
    </button>

    {{-- Chat Window --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-24 right-6 z-50 w-[380px] max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-brew-dark to-brew-brown p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center">
                    <span class="text-xl">☕</span>
                </div>
                <div>
                    <h3 class="text-brew-cream font-semibold">Brew - AI Barista</h3>
                    <p class="text-brew-cream/70 text-xs flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5 animate-pulse"></span>
                        Online • Siap membantu
                    </p>
                </div>
            </div>
        </div>

        {{-- Messages Container --}}
        <div x-ref="messagesContainer" class="h-80 overflow-y-auto p-4 space-y-4 bg-gray-50">
            {{-- Welcome Message --}}
            <template x-if="messages.length === 0">
                <div class="text-center py-4">
                    <div class="w-16 h-16 bg-brew-cream rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-3xl">👋</span>
                    </div>
                    <h4 class="font-semibold text-brew-dark mb-1">Hai, saya Brew!</h4>
                    <p class="text-sm text-gray-500 mb-4">
                        Ceritakan mood kamu, dan saya akan carikan minuman yang cocok ☕
                    </p>

                    {{-- Quick Replies --}}
                    <div class="flex flex-wrap justify-center gap-2">
                        <template x-for="reply in quickReplies" :key="reply.text">
                            <button @click="sendQuickReply(reply)"
                                class="px-3 py-1.5 bg-white border border-brew-gold text-brew-dark text-sm rounded-full hover:bg-brew-cream transition-colors">
                                <span x-text="reply.text"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Message Bubbles --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    {{-- AI Avatar --}}
                    <div x-show="msg.type === 'ai'"
                        class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0 mr-2">
                        <span class="text-sm">☕</span>
                    </div>

                    {{-- Message Bubble --}}
                    <div :class="msg.type === 'user' ?
                        'bg-brew-gold text-brew-dark rounded-2xl rounded-br-md' :
                        'bg-white border border-gray-200 text-gray-700 rounded-2xl rounded-bl-md'"
                        class="px-4 py-2.5 max-w-[80%] shadow-sm">
                        <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>

                        {{-- Recommended Menus (if any) --}}
                        <template x-if="msg.recommendations && msg.recommendations.length > 0">
                            <div class="mt-3 space-y-2">
                                <template x-for="menu in msg.recommendations" :key="menu.id">
                                    <div
                                        class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                        <div>
                                            <p class="font-medium text-sm text-brew-dark" x-text="menu.name"></p>
                                            <p class="text-xs text-gray-500"
                                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(menu.price)"></p>
                                        </div>
                                        <button @click="addToCart(menu)"
                                            class="px-3 py-1 bg-brew-gold text-brew-dark text-xs font-medium rounded-full hover:bg-yellow-500 transition-colors">
                                            + Tambah
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- User Avatar --}}
                    <div x-show="msg.type === 'user'"
                        class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 ml-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="flex justify-start">
                <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0 mr-2">
                    <span class="text-sm">☕</span>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex space-x-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                            style="animation-delay: 300ms"></span>
                    </div>
                     <span class="text-xs text-gray-500">
                Brew lagi mikirkan rekomendasi buatmu sebentar ya…
            </span>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 border-t border-gray-200 bg-white">
            <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                <input x-model="inputMessage" type="text" placeholder="Ceritakan mood kamu..."
                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-brew-gold focus:border-transparent"
                    :disabled="isTyping">
                <button type="submit" :disabled="!inputMessage.trim() || isTyping"
                    class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center hover:bg-yellow-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-brew-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function aiChatWidget() {
        return {
            isOpen: false,
            isTyping: false,
            hasUnread: false,
            inputMessage: '',
            messages: [],
            conversationHistory: [],
            quickReplies: [{
                    text: '☕ Rekomendasi untukku',
                    action: 'recommend'
                },
                {
                    text: '😊 Saya sedang senang',
                    action: 'mood_happy'
                },
                {
                    text: '😔 Saya sedang lelah',
                    action: 'mood_tired'
                },
                {
                    text: '😰 Saya sedang stress',
                    action: 'mood_stress'
                },
            ],

            toggleChat() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.hasUnread = false;
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            async sendMessage() {
                if (!this.inputMessage.trim() || this.isTyping) return;

                const userMessage = this.inputMessage.trim();
                this.inputMessage = '';

                // Add user message to chat
                this.messages.push({
                    type: 'user',
                    content: userMessage
                });

                this.scrollToBottom();
                this.isTyping = true;

                try {
                    // Call AI recommendation endpoint
                    const response = await fetch('/order/ai/recommend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                        },
                        body: JSON.stringify({
                            mood: userMessage
                        }),
                    });

                    const data = await response.json();

                    // Add AI response to chat
                    this.messages.push({
                        type: 'ai',
                        content: data.message || 'Maaf, saya sedang mengalami gangguan.',
                        recommendations: data.recommendations || [],
                    });

                    // Update conversation history
                    this.conversationHistory.push({
                        user: userMessage,
                        assistant: data.message,
                    });

                } catch (error) {
                    console.error('AI Error:', error);
                    this.messages.push({
                        type: 'ai',
                        content: 'Maaf, saya sedang mengalami gangguan. Coba lagi nanti ya! 🙏',
                    });
                }

                this.isTyping = false;
                this.scrollToBottom();
            },

            sendQuickReply(reply) {
                const moodMap = {
                    'recommend': 'Tolong rekomendasikan minuman yang cocok untuk saya',
                    'mood_happy': 'Saya sedang senang dan ingin merayakannya! 😊',
                    'mood_tired': 'Saya sedang lelah dan butuh energi 😔',
                    'mood_stress': 'Saya sedang stress, butuh sesuatu yang menenangkan 😰',
                };

                this.inputMessage = moodMap[reply.action] || reply.text;
                this.sendMessage();
            },

            addToCart(menu) {
                // TODO: Integrate with cart system
                alert(`${menu.name} ditambahkan ke keranjang! (TODO: Integrate with cart)`);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                });
            },
        };
    }
</script>
