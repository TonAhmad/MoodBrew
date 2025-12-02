@extends('layouts.adminLayout')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Back & Month Selector --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center text-brew-brown hover:text-brew-dark transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Laporan
                </a>

                <form method="GET" action="{{ route('admin.reports.monthly') }}" class="flex items-center gap-3">
                    <input type="month" name="month" value="{{ $month }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                    <button type="submit"
                        class="px-4 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        {{-- Header --}}
        <div class="bg-gradient-to-r from-brew-gold to-yellow-500 rounded-xl shadow-sm p-6 text-brew-dark">
            <h2 class="text-2xl font-bold">{{ $report['formatted_period'] ?? $month }}</h2>
            <p class="text-brew-dark/70 mt-1">Ringkasan penjualan bulanan</p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-600 mt-1">
                    Rp {{ number_format($report['total_revenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $report['total_orders'] ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Rata-rata Harian</p>
                <p class="text-2xl font-bold text-brew-gold mt-1">
                    Rp {{ number_format($report['avg_daily_revenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Rata-rata per Order</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">
                    Rp {{ number_format($report['avg_order_value'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Weekly Comparison --}}
        @if (!empty($report['weekly_comparison']))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Perbandingan Mingguan</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach ($report['weekly_comparison'] as $week)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium text-brew-dark">{{ $week['week'] }}</p>
                            <p class="text-2xl font-bold text-green-600 mt-2">
                                Rp {{ number_format($week['revenue'], 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $week['orders'] }} pesanan</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Best Sellers --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">🏆 Top 10 Menu Bulan Ini</h3>
                @if (!empty($report['best_sellers']) && count($report['best_sellers']) > 0)
                    <div class="space-y-2">
                        @foreach ($report['best_sellers'] as $index => $item)
                            <div class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-yellow-50' : 'bg-gray-50' }} rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="w-7 h-7 {{ $index < 3 ? 'bg-yellow-400' : 'bg-gray-300' }} text-brew-dark rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-brew-dark text-sm">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->category }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-brew-dark text-sm">{{ $item->total_qty }}</p>
                                    <p class="text-xs text-green-600">
                                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada data</p>
                    </div>
                @endif
            </div>

            {{-- Revenue by Category --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">📊 Kontribusi Kategori</h3>
                @if (!empty($report['revenue_by_category']))
                    @php
                        $totalRevenue = array_sum($report['revenue_by_category']);
                        $categoryConfig = [
                            'Coffee' => ['color' => 'bg-amber-500', 'icon' => '☕'],
                            'Non-Coffee' => ['color' => 'bg-green-500', 'icon' => '🍵'],
                            'Food' => ['color' => 'bg-orange-500', 'icon' => '🍽️'],
                            'Snack' => ['color' => 'bg-purple-500', 'icon' => '🍪'],
                            'Dessert' => ['color' => 'bg-pink-500', 'icon' => '🍰'],
                        ];
                    @endphp

                    {{-- Pie Chart Visual --}}
                    <div class="flex items-center justify-center mb-6">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                @php
                                    $offset = 0;
                                    $colors = ['#F59E0B', '#10B981', '#F97316', '#8B5CF6', '#EC4899', '#6B7280'];
                                    $colorIndex = 0;
                                @endphp
                                @foreach ($report['revenue_by_category'] as $category => $revenue)
                                    @php
                                        $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
                                        $dasharray = $percentage . ' ' . (100 - $percentage);
                                        $color = $colors[$colorIndex % count($colors)];
                                        $colorIndex++;
                                    @endphp
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $color }}"
                                        stroke-width="20" stroke-dasharray="{{ $dasharray }}"
                                        stroke-dashoffset="{{ -$offset }}" />
                                    @php
                                        $offset += $percentage;
                                    @endphp
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Total</p>
                                    <p class="text-sm font-bold text-brew-dark">
                                        {{ number_format($totalRevenue / 1000000, 1) }}jt
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="space-y-3">
                        @foreach ($report['revenue_by_category'] as $category => $revenue)
                            @php
                                $config = $categoryConfig[$category] ?? ['color' => 'bg-gray-500', 'icon' => '📦'];
                                $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 {{ $config['color'] }} rounded-full mr-2"></span>
                                    <span class="text-sm">{{ $config['icon'] }} {{ $category }}</span>
                                </div>
                                <span class="text-sm font-medium">
                                    Rp {{ number_format($revenue, 0, ',', '.') }} ({{ round($percentage) }}%)
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada data</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Daily Data Table --}}
        @if (!empty($report['daily_data']))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-brew-dark">Detail Harian</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="text-left py-3 px-6 text-sm font-semibold text-gray-600">Tanggal</th>
                                <th class="text-center py-3 px-6 text-sm font-semibold text-gray-600">Pesanan</th>
                                <th class="text-right py-3 px-6 text-sm font-semibold text-gray-600">Pendapatan</th>
                                <th class="text-right py-3 px-6 text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($report['daily_data'] as $dayData)
                                <tr class="hover:bg-gray-50 {{ $dayData['revenue'] > 0 ? '' : 'opacity-50' }}">
                                    <td class="py-3 px-6">
                                        <span class="font-medium text-brew-dark">Tanggal {{ $dayData['day'] }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-center">{{ $dayData['orders'] }}</td>
                                    <td class="py-3 px-6 text-right font-medium {{ $dayData['revenue'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        Rp {{ number_format($dayData['revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        @if ($dayData['revenue'] > 0)
                                            <a href="{{ route('admin.reports.daily', ['date' => $dayData['date']]) }}"
                                                class="text-brew-gold hover:text-brew-brown text-sm">
                                                Detail →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
