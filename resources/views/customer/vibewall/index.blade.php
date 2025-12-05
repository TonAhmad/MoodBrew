@extends('layouts.custLayout')

@section('title', 'Vibe Wall - MoodBrew')

@section('content')
    <div class="min-h-screen bg-gray-50 pb-20">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold flex items-center">
                            <span class="mr-2">💭</span> Vibe Wall
                        </h1>
                        <p class="text-purple-100 text-sm mt-1">Share your mood, spread good vibes!</p>
                    </div>
                    <a href="{{ route('customer.vibewall.create') }}" 
                       class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-all hover:scale-105 shadow-lg">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            {{-- Featured Vibes --}}
            @if($featuredVibes->isNotEmpty())
                <div class="mt-6">
                    <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                        <span class="text-2xl mr-2">⭐</span> Featured Vibes
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($featuredVibes as $vibe)
                            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-5 shadow-md border-2 border-yellow-200 hover:shadow-lg transition-all">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-xl">✨</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-brew-dark text-sm truncate">{{ $vibe->display_name ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-gray-500">{{ $vibe->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="text-gray-800 text-sm leading-relaxed line-clamp-4">{{ $vibe->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Post New Vibe CTA --}}
            <div class="mt-6">
                <a href="{{ route('customer.vibewall.create') }}" 
                   class="block bg-white rounded-2xl shadow-sm p-5 border-2 border-dashed border-gray-300 hover:border-brew-gold hover:shadow-md transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-brew-cream to-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">✨</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-brew-dark text-lg">Bagikan Mood-mu!</p>
                            <p class="text-gray-500 text-sm">Tap untuk menulis vibe baru</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>

            {{-- Vibe Wall Feed --}}
            <div class="mt-6 mb-6">
                <h3 class="font-bold text-brew-dark mb-4 flex items-center text-lg">
                    <span class="text-2xl mr-2">🌊</span> Recent Vibes
                </h3>
                
                @if($vibes->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-5xl">💭</span>
                        </div>
                        <p class="text-gray-600 font-medium text-lg mb-2">Belum ada vibe</p>
                        <p class="text-gray-400">Jadilah yang pertama share mood-mu!</p>
                        <a href="{{ route('customer.vibewall.create') }}" 
                           class="inline-flex items-center mt-6 px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-400 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Post First Vibe
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($vibes as $vibe)
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all p-5 border border-gray-100">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">💭</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-bold text-brew-dark">{{ $vibe->display_name ?? 'Anonymous' }}</p>
                                            <span class="text-xs text-gray-400">{{ $vibe->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed mb-3">{{ $vibe->content }}</p>
                                        
                                        {{-- Sentiment Badge --}}
                                        @php
                                            // Mapping emoji mood ke kategori yang lebih spesifik
                                            $moodEmoji = $vibe->mood_emoji ?? '😊';
                                            $vibeConfig = match($moodEmoji) {
                                                '😊' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'emoji' => '😊', 'label' => 'Happy'],
                                                '😌' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'emoji' => '😌', 'label' => 'Relaxed'],
                                                '⚡' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'emoji' => '⚡', 'label' => 'Energetic'],
                                                '😴' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'emoji' => '😴', 'label' => 'Tired'],
                                                '😤' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'emoji' => '😤', 'label' => 'Stressed'],
                                                '🥰' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-700', 'emoji' => '🥰', 'label' => 'Loved'],
                                                '🤔' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'emoji' => '🤔', 'label' => 'Thoughtful'],
                                                '☕' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'emoji' => '☕', 'label' => 'Coffee Time'],
                                                default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'emoji' => '😌', 'label' => 'Chill'],
                                            };
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full {{ $vibeConfig['bg'] }} {{ $vibeConfig['text'] }}">
                                                <span class="mr-1">{{ $vibeConfig['emoji'] }}</span>
                                                {{ $vibeConfig['label'] }} vibes
                                            </span>
                                        </div>
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
    </div>
@endsection
