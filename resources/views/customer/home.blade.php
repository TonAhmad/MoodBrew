@extends('layouts.custLayout')

@section('title', 'MoodBrew AI')

@section('content')
    <div class="pb-20 lg:pb-6">`
        {{-- Welcome Section --}}
        <div class="bg-gradient-to-br from-brew-brown to-brew-dark p-6 text-white">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-brew-gold rounded-full flex items-center justify-center">
                    <span class="text-2xl">🤖</span>
                </div>
                <div>
                    <p class="text-brew-cream/80 text-sm">Halo,</p>
                    <p class="font-bold text-lg">{{ $customer['name'] ?? 'Teman' }}! ☕</p>
                </div>
            </div>
            <p class="text-brew-cream/90">
                Ceritakan perasaanmu hari ini, dan aku akan merekomendasikan minuman yang cocok untukmu.
            </p>
        </div>

        {{-- AI Status --}}
        @if(!($aiAvailable ?? false))
            {{-- Under Maintenance Banner --}}
            <div class="mx-4 mt-4">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🔧</span>
                    </div>
                    <h3 class="font-bold text-amber-800 text-lg mb-2">AI Sedang Maintenance</h3>
                    <p class="text-amber-700 text-sm mb-4">
                        Fitur AI Chat sedang dalam perbaikan. Kamu tetap bisa melihat menu dan memesan langsung!
                    </p>
                    <a href="{{ route('customer.menu.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-400 transition-colors">
                        <span>Lihat Menu</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            {{-- Flash Sales Section --}}
            @if(($flashSales ?? collect())->isNotEmpty())
                <div class="p-4">
                    <h3 class="font-bold text-brew-dark mb-3 flex items-center">
                        <span class="text-xl mr-2">🔥</span> Flash Sale!
                    </h3>
                    <div class="flex space-x-3 overflow-x-auto pb-2">
                        @foreach($flashSales as $sale)
                            <div class="flex-shrink-0 w-40 bg-white rounded-xl shadow-sm p-3 border-2 border-red-200">
                                @if($sale->menuItem->image_path)
                                    <img src="{{ asset('storage/' . $sale->menuItem->image_path) }}" alt="{{ $sale->menuItem->name }}"
                                        class="w-full h-20 rounded-lg object-cover mb-2">
                                @else
                                    <div class="w-full h-20 bg-red-50 rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">☕</span>
                                    </div>
                                @endif
                                <p class="font-semibold text-brew-dark text-sm truncate">{{ $sale->menuItem->name }}</p>
                                <p class="text-gray-400 text-xs line-through">Rp {{ number_format($sale->menuItem->price, 0, ',', '.') }}</p>
                                <p class="text-red-500 font-bold">Rp {{ number_format($sale->discounted_price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Popular Items Section --}}
            @if(($popularItems ?? collect())->isNotEmpty())
                <div class="p-4">
                    <h3 class="font-bold text-brew-dark mb-3 flex items-center justify-between">
                        <span><span class="text-xl mr-2">⭐</span> Populer</span>
                        <a href="{{ route('customer.menu.index') }}" class="text-brew-gold text-sm">Lihat Semua →</a>
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($popularItems as $item)
                            <a href="{{ route('customer.menu.show', $item->slug) }}" class="bg-white rounded-xl shadow-sm p-3 block">
                                @if($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                        class="w-full h-20 rounded-lg object-cover mb-2">
                                @else
                                    <div class="w-full h-20 bg-brew-cream rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-3xl">{{ $item->category === 'coffee' ? '☕' : '🥤' }}</span>
                                    </div>
                                @endif
                                <p class="font-semibold text-brew-dark text-sm truncate">{{ $item->name }}</p>
                                <p class="text-brew-gold font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            {{-- Chat Interface - Responsive Layout --}}
            <div class="max-w-7xl mx-auto px-4 py-4" x-data="aiChat()">
                <div class="lg:grid lg:grid-cols-3 lg:gap-6">
                    {{-- Chat Section (Mobile: full width, Desktop: 2 columns) --}}
                    <div class="lg:col-span-2 space-y-4">
                        {{-- Chat Messages Container --}}
                        <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6">
                            <div id="chatMessages" class="space-y-4 h-[50vh] lg:h-[60vh] overflow-y-auto pr-2">
                                {{-- AI Welcome Message --}}
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                                        <span>🤖</span>
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none p-4 shadow-sm max-w-[85%] lg:max-w-[75%]">
                                        <p class="text-gray-700">
                                            Hai {{ $customer['name'] ?? 'teman' }}! 👋
                                        </p>
                                        <p class="text-gray-700 mt-2">
                                            Aku MoodBrew AI. Cerita dong, lagi ngerasa gimana hari ini? Stress kerjaan? Butuh semangat? Atau mau santai aja?
                                        </p>
                                        <p class="text-xs text-gray-400 mt-2">Just now</p>
                                    </div>
                                </div>

                                {{-- Dynamic Messages --}}
                                <template x-for="(msg, index) in messages" :key="index">
                                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start space-x-3'">
                                        <template x-if="msg.role === 'assistant'">
                                            <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                                                <span>🤖</span>
                                            </div>
                                        </template>
                                        <div :class="msg.role === 'user' 
                                            ? 'bg-brew-brown text-white rounded-2xl rounded-tr-none p-4 shadow-sm max-w-[85%] lg:max-w-[75%]' 
                                            : 'bg-gray-50 rounded-2xl rounded-tl-none p-4 shadow-sm max-w-[85%] lg:max-w-[75%]'">
                                            <p x-html="msg.content" :class="msg.role === 'user' ? 'text-white' : 'text-gray-700'"></p>
                                            <p class="text-xs mt-2" :class="msg.role === 'user' ? 'text-white/60' : 'text-gray-400'" x-text="msg.time"></p>
                                        </div>
                                    </div>
                                </template>

                                {{-- Loading indicator --}}
                                <div x-show="loading" class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                                        <span>🤖</span>
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none p-4 shadow-sm">
                                        <div class="flex space-x-1">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Chat Input (Desktop: fixed in chat box) --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <form @submit.prevent="sendMessage()" class="flex items-center space-x-2">
                                    <input x-model="userInput" type="text" placeholder="Ceritakan mood-mu..."
                                        class="flex-1 px-4 py-3 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-brew-gold/50 text-sm lg:text-base">
                                    <button type="submit" :disabled="loading || !userInput.trim()"
                                        class="w-12 h-12 bg-brew-gold rounded-full flex items-center justify-center text-brew-dark hover:bg-yellow-400 transition-colors disabled:opacity-50 flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3 21l18-9L3 3l3 9m0 0h9"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Quick Mood Buttons (Mobile only) --}}
                        <div class="lg:hidden">
                            <p class="text-sm text-gray-500 mb-2">Atau pilih salah satu:</p>
                            <div class="flex flex-wrap gap-2">
                                <button @click="sendQuickMood('Saya ngantuk banget, butuh yang bisa bikin melek')"
                                    class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm hover:bg-blue-100 transition-colors">
                                    😴 Ngantuk banget
                                </button>
                                <button @click="sendQuickMood('Saya lagi stress berat, butuh yang menenangkan')"
                                    class="px-4 py-2 bg-orange-50 text-orange-700 rounded-full text-sm hover:bg-orange-100 transition-colors">
                                    😤 Stress berat
                                </button>
                                <button @click="sendQuickMood('Saya lagi happy, mau yang enak dan seru')"
                                    class="px-4 py-2 bg-green-50 text-green-700 rounded-full text-sm hover:bg-green-100 transition-colors">
                                    😊 Happy vibes
                                </button>
                                <button @click="sendQuickMood('Saya lagi galau, butuh comfort drink')"
                                    class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm hover:bg-purple-100 transition-colors">
                                    🤔 Galau dikit
                                </button>
                                <button @click="sendQuickMood('Saya butuh energi dan semangat!')"
                                    class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-full text-sm hover:bg-yellow-100 transition-colors">
                                    ⚡ Butuh energi
                                </button>
                                <button @click="sendQuickMood('Saya lagi jatuh cinta, mau yang sweet')"
                                    class="px-4 py-2 bg-pink-50 text-pink-700 rounded-full text-sm hover:bg-pink-100 transition-colors">
                                    🥰 Lagi jatuh cinta
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar (Desktop only) --}}
                    <div class="hidden lg:block lg:col-span-1 space-y-4">
                        {{-- Quick Mood Buttons --}}
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="font-bold text-brew-dark mb-4 flex items-center">
                                <span class="text-xl mr-2">😊</span> Quick Mood
                            </h3>
                            <div class="space-y-2">
                                <button @click="sendQuickMood('Saya ngantuk banget, butuh yang bisa bikin melek')"
                                    class="w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-xl text-sm hover:bg-blue-100 transition-colors text-left">
                                    😴 Ngantuk banget
                                </button>
                                <button @click="sendQuickMood('Saya lagi stress berat, butuh yang menenangkan')"
                                    class="w-full px-4 py-3 bg-orange-50 text-orange-700 rounded-xl text-sm hover:bg-orange-100 transition-colors text-left">
                                    😤 Stress berat
                                </button>
                                <button @click="sendQuickMood('Saya lagi happy, mau yang enak dan seru')"
                                    class="w-full px-4 py-3 bg-green-50 text-green-700 rounded-xl text-sm hover:bg-green-100 transition-colors text-left">
                                    😊 Happy vibes
                                </button>
                                <button @click="sendQuickMood('Saya lagi galau, butuh comfort drink')"
                                    class="w-full px-4 py-3 bg-purple-50 text-purple-700 rounded-xl text-sm hover:bg-purple-100 transition-colors text-left">
                                    🤔 Galau dikit
                                </button>
                                <button @click="sendQuickMood('Saya butuh energi dan semangat!')"
                                    class="w-full px-4 py-3 bg-yellow-50 text-yellow-700 rounded-xl text-sm hover:bg-yellow-100 transition-colors text-left">
                                    ⚡ Butuh energi
                                </button>
                                <button @click="sendQuickMood('Saya lagi jatuh cinta, mau yang sweet')"
                                    class="w-full px-4 py-3 bg-pink-50 text-pink-700 rounded-xl text-sm hover:bg-pink-100 transition-colors text-left">
                                    🥰 Lagi jatuh cinta
                                </button>
                            </div>
                        </div>

                        {{-- Recommendations Section --}}
                        <div x-show="recommendations.length > 0" x-transition class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="font-bold text-brew-dark mb-4 flex items-center">
                                <span class="text-xl mr-2">✨</span> Rekomendasi
                            </h3>
                            <div class="space-y-3">
                                <template x-for="item in recommendations" :key="item.id">
                                    <div class="bg-gradient-to-br from-brew-cream to-brew-light rounded-xl p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div x-show="item.image_path" class="w-12 h-12 rounded-lg flex-shrink-0">
                                                <img :src="'{{ asset('storage') }}/' + item.image_path" :alt="item.name"
                                                    class="w-full h-full rounded-lg object-cover">
                                            </div>
                                            <div x-show="!item.image_path" class="w-12 h-12 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl" x-text="item.category === 'coffee' ? '☕' : '🥤'"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-brew-dark text-sm truncate" x-text="item.name"></p>
                                                <p class="text-brew-gold font-bold text-sm" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-3 line-clamp-2" x-text="item.description"></p>
                                        <button @click="addToCart(item.id)" 
                                            class="w-full px-4 py-2 bg-brew-gold text-brew-dark text-sm font-semibold rounded-lg hover:bg-yellow-400 transition-colors">
                                            + Tambah ke Keranjang
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Recommendations (below chat) --}}
                <div x-show="recommendations.length > 0" x-transition class="lg:hidden mt-4">
                    <h3 class="font-semibold text-brew-dark mb-3">✨ Rekomendasi untukmu:</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="item in recommendations" :key="item.id">
                            <div class="bg-white rounded-xl shadow-sm p-3">
                                <div x-show="item.image_path" class="w-full h-20 rounded-lg mb-2">
                                    <img :src="'{{ asset('storage') }}/' + item.image_path" :alt="item.name"
                                        class="w-full h-full rounded-lg object-cover">
                                </div>
                                <div x-show="!item.image_path" class="w-full h-20 bg-brew-cream rounded-lg mb-2 flex items-center justify-center">
                                    <span class="text-3xl" x-text="item.category === 'coffee' ? '☕' : '🥤'"></span>
                                </div>
                                <p class="font-semibold text-brew-dark text-sm truncate" x-text="item.name"></p>
                                <p class="text-brew-gold font-bold text-xs" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>
                                <button @click="addToCart(item.id)" 
                                    class="w-full mt-2 px-3 py-2 bg-brew-gold text-brew-dark text-xs font-semibold rounded-lg hover:bg-yellow-400 transition-colors">
                                    + Keranjang
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
function aiChat() {
    return {
        messages: [],
        loading: false,
        recommendations: [],
        userInput: '',
        conversationHistory: [],
        
        async sendMessage() {
            const message = this.userInput.trim();
            if (!message || this.loading) return;
            
            // Add user message
            this.messages.push({
                role: 'user',
                content: message,
                time: this.getCurrentTime()
            });
            
            // Clear input
            this.userInput = '';
            this.loading = true;
            this.scrollToBottom();
            
            try {
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        message: message,
                        history: this.conversationHistory 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Add AI response
                    this.messages.push({
                        role: 'assistant',
                        content: this.formatAiResponse(data.message),
                        time: this.getCurrentTime()
                    });
                    
                    // Update conversation history
                    this.conversationHistory.push({
                        user: message,
                        assistant: data.message
                    });
                    
                    // Extract menu suggestions if any
                    if (data.suggested_menus && data.suggested_menus.length > 0) {
                        this.recommendations = data.suggested_menus;
                        console.log('Recommendations received:', this.recommendations);
                    }
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: data.message || 'Maaf, coba lagi ya!',
                        time: this.getCurrentTime()
                    });
                }
            } catch (error) {
                console.error('Chat error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: 'Maaf, ada gangguan. Coba lagi nanti ya! 🙏',
                    time: this.getCurrentTime()
                });
            }
            
            this.loading = false;
            this.scrollToBottom();
        },
        
        sendQuickMood(mood) {
            this.userInput = mood;
            this.sendMessage();
        },
        
        formatAiResponse(text) {
            // Format response untuk lebih readable
            // 1. Convert **bold** markdown ke <strong>
            text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            
            // 2. Convert *italic* markdown ke <em>
            text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
            
            // 3. Replace newlines dengan <br> untuk HTML
            text = text.replace(/\n/g, '<br>');
            
            return text;
        },
        
        async addToCart(itemId) {
            try {
                const response = await fetch('{{ route("customer.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menu_item_id: itemId, quantity: 1 })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Show success message
                    this.messages.push({
                        role: 'assistant',
                        content: '✅ ' + data.message,
                        time: this.getCurrentTime()
                    });
                    this.scrollToBottom();
                    
                    // Update cart badge
                    if (data.cart_count) {
                        this.updateCartBadge(data.cart_count);
                    }
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: '❌ ' + (data.message || 'Gagal menambahkan ke keranjang'),
                        time: this.getCurrentTime()
                    });
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.messages.push({
                    role: 'assistant',
                    content: '❌ Terjadi kesalahan. Coba lagi ya!',
                    time: this.getCurrentTime()
                });
                this.scrollToBottom();
            }
        },
        
        updateCartBadge(count) {
            const badges = document.querySelectorAll('[data-cart-badge]');
            badges.forEach(badge => {
                badge.textContent = count;
                if (count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        },
        
        getCurrentTime() {
            return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('chatMessages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    }
}
</script>
@endpush
