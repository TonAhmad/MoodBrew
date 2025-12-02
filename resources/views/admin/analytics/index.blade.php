@extends('layouts.adminLayout')

@section('title', 'Mood Analytics')
@section('page-title', 'Mood Analytics')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- AI Status Banner --}}
        @if (!$aiAvailable)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start space-x-3">
                <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-semibold text-yellow-800">Fitur AI Analytics Belum Dikonfigurasi</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Analisis sentiment dan mood prediction memerlukan konfigurasi AI. 
                        Data yang ditampilkan adalah data dasar tanpa analisis AI.
                    </p>
                </div>
            </div>
        @endif

        {{-- Period Selector --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <select name="period" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>90 Hari Terakhir</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Vibe Entries</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['totalVibeEntries'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">
                        💭
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Avg Sentiment</p>
                        <p class="text-2xl font-bold {{ ($stats['avgSentiment'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                            {{ number_format($stats['avgSentiment'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-{{ ($stats['avgSentiment'] ?? 0) >= 0 ? 'green' : 'red' }}-100 rounded-xl flex items-center justify-center text-2xl">
                        {{ ($stats['avgSentiment'] ?? 0) >= 0 ? '😊' : '😔' }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Positive Ratio</p>
                        <p class="text-2xl font-bold text-brew-gold mt-1">{{ $stats['positiveRatio'] ?? 0 }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-brew-cream rounded-xl flex items-center justify-center text-2xl">
                        ✨
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Customer Interactions</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['totalInteractions'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl">
                        🤝
                    </div>
                </div>
            </div>
        </div>

        {{-- Mood Distribution --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">📊 Distribusi Mood Pelanggan</h3>
                @if (!empty($moodDistribution) && array_sum($moodDistribution) > 0)
                    @php
                        $moodConfig = [
                            'happy' => ['label' => 'Happy', 'color' => 'bg-yellow-400', 'emoji' => '😊'],
                            'relaxed' => ['label' => 'Relaxed', 'color' => 'bg-green-400', 'emoji' => '😌'],
                            'energetic' => ['label' => 'Energetic', 'color' => 'bg-orange-400', 'emoji' => '⚡'],
                            'tired' => ['label' => 'Tired', 'color' => 'bg-blue-400', 'emoji' => '😴'],
                            'stressed' => ['label' => 'Stressed', 'color' => 'bg-red-400', 'emoji' => '😰'],
                        ];
                        $totalMoods = array_sum($moodDistribution);
                    @endphp
                    <div class="space-y-4">
                        @foreach ($moodDistribution as $mood => $count)
                            @php
                                $config = $moodConfig[$mood] ?? ['label' => ucfirst($mood), 'color' => 'bg-gray-400', 'emoji' => '🙂'];
                                $percentage = $totalMoods > 0 ? ($count / $totalMoods) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="flex items-center text-sm font-medium">
                                        <span class="mr-2">{{ $config['emoji'] }}</span>
                                        {{ $config['label'] }}
                                    </span>
                                    <span class="text-sm text-gray-600">{{ $count }} ({{ round($percentage) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="{{ $config['color'] }} h-4 rounded-full transition-all duration-500"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <span class="text-4xl block mb-3">📊</span>
                        <p>Belum ada data mood</p>
                        <p class="text-sm mt-1">Data akan muncul setelah ada pesanan dengan mood selection</p>
                    </div>
                @endif
            </div>

            {{-- Customer Interaction Stats --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">🤝 Statistik Interaksi</h3>
                @if (!empty($interactionStats) && $interactionStats['total'] > 0)
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-blue-50 rounded-lg text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $interactionStats['total'] }}</p>
                            <p class="text-sm text-gray-600">Total Interaksi</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $interactionStats['handled'] }}</p>
                            <p class="text-sm text-gray-600">Sudah Ditangani</p>
                        </div>
                    </div>
                    
                    @if (!empty($interactionStats['byType']))
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Berdasarkan Tipe:</h4>
                        <div class="space-y-2">
                            @php
                                $typeLabels = [
                                    'complaint' => ['label' => 'Komplain', 'color' => 'bg-red-400'],
                                    'compliment' => ['label' => 'Pujian', 'color' => 'bg-green-400'],
                                    'suggestion' => ['label' => 'Saran', 'color' => 'bg-blue-400'],
                                    'question' => ['label' => 'Pertanyaan', 'color' => 'bg-yellow-400'],
                                ];
                            @endphp
                            @foreach ($interactionStats['byType'] as $type => $count)
                                @php
                                    $typeConfig = $typeLabels[$type] ?? ['label' => ucfirst($type), 'color' => 'bg-gray-400'];
                                @endphp
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="flex items-center text-sm">
                                        <span class="w-3 h-3 {{ $typeConfig['color'] }} rounded-full mr-2"></span>
                                        {{ $typeConfig['label'] }}
                                    </span>
                                    <span class="font-medium">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($interactionStats['avgResponseTime'] > 0)
                        <div class="mt-4 p-3 bg-brew-cream rounded-lg">
                            <p class="text-sm text-gray-600">Rata-rata Waktu Respons:</p>
                            <p class="text-xl font-bold text-brew-dark">{{ $interactionStats['avgResponseTime'] }} menit</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-gray-500">
                        <span class="text-4xl block mb-3">🤝</span>
                        <p>Belum ada data interaksi</p>
                        <p class="text-sm mt-1">Data dari Empathy Radar akan muncul di sini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sentiment Trend --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-brew-dark mb-4">📈 Trend Sentiment (Vibe Wall)</h3>
            @if (!empty($sentimentTrend) && count($sentimentTrend) > 0)
                <div class="h-64 flex items-end justify-around gap-1">
                    @php
                        $maxSentiment = max(array_map(fn($s) => abs($s['sentiment']), $sentimentTrend)) ?: 1;
                    @endphp
                    @foreach ($sentimentTrend as $dayData)
                        @php
                            $sentiment = $dayData['sentiment'];
                            $isPositive = $sentiment >= 0;
                            $height = (abs($sentiment) / $maxSentiment) * 50; // Max 50% height from center
                        @endphp
                        <div class="flex flex-col items-center flex-1 max-w-16">
                            {{-- Positive bar --}}
                            <div class="w-full h-24 flex items-end justify-center">
                                @if ($isPositive)
                                    <div class="w-3/4 bg-green-400 rounded-t transition-all duration-300"
                                        style="height: {{ $height }}%"
                                        title="{{ $dayData['date'] }}: {{ $sentiment }}"></div>
                                @endif
                            </div>
                            {{-- Center line --}}
                            <div class="w-full h-0.5 bg-gray-300"></div>
                            {{-- Negative bar --}}
                            <div class="w-full h-24 flex items-start justify-center">
                                @if (!$isPositive)
                                    <div class="w-3/4 bg-red-400 rounded-b transition-all duration-300"
                                        style="height: {{ $height }}%"
                                        title="{{ $dayData['date'] }}: {{ $sentiment }}"></div>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 mt-1 truncate w-full text-center">
                                {{ \Carbon\Carbon::parse($dayData['date'])->format('d/m') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-center gap-6 mt-4 text-sm">
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-green-400 rounded mr-2"></span>
                        Positive
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-red-400 rounded mr-2"></span>
                        Negative
                    </span>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <span class="text-4xl block mb-3">📈</span>
                    <p>Belum ada data sentiment trend</p>
                    <p class="text-sm mt-1">Data akan muncul setelah ada Vibe Wall entries</p>
                </div>
            @endif
        </div>

        {{-- Peak Mood Times --}}
        @if (!empty($peakMoodTimes) && count($peakMoodTimes) > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">⏰ Peak Mood Times</h3>
                <p class="text-sm text-gray-600 mb-4">Distribusi mood pelanggan berdasarkan jam pemesanan</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-2 px-3 text-left">Jam</th>
                                <th class="py-2 px-3 text-center">😊 Happy</th>
                                <th class="py-2 px-3 text-center">😌 Relaxed</th>
                                <th class="py-2 px-3 text-center">⚡ Energetic</th>
                                <th class="py-2 px-3 text-center">😴 Tired</th>
                                <th class="py-2 px-3 text-center">😰 Stressed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peakMoodTimes as $hour => $moods)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-3 font-medium">{{ sprintf('%02d:00', $hour) }}</td>
                                    <td class="py-2 px-3 text-center">{{ $moods['happy'] ?? 0 }}</td>
                                    <td class="py-2 px-3 text-center">{{ $moods['relaxed'] ?? 0 }}</td>
                                    <td class="py-2 px-3 text-center">{{ $moods['energetic'] ?? 0 }}</td>
                                    <td class="py-2 px-3 text-center">{{ $moods['tired'] ?? 0 }}</td>
                                    <td class="py-2 px-3 text-center">{{ $moods['stressed'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- AI Under Maintenance Notice --}}
        @if (!$aiAvailable)
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-sm p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">🤖 Fitur AI Analytics</h3>
                        <p class="text-white/80">
                            Fitur-fitur berikut akan tersedia setelah AI dikonfigurasi:
                        </p>
                        <ul class="mt-3 space-y-1 text-sm text-white/90">
                            <li>• Mood Prediction - Prediksi mood pelanggan berdasarkan waktu & cuaca</li>
                            <li>• Sentiment Deep Analysis - Analisis mendalam dari Vibe Wall</li>
                            <li>• Customer Behavior Insights - Pola perilaku pelanggan</li>
                            <li>• Recommendation Optimization - Optimasi rekomendasi menu</li>
                        </ul>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-5xl">🔬</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
