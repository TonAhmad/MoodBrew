@extends('layouts.landingLayout')

@section('title', 'Vibe Wall - MoodBrew')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-purple-600 to-pink-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-4xl md:text-5xl font-bold mb-4">
                    💭 Vibe Wall
                </h1>
                <p class="text-white/90 text-lg max-w-2xl mx-auto">
                    Lihat apa yang dirasakan pengunjung MoodBrew hari ini. Share your vibe dan temukan kesamaan dengan yang lain!
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-8 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-3xl font-bold text-brew-gold">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-600">Total Vibes</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['today'] }}</p>
                    <p class="text-sm text-gray-600">Hari Ini</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['featured'] }}</p>
                    <p class="text-sm text-gray-600">Featured</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Vibes Grid --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($vibes->count() > 0)
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($vibes as $vibe)
                        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all {{ $vibe->is_featured ? 'ring-2 ring-purple-400' : '' }}">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xl">{{ $vibe->mood_emoji ?? '😊' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-brew-dark">{{ $vibe->display_name ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-gray-500">{{ $vibe->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($vibe->is_featured)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">⭐ Featured</span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <p class="text-gray-700 leading-relaxed mb-3">{{ $vibe->content }}</p>

                            {{-- Sentiment Badge --}}
                            @php
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
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full {{ $vibeConfig['bg'] }} {{ $vibeConfig['text'] }}">
                                <span class="mr-1">{{ $vibeConfig['emoji'] }}</span>
                                {{ $vibeConfig['label'] }} vibes
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <span class="text-6xl block mb-4">💭</span>
                    <p class="text-gray-500 text-lg">Belum ada vibes yang dibagikan</p>
                    <p class="text-sm text-gray-400 mt-2">Jadilah yang pertama untuk share your vibe!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 gradient-brew">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-6">
                Bagikan Vibes-mu Juga!
            </h2>
            <p class="text-brew-cream/80 text-lg mb-8">
                Masuk dan ceritakan perasaanmu di MoodBrew. Semua vibes diterima dengan hangat! 💜
            </p>
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center px-8 py-4 bg-brew-gold text-brew-dark font-bold rounded-full hover:bg-yellow-400 transition-all transform hover:scale-105 shadow-lg">
                💭 Share Your Vibe
            </a>
        </div>
    </section>
@endsection
