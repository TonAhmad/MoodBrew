{{--
    AI Mood Recommendation Page
    
    Halaman full-screen untuk customer memilih mood dan mendapat rekomendasi.
    Ini adalah contoh implementasi UI untuk AI recommendation.
    
    Route: /order/mood
--}}
@extends('layouts.app')

@section('title', 'Pilih Mood - MoodBrew')

@section('content')
    <div x-data="moodRecommendation()" class="min-h-screen bg-gradient-to-b from-brew-cream to-white">
        {{-- Header --}}
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="container mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('customer.home') }}" class="flex items-center space-x-2">
                    <span class="text-2xl">☕</span>
                    <span class="font-bold text-brew-dark">MoodBrew</span>
                </a>
                <a href="{{ route('customer.home') }}" class="text-brew-brown hover:text-brew-dark">
                    Skip →
                </a>
            </div>
        </header>

        <div class="container mx-auto px-4 pt-24 pb-8">
            {{-- Step 1: Mood Selection --}}
            <div x-show="step === 1" x-transition class="max-w-2xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-brew-dark mb-4">
                    Bagaimana perasaanmu hari ini? 💭
                </h1>
                <p class="text-gray-600 mb-8">
                    Pilih mood yang paling menggambarkan perasaanmu, dan kami akan carikan minuman yang cocok!
                </p>

                {{-- Mood Options Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                    <template x-for="mood in moods" :key="mood.id">
                        <button @click="selectMood(mood)"
                            :class="selectedMood?.id === mood.id ? 'ring-2 ring-brew-gold bg-brew-cream' :
                                'bg-white hover:bg-gray-50'"
                            class="p-6 rounded-2xl border border-gray-200 transition-all duration-200 text-center group">
                            <span class="text-4xl mb-3 block group-hover:scale-110 transition-transform"
                                x-text="mood.emoji"></span>
                            <p class="font-semibold text-brew-dark" x-text="mood.label"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="mood.description"></p>
                        </button>
                    </template>
                </div>

                {{-- Custom Mood Input --}}
                <div class="relative">
                    <p class="text-gray-500 text-sm mb-3">Atau ceritakan perasaanmu:</p>
                    <div class="flex space-x-2">
                        <input x-model="customMood" type="text" placeholder="Contoh: Saya lelah setelah meeting..."
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold">
                        <button @click="submitCustomMood" :disabled="!customMood.trim()"
                            class="px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-500 transition-colors disabled:opacity-50">
                            Kirim
                        </button>
                    </div>
                </div>

                {{-- Next Button --}}
                <button x-show="selectedMood" @click="getRecommendation"
                    class="mt-8 px-8 py-4 bg-brew-dark text-brew-cream font-semibold rounded-xl hover:bg-brew-brown transition-colors">
                    Cari Rekomendasi ☕
                </button>
            </div>

            {{-- Step 2: Loading --}}
            <div x-show="step === 2" x-transition class="max-w-md mx-auto text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 relative">
                    <div class="w-full h-full bg-brew-cream rounded-full animate-pulse flex items-center justify-center">
                        <span class="text-5xl animate-bounce">☕</span>
                    </div>
                    <div class="absolute inset-0 border-4 border-brew-gold/30 rounded-full animate-ping">
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-brew-dark mb-2">Sedang Menganalisis Mood...</h2>
                <p class="text-gray-600">Brew sedang mencari minuman yang cocok untukmu</p>

                {{-- Fun Loading Messages --}}
                <div class="mt-6 space-y-2 text-sm text-gray-500" x-data="{ messages: ['Membaca pikiranmu...', 'Mencampur formula rahasia...', 'Menyiapkan rekomendasi...'], current: 0 }" x-init="setInterval(() => current = (current + 1) % messages.length, 1500)">
                    <p x-text="messages[current]" class="animate-pulse"></p>
                </div>
            </div>

            {{-- Step 3: Recommendations --}}
            <div x-show="step === 3" x-transition class="max-w-3xl mx-auto">
                <div class="text-center mb-8">
                    <span class="text-5xl mb-4 block" x-text="selectedMood?.emoji || '☕'"></span>
                    <h2 class="text-2xl font-bold text-brew-dark mb-2">
                        Rekomendasi untuk mood "<span x-text="selectedMood?.label || 'Kamu'"></span>"
                    </h2>
                    <p class="text-gray-600" x-text="aiMessage"></p>
                </div>

                {{-- Recommendation Cards --}}
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <template x-for="(menu, index) in recommendations" :key="menu.id">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow"
                            :class="index === 0 ? 'ring-2 ring-brew-gold' : ''">
                            {{-- Best Match Badge --}}
                            <div x-show="index === 0"
                                class="bg-brew-gold text-brew-dark text-center py-1 text-sm font-medium">
                                ⭐ Best Match
                            </div>

                            {{-- Menu Image Placeholder --}}
                            <div
                                class="h-40 bg-gradient-to-br from-brew-cream to-brew-gold/30 flex items-center justify-center">
                                <span class="text-6xl">☕</span>
                            </div>

                            {{-- Menu Info --}}
                            <div class="p-5">
                                <h3 class="font-bold text-lg text-brew-dark mb-1" x-text="menu.name"></h3>
                                <p class="text-sm text-gray-500 mb-2" x-text="menu.category"></p>
                                <p class="text-brew-gold font-bold text-xl mb-4"
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(menu.price)"></p>

                                {{-- Flavor Tags --}}
                                <div class="flex flex-wrap gap-1 mb-4" x-show="menu.flavor_profile?.flavor_notes">
                                    <template x-for="tag in (menu.flavor_profile?.flavor_notes || [])"
                                        :key="tag">
                                        <span class="px-2 py-0.5 bg-brew-cream text-brew-brown text-xs rounded-full"
                                            x-text="tag"></span>
                                    </template>
                                </div>

                                <button @click="addToCart(menu)"
                                    class="w-full py-3 bg-brew-dark text-brew-cream font-semibold rounded-xl hover:bg-brew-brown transition-colors">
                                    + Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button @click="resetSelection"
                        class="px-6 py-3 border border-brew-brown text-brew-brown rounded-xl hover:bg-brew-cream transition-colors">
                        ← Pilih Mood Lain
                    </button>
                    <a href="{{ route('customer.home') }}"
                        class="px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-500 transition-colors text-center">
                        Lanjut Belanja →
                    </a>
                </div>
            </div>

            {{-- Error State --}}
            <div x-show="step === 'error'" x-transition class="max-w-md mx-auto text-center py-16">
                <span class="text-6xl mb-4 block">😔</span>
                <h2 class="text-2xl font-bold text-brew-dark mb-2">Oops, Ada Masalah</h2>
                <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
                <button @click="resetSelection"
                    class="px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-500 transition-colors">
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>

    <script>
        function moodRecommendation() {
            return {
                step: 1,
                selectedMood: null,
                customMood: '',
                recommendations: [],
                aiMessage: '',
                errorMessage: '',

                moods: [{
                        id: 'happy',
                        emoji: '😊',
                        label: 'Senang',
                        description: 'Feeling good!',
                        prompt: 'Saya sedang senang dan ingin menikmati sesuatu yang istimewa'
                    },
                    {
                        id: 'tired',
                        emoji: '😴',
                        label: 'Lelah',
                        description: 'Butuh energi',
                        prompt: 'Saya sedang lelah dan butuh minuman yang bisa memberikan energi'
                    },
                    {
                        id: 'stressed',
                        emoji: '😰',
                        label: 'Stress',
                        description: 'Perlu relax',
                        prompt: 'Saya sedang stress dan butuh sesuatu yang menenangkan'
                    },
                    {
                        id: 'focused',
                        emoji: '🎯',
                        label: 'Fokus',
                        description: 'Mau produktif',
                        prompt: 'Saya ingin fokus dan produktif, butuh minuman yang bisa membantu konsentrasi'
                    },
                    {
                        id: 'chill',
                        emoji: '😌',
                        label: 'Santai',
                        description: 'Me time',
                        prompt: 'Saya ingin bersantai dan menikmati waktu sendiri'
                    },
                    {
                        id: 'sad',
                        emoji: '😢',
                        label: 'Sedih',
                        description: 'Butuh comfort',
                        prompt: 'Saya sedang merasa sedih dan butuh sesuatu yang menghangatkan hati'
                    },
                ],

                selectMood(mood) {
                    this.selectedMood = mood;
                },

                submitCustomMood() {
                    if (!this.customMood.trim()) return;

                    this.selectedMood = {
                        id: 'custom',
                        emoji: '💭',
                        label: 'Custom',
                        prompt: this.customMood.trim()
                    };

                    this.getRecommendation();
                },

                async getRecommendation() {
                    if (!this.selectedMood) return;

                    this.step = 2; // Loading

                    try {
                        const response = await fetch('/order/ai/recommend', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                mood: this.selectedMood.prompt
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.recommendations = data.recommendations || [];
                            this.aiMessage = data.message || 'Berikut rekomendasi kami:';
                            this.step = 3;
                        } else {
                            throw new Error(data.message || 'Gagal mendapatkan rekomendasi');
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        this.errorMessage = error.message || 'Terjadi kesalahan. Silakan coba lagi.';
                        this.step = 'error';
                    }
                },

                addToCart(menu) {
                    // TODO: Integrate with cart system
                    alert(`${menu.name} ditambahkan ke keranjang!`);
                },

                resetSelection() {
                    this.step = 1;
                    this.selectedMood = null;
                    this.customMood = '';
                    this.recommendations = [];
                    this.aiMessage = '';
                    this.errorMessage = '';
                },
            };
        }
    </script>
@endsection
