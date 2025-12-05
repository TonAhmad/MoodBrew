@extends('layouts.cashierLayout')

@section('title', 'Trigger Flash Sale')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Dead-hour Flash Sales</h1>
            <p class="text-gray-600">Boost penjualan saat jam sepi dengan AI copywriting</p>
        </div>
        @if(!$activeFlashSale)
        <a href="{{ route('cashier.flashsale.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Trigger Flash Sale
        </a>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['totalToday'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Flash Sale Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['activeNow'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Aktif Sekarang</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['totalThisMonth'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Bulan Ini</p>
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

    <!-- Active Flash Sale -->
    @if($activeFlashSale)
    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
                    <span class="font-semibold uppercase tracking-wide text-sm">Flash Sale Aktif</span>
                </div>
                <h3 class="text-2xl font-bold mb-1">{{ $activeFlashSale->name }}</h3>
                <p class="text-white/80">
                    Diskon {{ $activeFlashSale->discount_percentage }}% • 
                    Kode: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded">{{ $activeFlashSale->promo_code }}</span>
                </p>
                <p class="text-sm text-white/70 mt-2">
                    Berakhir: {{ $activeFlashSale->ends_at ? $activeFlashSale->ends_at->format('d M Y, H:i') : 'Tidak terbatas' }}
                </p>
            </div>
            <form action="{{ route('cashier.flashsale.end', $activeFlashSale) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" onclick="return confirm('Yakin ingin mengakhiri flash sale ini?')"
                        class="px-6 py-3 bg-white text-orange-600 font-bold rounded-lg hover:bg-gray-100 transition-colors">
                    Akhiri Promo
                </button>
            </form>
        </div>

        @if($activeFlashSale->ai_generated_copy)
        <div class="mt-4 p-4 bg-white/10 rounded-lg border border-white/20">
            <p class="text-sm font-medium mb-3 flex items-center">
                <span class="text-lg mr-2">🤖</span>
                AI Generated Copy:
            </p>
            @php
                $aiCopy = is_string($activeFlashSale->ai_generated_copy) 
                    ? json_decode($activeFlashSale->ai_generated_copy, true) 
                    : $activeFlashSale->ai_generated_copy;
            @endphp
            
            @if($aiCopy && is_array($aiCopy))
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wide mb-1">Headline:</p>
                    <p class="text-white font-bold text-lg">{{ $aiCopy['headline'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wide mb-1">Description:</p>
                    <p class="text-white/90">{{ $aiCopy['description'] ?? '-' }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wide mb-1">CTA:</p>
                        <span class="inline-block px-3 py-1 bg-white text-orange-600 font-bold rounded text-sm">
                            {{ $aiCopy['cta'] ?? 'ORDER NOW' }}
                        </span>
                    </div>
                    @if(isset($aiCopy['hashtags']) && is_array($aiCopy['hashtags']))
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wide mb-1">Hashtags:</p>
                        <p class="text-white/90 text-sm">{{ implode(' ', $aiCopy['hashtags']) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <p class="text-white/90 italic">{{ $activeFlashSale->ai_generated_copy }}</p>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- Suggestions (when AI unavailable, show static) -->
    @if(count($suggestions) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-semibold text-brew-dark">💡 Saran Flash Sale</h2>
            <p class="text-sm text-gray-500">Berdasarkan kondisi saat ini</p>
        </div>
        <div class="p-4 space-y-3">
            @foreach($suggestions as $suggestion)
            <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <p class="font-medium text-blue-800">{{ $suggestion['message'] }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    Saran: Diskon {{ $suggestion['suggested_discount'] }}% selama {{ $suggestion['suggested_duration'] }} jam
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Flash Sales History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-semibold text-brew-dark">Riwayat Flash Sale</h2>
        </div>

        @if($flashSales->isEmpty())
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p class="text-gray-500">Belum ada flash sale yang dibuat</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($flashSales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-brew-dark">{{ $sale->name }}</p>
                            <p class="text-xs text-gray-500">by {{ $sale->triggeredBy->name ?? 'System' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <code class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $sale->promo_code }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-brew-gold">{{ $sale->discount_percentage }}%</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->starts_at->format('d/m H:i') }} - {{ $sale->ends_at ? $sale->ends_at->format('d/m H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($sale->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Aktif</span>
                            @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($flashSales->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $flashSales->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
