@extends('layouts.custLayout')

@section('title', 'Vibe Wall - MoodBrew')

@section('content')
    <div class="pb-20">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold">💭 Vibe Wall</h1>
                    <p class="text-white/80 text-sm">Share your mood, spread good vibes!</p>
                </div>
                <a href="{{ route('customer.vibewall.create') }}" 
                   class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Featured Vibes --}}
        @if($featuredVibes->isNotEmpty())
            <div class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50">
                <h3 class="font-bold text-brew-dark mb-3 flex items-center">
                    <span class="text-xl mr-2">⭐</span> Featured Vibes
                </h3>
                <div class="flex space-x-3 overflow-x-auto pb-2">
                    @foreach($featuredVibes as $vibe)
                        <div class="flex-shrink-0 w-64 bg-white rounded-xl p-4 shadow-sm border-2 border-yellow-200">
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="text-2xl">✨</span>
                                <div>
                                    <p class="font-semibold text-brew-dark text-sm">{{ $vibe->display_name ?? 'Anonymous' }}</p>
                                    <p class="text-xs text-gray-400">{{ $vibe->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm line-clamp-3">{{ $vibe->content }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Post New Vibe CTA --}}
        <div class="p-4">
            <a href="{{ route('customer.vibewall.create') }}" 
               class="block bg-white rounded-xl shadow-sm p-4 border-2 border-dashed border-gray-300 hover:border-brew-gold transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-brew-cream rounded-full flex items-center justify-center">
                        <span class="text-2xl">✨</span>
                    </div>
                    <div>
                        <p class="font-semibold text-brew-dark">Bagikan Mood-mu!</p>
                        <p class="text-gray-500 text-sm">Tap untuk menulis vibe baru</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Vibe Wall Feed --}}
        <div class="p-4">
            <h3 class="font-bold text-brew-dark mb-3">🌊 Recent Vibes</h3>
            
            @if($vibes->isEmpty())
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">💭</span>
                    </div>
                    <p class="text-gray-500">Belum ada vibe</p>
                    <p class="text-gray-400 text-sm">Jadilah yang pertama share mood-mu!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($vibes as $vibe)
                        <div class="bg-white rounded-xl shadow-sm p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-brew-cream rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">💭</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-semibold text-brew-dark">{{ $vibe->display_name ?? 'Anonymous' }}</p>
                                        <span class="text-xs text-gray-400">{{ $vibe->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 mt-2">{{ $vibe->content }}</p>
                                    
                                    {{-- Sentiment Badge --}}
                                    @php
                                        $sentimentLabel = $vibe->sentiment_score >= 0.6 ? 'positive' : ($vibe->sentiment_score <= 0.4 ? 'negative' : 'neutral');
                                        $sentimentConfig = match($sentimentLabel) {
                                            'positive' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'emoji' => '😊'],
                                            'negative' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'emoji' => '😔'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'emoji' => '😐'],
                                        };
                                    @endphp
                                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full {{ $sentimentConfig['bg'] }} {{ $sentimentConfig['text'] }}">
                                        {{ $sentimentConfig['emoji'] }} {{ ucfirst($sentimentLabel) }} vibes
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $vibes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
