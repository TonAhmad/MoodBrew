@extends('layouts.adminLayout')

@section('title', 'Pending Moderation')
@section('page-title', 'Pending Moderation')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Back Link --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.vibewall.index') }}"
                class="flex items-center text-brew-brown hover:text-brew-dark transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Vibe Wall
            </a>

            <span class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg font-medium">
                {{ $entries->count() }} entries menunggu review
            </span>
        </div>

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
                        AI Sentiment belum dikonfigurasi. Moderasi manual diperlukan untuk semua entries.
                    </p>
                </div>
            </div>
        @endif

        {{-- Quick Actions Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="font-semibold text-blue-800 mb-2">💡 Tips Moderasi</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>Approve</strong> - Entry akan tampil di Vibe Wall publik</li>
                <li>• <strong>Feature</strong> - Entry akan ditampilkan lebih menonjol</li>
                <li>• <strong>Delete</strong> - Hapus entry yang tidak sesuai (spam, offensive, dll)</li>
            </ul>
        </div>

        {{-- Pending Entries --}}
        @if ($entries->count() > 0)
            <div class="space-y-4">
                @foreach ($entries as $entry)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex-1">
                                {{-- Content --}}
                                <p class="text-brew-dark text-lg mb-3 leading-relaxed">
                                    "{{ $entry->content }}"
                                </p>

                                {{-- Meta --}}
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $entry->display_name ?? 'Anonymous' }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $entry->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <span>{{ $entry->created_at->diffForHumans() }}</span>
                                    @if ($entry->user)
                                        <span class="px-2 py-0.5 bg-brew-cream text-brew-brown rounded text-xs">
                                            Registered User
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                            Guest
                                        </span>
                                    @endif
                                </div>

                                {{-- Sentiment (if available) --}}
                                @if ($entry->sentiment_score != 0)
                                    @php
                                        $sentiment = $entry->sentiment_score;
                                        $sentimentClass =
                                            $sentiment >= 0.3
                                                ? 'text-green-600'
                                                : ($sentiment <= -0.3
                                                    ? 'text-red-600'
                                                    : 'text-gray-600');
                                    @endphp
                                    <div class="mt-3 text-sm {{ $sentimentClass }}">
                                        Sentiment Score: {{ number_format($sentiment, 2) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-2 min-w-32">
                                <form action="{{ route('admin.vibewall.approve', $entry) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('admin.vibewall.featured', $entry) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-purple-500 text-white font-medium rounded-lg hover:bg-purple-600 transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Approve & Feature
                                    </button>
                                </form>

                                <form action="{{ route('admin.vibewall.reject', $entry) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus entry ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-red-100 text-red-600 font-medium rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $entries->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-16 text-center">
                <span class="text-6xl block mb-4">🎉</span>
                <h3 class="text-xl font-semibold text-brew-dark mb-2">Semua Entry Sudah Di-review!</h3>
                <p class="text-gray-500">Tidak ada entry yang menunggu moderasi saat ini.</p>
                <a href="{{ route('admin.vibewall.index') }}"
                    class="inline-block mt-6 px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                    Lihat Semua Entries
                </a>
            </div>
        @endif
    </div>
@endsection
