@extends('layouts.adminLayout')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Period Selector --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <select name="period" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                    Tampilkan
                </button>

                <a href="{{ route('admin.reports.daily', ['date' => $date]) }}"
                    class="px-6 py-2 bg-brew-brown text-white font-semibold rounded-lg hover:bg-brew-dark transition-colors">
                    Detail Harian
                </a>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            Rp {{ number_format($report['total_revenue'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pesanan</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $report['total_orders'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Rata-rata per Order</p>
                        <p class="text-2xl font-bold text-brew-gold mt-1">
                            Rp {{ number_format($report['avg_order_value'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-brew-cream rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-brew-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Periode</p>
                        <p class="text-lg font-bold text-brew-dark mt-1">
                            {{ $report['formatted_date'] ?? $report['formatted_period'] ?? '-' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Best Sellers --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Menu Terlaris</h3>
                @if (!empty($report['best_sellers']) && count($report['best_sellers']) > 0)
                    <div class="space-y-3">
                        @foreach ($report['best_sellers'] as $index => $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="w-8 h-8 bg-brew-gold text-brew-dark rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-brew-dark">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->category }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-brew-dark">{{ $item->total_qty }} pcs</p>
                                    <p class="text-sm text-gray-500">
                                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p>Belum ada data penjualan</p>
                    </div>
                @endif
            </div>

            {{-- Revenue by Category --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Pendapatan per Kategori</h3>
                @if (!empty($report['revenue_by_category']) && count($report['revenue_by_category']) > 0)
                    <div class="space-y-3">
                        @php
                            $totalRevenue = array_sum($report['revenue_by_category']);
                            $colors = [
                                'Coffee' => 'bg-amber-500',
                                'Non-Coffee' => 'bg-green-500',
                                'Food' => 'bg-orange-500',
                                'Snack' => 'bg-purple-500',
                                'Dessert' => 'bg-pink-500',
                            ];
                        @endphp
                        @foreach ($report['revenue_by_category'] as $category => $revenue)
                            @php
                                $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
                                $color = $colors[$category] ?? 'bg-gray-500';
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-brew-dark">{{ $category }}</span>
                                    <span class="text-gray-600">Rp {{ number_format($revenue, 0, ',', '.') }}
                                        ({{ round($percentage) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="{{ $color }} h-3 rounded-full transition-all duration-300"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada data kategori</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Hourly Breakdown (only for daily report) --}}
        @if ($period === 'daily' && !empty($report['hourly_breakdown']))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Penjualan per Jam</h3>
                <div class="overflow-x-auto">
                    <div class="flex items-end space-x-2 min-w-max h-48">
                        @php
                            $maxRevenue = max(array_column($report['hourly_breakdown'], 'revenue')) ?: 1;
                        @endphp
                        @foreach ($report['hourly_breakdown'] as $hourData)
                            @php
                                $height = ($hourData['revenue'] / $maxRevenue) * 100;
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="w-10 bg-brew-gold rounded-t transition-all duration-300 hover:bg-brew-brown"
                                    style="height: {{ max($height, 2) }}%" title="Rp {{ number_format($hourData['revenue'], 0, ',', '.') }}">
                                </div>
                                <span class="text-xs text-gray-500 mt-1">{{ substr($hourData['hour'], 0, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Weekly/Monthly Data Chart --}}
        @if ($period !== 'daily' && !empty($report['daily_data']))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">
                    {{ $period === 'weekly' ? 'Penjualan Harian (Minggu Ini)' : 'Penjualan Harian (Bulan Ini)' }}
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Tanggal</th>
                                <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Pesanan</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['daily_data'] as $dayData)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <span class="font-medium text-brew-dark">
                                            {{ $dayData['day_name'] ?? $dayData['day'] ?? $dayData['date'] }}
                                        </span>
                                        @if (isset($dayData['date']))
                                            <span class="text-sm text-gray-500 ml-2">{{ $dayData['date'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">{{ $dayData['orders'] }}</td>
                                    <td class="py-3 px-4 text-right font-medium text-green-600">
                                        Rp {{ number_format($dayData['revenue'], 0, ',', '.') }}
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
