@extends('layouts.adminLayout')

@section('title', 'Vibe Wall Moderation')
@section('page-title', 'Vibe Wall')

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
                    <h4 class="font-semibold text-yellow-800">Auto-Moderation Tidak Aktif</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Fitur AI Sentiment belum dikonfigurasi. Semua entry memerlukan moderasi manual.
                        Sentiment score tidak akan di-generate otomatis.
                    </p>
                </div>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-2xl font-bold text-brew-dark">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-orange-500">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Approved</p>
                <p class="text-2xl font-bold text-green-500">{{ $stats['approved'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Featured</p>
                <p class="text-2xl font-bold text-purple-500">{{ $stats['featured'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Positive 😊</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['positive'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Negative 😔</p>
                <p class="text-2xl font-bold text-red-500">{{ $stats['negative'] ?? 0 }}</p>
            </div>
        </div>

        {{-- Filter & Actions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('admin.vibewall.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>Featured</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sentiment</label>
                    <select name="sentiment"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="">Semua</option>
                        <option value="positive" {{ request('sentiment') === 'positive' ? 'selected' : '' }}>Positive</option>
                        <option value="neutral" {{ request('sentiment') === 'neutral' ? 'selected' : '' }}>Neutral</option>
                        <option value="negative" {{ request('sentiment') === 'negative' ? 'selected' : '' }}>Negative</option>
                    </select>
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                    Filter
                </button>

                <a href="{{ route('admin.vibewall.pending') }}"
                    class="px-6 py-2 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors">
                    Review Pending ({{ $stats['pending'] ?? 0 }})
                </a>
            </form>
        </div>

        {{-- Entries List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-brew-dark">Vibe Wall Entries</h3>
            </div>

            @if ($entries->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($entries as $entry)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    {{-- Status Badges --}}
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @if ($entry->is_approved)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                                ✓ Approved
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">
                                                ⏳ Pending
                                            </span>
                                        @endif

                                        @if ($entry->is_featured)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                                ⭐ Featured
                                            </span>
                                        @endif

                                        @php
                                            // Mapping emoji mood ke kategori yang lebih spesifik
                                            $moodEmoji = $entry->mood_emoji ?? '😊';
                                            $sentiment = $entry->sentiment_score ?? 0;
                                            
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
                                        <span class="px-2 py-1 {{ $vibeConfig['bg'] }} {{ $vibeConfig['text'] }} text-xs rounded-full">
                                            {{ $vibeConfig['emoji'] }} {{ $vibeConfig['label'] }} (Score: {{ number_format($sentiment, 2) }})
                                        </span>
                                    </div>

                                    {{-- Content --}}
                                    <p class="text-brew-dark text-lg mb-2">{{ $entry->content }}</p>

                                    {{-- Meta --}}
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span>
                                            <span class="font-medium">{{ $entry->display_name ?? 'Anonymous' }}</span>
                                        </span>
                                        <span>{{ $entry->created_at->diffForHumans() }}</span>
                                        @if ($entry->user)
                                            <span class="text-brew-gold">Registered User</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-col gap-2">
                                    @if (!$entry->is_approved)
                                        <form action="{{ route('admin.vibewall.approve', $entry) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.vibewall.featured', $entry) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full px-4 py-2 {{ $entry->is_featured ? 'bg-gray-200 text-gray-700' : 'bg-purple-500 text-white' }} text-sm rounded-lg hover:opacity-80 transition-colors">
                                            {{ $entry->is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                    </form>

                                    @if ($aiAvailable)
                                        <form action="{{ route('admin.vibewall.analyze', $entry) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                                {{ $entry->sentiment_score == 0 ? 'Analyze' : 'Re-analyze' }}
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.vibewall.reject', $entry) }}" method="POST"
                                        onsubmit="return confirm('Hapus entry ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-red-100 text-red-600 text-sm rounded-lg hover:bg-red-200 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-200">
                    {{ $entries->links() }}
                </div>
            @else
                <div class="text-center py-16 text-gray-500">
                    <span class="text-5xl block mb-4">💭</span>
                    <p class="text-lg">Belum ada Vibe Wall entries</p>
                    <p class="text-sm mt-2">Entries dari pelanggan akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
@endsection
