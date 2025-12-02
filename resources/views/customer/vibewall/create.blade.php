@extends('layouts.custLayout')

@section('title', 'Share Your Vibe - MoodBrew')

@section('content')
    <div class="pb-20">
        {{-- Header --}}
        <div class="bg-white p-4 border-b border-gray-100 flex items-center">
            <a href="{{ route('customer.vibewall.index') }}" class="mr-3">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-brew-dark">✨ Share Your Vibe</h1>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('customer.vibewall.store') }}" method="POST" class="p-4 space-y-4" x-data="vibeForm()">
            @csrf
            
            {{-- Mood Emoji Selector --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-brew-dark mb-3">Pilih Mood Emoji</h3>
                <div class="grid grid-cols-4 gap-3">
                    @foreach($moodEmojis as $mood)
                        <label class="cursor-pointer">
                            <input type="radio" name="mood_emoji" value="{{ $mood['emoji'] }}" class="hidden peer" 
                                {{ old('mood_emoji', '😊') === $mood['emoji'] ? 'checked' : '' }}>
                            <div class="p-3 rounded-xl border-2 border-gray-200 text-center transition-all
                                        peer-checked:border-brew-gold peer-checked:bg-brew-cream hover:border-gray-300">
                                <span class="text-3xl block">{{ $mood['emoji'] }}</span>
                                <span class="text-xs text-gray-500 mt-1 block">{{ $mood['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Message Input --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-brew-dark mb-3">Ceritakan Mood-mu</h3>
                <textarea name="message" rows="4" maxlength="280" required x-model="message"
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50 resize-none"
                    placeholder="Lagi ngerasa gimana hari ini? Share your vibe! ✨">{{ old('message') }}</textarea>
                <div class="flex justify-between items-center mt-2">
                    <p class="text-xs text-gray-400">Tips: Ceritakan pengalamanmu di MoodBrew!</p>
                    <span class="text-xs" :class="message.length > 250 ? 'text-orange-500' : 'text-gray-400'"
                        x-text="message.length + '/280'"></span>
                </div>
                @error('message')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name (Optional) --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-brew-dark mb-3">Nama (Opsional)</h3>
                <input type="text" name="customer_name" value="{{ old('customer_name', session('customer_name')) }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50"
                    placeholder="Tampilkan sebagai...">
                <p class="text-xs text-gray-400 mt-2">Kosongkan untuk post sebagai "Anonymous"</p>
            </div>

            {{-- Submit Button --}}
            <button type="submit" 
                class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors">
                ✨ Post Vibe
            </button>

            {{-- Note --}}
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <span class="text-xl">ℹ️</span>
                    <div>
                        <p class="text-blue-800 text-sm font-medium">Moderasi</p>
                        <p class="text-blue-600 text-xs mt-1">
                            Vibe-mu akan ditinjau oleh tim kami sebelum tampil di Vibe Wall. 
                            Pastikan kontenmu positif dan tidak mengandung SARA.
                        </p>
                    </div>
                </div>
            </div>
        </form>

        {{-- My Recent Vibes --}}
        @if($myVibes->isNotEmpty())
            <div class="p-4">
                <h3 class="font-semibold text-brew-dark mb-3">📝 Vibe-mu</h3>
                <div class="space-y-3">
                    @foreach($myVibes->take(5) as $vibe)
                        <div class="bg-white rounded-xl shadow-sm p-4 {{ !$vibe->is_approved ? 'opacity-70' : '' }}">
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl">💭</span>
                                <div class="flex-1">
                                    <p class="text-gray-700 text-sm">{{ $vibe->content }}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-gray-400">{{ $vibe->created_at->diffForHumans() }}</span>
                                        @if(!$vibe->is_approved)
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">
                                                ⏳ Menunggu Review
                                            </span>
                                        @elseif($vibe->is_featured)
                                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">
                                                ⭐ Featured
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
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
