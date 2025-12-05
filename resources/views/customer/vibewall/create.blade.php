@extends('layouts.custLayout')

@section('title', 'Share Your Vibe - MoodBrew')

@section('content')
    <div class="min-h-screen bg-gray-50 pb-20">
        {{-- Header --}}
        <div class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center">
                <a href="{{ route('customer.vibewall.index') }}" class="mr-3 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-brew-dark flex items-center">
                    <span class="mr-2">✨</span> Share Your Vibe
                </h1>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('customer.vibewall.store') }}" method="POST" class="mt-6 space-y-5" x-data="vibeForm()">
                @csrf
                
                {{-- Mood Emoji Selector --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                        <span class="mr-2">😊</span> Pilih Mood Emoji
                    </h3>
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                        @foreach($moodEmojis as $mood)
                            <label class="cursor-pointer group">
                                <input type="radio" name="mood_emoji" value="{{ $mood['emoji'] }}" class="hidden peer" 
                                    {{ old('mood_emoji', '😊') === $mood['emoji'] ? 'checked' : '' }}>
                                <div class="p-4 rounded-xl border-2 border-gray-200 text-center transition-all
                                            peer-checked:border-brew-gold peer-checked:bg-brew-cream peer-checked:shadow-md
                                            hover:border-gray-300 group-hover:scale-105">
                                    <span class="text-4xl block mb-1">{{ $mood['emoji'] }}</span>
                                    <span class="text-xs text-gray-600 font-medium block">{{ $mood['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Message Input --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                        <span class="mr-2">💭</span> Ceritakan Mood-mu
                    </h3>
                    <textarea name="message" rows="6" maxlength="280" required x-model="message"
                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold/50 focus:border-brew-gold resize-none text-gray-700"
                        placeholder="Lagi ngerasa gimana hari ini? Share your vibe! ✨">{{ old('message') }}</textarea>
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-sm text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ceritakan pengalamanmu di MoodBrew!
                        </p>
                        <span class="text-sm font-medium" :class="message.length > 250 ? 'text-orange-600' : 'text-gray-500'"
                            x-text="message.length + '/280'"></span>
                    </div>
                    @error('message')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Name (Optional) --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                        <span class="mr-2">👤</span> Nama (Opsional)
                    </h3>
                    <input type="text" name="customer_name" value="{{ old('customer_name', session('customer_name')) }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold/50 focus:border-brew-gold"
                        placeholder="Tampilkan sebagai...">
                    <p class="text-sm text-gray-500 mt-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kosongkan untuk post sebagai "Anonymous"
                    </p>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-brew-gold to-yellow-400 text-brew-dark font-bold rounded-xl hover:shadow-lg transition-all transform hover:scale-105 flex items-center justify-center text-lg">
                    <span class="mr-2">✨</span> Post Vibe
                </button>

                {{-- Note --}}
                <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl flex-shrink-0">ℹ️</span>
                        <div>
                            <p class="text-blue-900 font-semibold mb-1">Moderasi Konten</p>
                            <p class="text-blue-700 text-sm leading-relaxed">
                                Vibe-mu akan ditinjau oleh tim kami sebelum tampil di Vibe Wall. 
                                Pastikan kontenmu positif dan tidak mengandung SARA.
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            {{-- My Recent Vibes --}}
            @if($myVibes->isNotEmpty())
                <div class="mt-8 mb-6">
                    <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                        <span class="mr-2">📝</span> Vibe-mu
                    </h3>
                    <div class="space-y-3">
                        @foreach($myVibes->take(5) as $vibe)
                            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 {{ !$vibe->is_approved ? 'opacity-70' : '' }}">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xl">💭</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-700 leading-relaxed">{{ $vibe->content }}</p>
                                        <div class="flex items-center justify-between mt-3">
                                            <span class="text-xs text-gray-400">{{ $vibe->created_at->diffForHumans() }}</span>
                                            @if(!$vibe->is_approved)
                                                <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">
                                                    ⏳ Menunggu Review
                                                </span>
                                            @elseif($vibe->is_featured)
                                                <span class="px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-full font-medium">
                                                    ⭐ Featured
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">
                                                    ✅ Published
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function vibeForm() {
    return {
        message: '{{ old("message", "") }}'
    }
}
</script>
@endpush
