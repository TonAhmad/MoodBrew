{{-- Contoh implementasi AI Chat di halaman customer --}}
@extends('layouts.custLayout')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    {{-- AI Chat Widget --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 text-coffee-800">🤖 AI Coffee Assistant</h2>
        
        {{-- Chat Messages --}}
        <div id="chat-messages" class="h-96 overflow-y-auto mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="text-center text-gray-500 py-8">
                <p>Tanya apa saja tentang menu kopi kami!</p>
                <p class="text-sm mt-2">Contoh: "Kopi apa yang cocok untuk pagi hari?"</p>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="flex gap-2">
            <input 
                type="text" 
                id="chat-input" 
                placeholder="Tanya sesuatu..."
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-coffee-500"
            >
            <button 
                id="send-btn"
                class="px-6 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition"
            >
                Kirim
            </button>
        </div>
    </div>

    {{-- Mood Analyzer --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-coffee-800">😊 Mood Analyzer</h2>
        
        <form id="mood-form">
            <textarea 
                id="mood-input"
                rows="3"
                placeholder="Ceritakan perasaan kamu hari ini..."
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-coffee-500"
            ></textarea>
            
            <button 
                type="submit"
                class="mt-3 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
            >
                Analisa Mood
            </button>
        </form>

        {{-- Mood Result --}}
        <div id="mood-result" class="mt-4 hidden">
            <div class="p-4 bg-purple-50 rounded-lg">
                <h3 class="font-bold text-lg mb-2">Mood Terdeteksi:</h3>
                <div id="mood-display" class="space-y-2"></div>
                
                <button 
                    id="get-recommendation-btn"
                    class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                >
                    Lihat Rekomendasi Menu
                </button>
            </div>
        </div>

        {{-- Recommendations --}}
        <div id="recommendations" class="mt-4 hidden">
            <h3 class="font-bold text-lg mb-3">Rekomendasi untuk Kamu:</h3>
            <div id="recommendations-list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const moodForm = document.getElementById('mood-form');
    const moodInput = document.getElementById('mood-input');
    const moodResult = document.getElementById('mood-result');
    const moodDisplay = document.getElementById('mood-display');
    const recommendationsDiv = document.getElementById('recommendations');
    const recommendationsList = document.getElementById('recommendations-list');
    const getRecommendationBtn = document.getElementById('get-recommendation-btn');

    let conversationHistory = [];
    let detectedMood = null;

    // Chat dengan AI
    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Tambahkan pesan user ke chat
        appendMessage('user', message);
        chatInput.value = '';

        // Disable button sementara
        sendBtn.disabled = true;
        sendBtn.textContent = 'Mengirim...';

        try {
            const response = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    conversation_history: conversationHistory
                })
            });

            const data = await response.json();

            if (data.success) {
                // Tambahkan response AI ke chat
                appendMessage('ai', data.data.response);
                
                // Update history
                conversationHistory.push({role: 'user', content: message});
                conversationHistory.push({role: 'assistant', content: data.data.response});
            } else {
                appendMessage('error', 'Maaf, AI sedang tidak tersedia. Silakan coba lagi.');
            }
        } catch (error) {
            console.error('Chat error:', error);
            appendMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
        } finally {
            sendBtn.disabled = false;
            sendBtn.textContent = 'Kirim';
        }
    }

    function appendMessage(type, message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 ${type === 'user' ? 'text-right' : 'text-left'}`;
        
        const bubble = document.createElement('div');
        bubble.className = `inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
            type === 'user' 
                ? 'bg-coffee-600 text-white' 
                : type === 'error'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-gray-200 text-gray-800'
        }`;
        bubble.textContent = message;
        
        messageDiv.appendChild(bubble);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Analisa mood
    async function analyzeMood(e) {
        e.preventDefault();
        
        const message = moodInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch('/api/ai/analyze-mood', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            if (data.success) {
                const mood = data.data;
                detectedMood = mood.detected_mood;
                
                moodDisplay.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="text-3xl">${getMoodEmoji(mood.detected_mood)}</span>
                        <span class="text-xl font-bold capitalize">${mood.detected_mood}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Confidence: ${(mood.confidence * 100).toFixed(0)}%
                    </div>
                    <div class="mt-2 text-sm">
                        ${mood.reasoning}
                    </div>
                `;
                
                moodResult.classList.remove('hidden');
                recommendationsDiv.classList.add('hidden');
            }
        } catch (error) {
            console.error('Mood analysis error:', error);
            alert('Gagal menganalisa mood. Silakan coba lagi.');
        }
    }

    // Dapatkan rekomendasi
    async function getRecommendations() {
        if (!detectedMood) return;

        try {
            const response = await fetch('/api/ai/recommend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mood: detectedMood })
            });

            const data = await response.json();

            if (data.success) {
                const recs = data.data.recommendations;
                
                recommendationsList.innerHTML = recs.map(rec => `
                    <div class="bg-white border rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold">${rec.name}</h4>
                            <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded">
                                ${(rec.match_score * 100).toFixed(0)}% match
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">${rec.reason}</p>
                    </div>
                `).join('');
                
                recommendationsDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Recommendation error:', error);
            alert('Gagal mendapatkan rekomendasi. Silakan coba lagi.');
        }
    }

    function getMoodEmoji(mood) {
        const emojis = {
            happy: '😊',
            sad: '😢',
            stressed: '😰',
            relaxed: '😌',
            energetic: '⚡',
            calm: '🧘'
        };
        return emojis[mood] || '😊';
    }

    // Event listeners
    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
    moodForm.addEventListener('submit', analyzeMood);
    getRecommendationBtn.addEventListener('click', getRecommendations);
});
</script>
@endpush
@endsection
