@extends('layouts.cashierLayout')

@section('title', 'Empathy Radar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Empathy Radar</h1>
            <p class="text-gray-600">Analisis sentimen pelanggan secara real-time</p>
        </div>
        <a href="{{ route('cashier.empathy.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Interaksi
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['totalToday'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Interaksi Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['needsAttention'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Perlu Perhatian</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                    {{ ($stats['averageSentiment'] ?? 0.5) >= 0.55 ? 'bg-green-100' : (($stats['averageSentiment'] ?? 0.5) >= 0.45 ? 'bg-yellow-100' : 'bg-red-100') }}">
                    <span class="text-lg">
                        {{ ($stats['averageSentiment'] ?? 0.5) >= 0.55 ? '😊' : (($stats['averageSentiment'] ?? 0.5) >= 0.45 ? '😐' : '😔') }}
                    </span>
                </div>
                <div>
                    <p class="text-lg font-bold text-brew-dark">{{ $stats['sentimentLabel'] ?? 'Netral' }}</p>
                    <p class="text-xs text-gray-500">Rata-rata Mood</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brew-gold/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-brew-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-brew-dark">{{ $distribution['positive_percentage'] ?? 0 }}%</p>
                    <p class="text-xs text-gray-500">Positif Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Sentiment Distribution -->
    @if($distribution['total'] > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="font-semibold text-brew-dark mb-4">Distribusi Sentimen Hari Ini</h3>
        <div class="flex h-4 rounded-full overflow-hidden bg-gray-100">
            @if($distribution['positive_percentage'] > 0)
            <div class="bg-green-500 transition-all" style="width: {{ $distribution['positive_percentage'] }}%"></div>
            @endif
            @if($distribution['neutral_percentage'] > 0)
            <div class="bg-yellow-400 transition-all" style="width: {{ $distribution['neutral_percentage'] }}%"></div>
            @endif
            @if($distribution['negative_percentage'] > 0)
            <div class="bg-red-500 transition-all" style="width: {{ $distribution['negative_percentage'] }}%"></div>
            @endif
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-600">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-green-500 rounded"></span>
                Positif ({{ $distribution['positive'] }})
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-yellow-400 rounded"></span>
                Netral ({{ $distribution['neutral'] }})
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-red-500 rounded"></span>
                Negatif ({{ $distribution['negative'] }})
            </span>
        </div>
    </div>
    @endif

    <!-- Needs Attention Section -->
    @if($needsAttention->count() > 0)
    <div class="bg-red-50 rounded-xl border border-red-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-red-200 flex items-center gap-2">
            <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
            <h2 class="font-semibold text-red-800">⚠️ Perlu Perhatian ({{ $needsAttention->count() }})</h2>
        </div>
        <div class="divide-y divide-red-100">
            @foreach($needsAttention as $interaction)
            <div class="p-4 bg-red-50/50 hover:bg-red-100/50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full">
                                {{ ucfirst($interaction->interaction_type) }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $interaction->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-800">{{ Str::limit($interaction->customer_message, 200) }}</p>
                        @if($interaction->order)
                        <p class="text-xs text-gray-500 mt-1">
                            Order: #{{ $interaction->order->order_number }}
                        </p>
                        @endif
                    </div>
                    <form action="{{ route('cashier.empathy.handle', $interaction) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_resolved" value="1">
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                            ✓ Tangani
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Today's Interactions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-semibold text-brew-dark">Interaksi Hari Ini</h2>
        </div>

        @if($interactions->isEmpty())
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <p class="text-gray-500">Belum ada interaksi pelanggan hari ini</p>
            <a href="{{ route('cashier.empathy.create') }}" class="inline-flex items-center mt-4 text-brew-gold hover:text-yellow-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Catat Interaksi Pertama
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($interactions as $interaction)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <!-- Sentiment Icon -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        @if(in_array($interaction->sentiment_type, ['positive', 'happy', 'satisfied']))
                            bg-green-100
                        @elseif(in_array($interaction->sentiment_type, ['negative', 'angry', 'frustrated', 'sad']))
                            bg-red-100
                        @else
                            bg-yellow-100
                        @endif">
                        <span class="text-lg">
                            @if(in_array($interaction->sentiment_type, ['positive', 'happy', 'satisfied']))
                                😊
                            @elseif(in_array($interaction->sentiment_type, ['negative', 'angry', 'frustrated', 'sad']))
                                😔
                            @else
                                😐
                            @endif
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                {{ ucfirst($interaction->interaction_type) }}
                            </span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">
                                {{ ucfirst($interaction->channel) }}
                            </span>
                            @if($interaction->resolved_at)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                                ✓ Resolved
                            </span>
                            @endif
                        </div>
                        <p class="text-gray-800 text-sm">{{ Str::limit($interaction->customer_message, 150) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $interaction->created_at->format('H:i') }}</p>

                        @if($interaction->ai_analysis)
                        <div class="mt-2 p-2 bg-purple-50 border border-purple-100 rounded text-xs">
                            <div class="flex items-center gap-1 text-purple-700 font-medium mb-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <span>AI Analysis:</span>
                            </div>
                            <p class="text-purple-700">{{ $interaction->ai_analysis }}</p>
                        </div>
                        @endif

                        @if($interaction->suggested_response)
                        <div class="mt-2 p-2 bg-green-50 border border-green-100 rounded text-xs">
                            <div class="flex items-center gap-1 text-green-700 font-medium mb-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span>Suggested Response:</span>
                            </div>
                            <p class="text-green-700">{{ $interaction->suggested_response }}</p>
                        </div>
                        @endif

                        @if($interaction->staff_notes)
                        <div class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-700">
                            <strong>Catatan:</strong> {{ $interaction->staff_notes }}
                        </div>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        <div class="text-xs text-gray-500">
                            Score: {{ number_format($interaction->sentiment_score * 100, 0) }}%
                        </div>
                        @if($interaction->handledBy)
                        <p class="text-xs text-gray-400 mt-1">
                            by {{ $interaction->handledBy->name }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
