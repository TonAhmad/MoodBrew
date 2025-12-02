@extends('layouts.adminLayout')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Back & Date Selector --}}
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

                <form method="GET" action="{{ route('admin.reports.daily') }}" class="flex items-center gap-3">
                    <input type="date" name="date" value="{{ $date }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                    <button type="submit"
                        class="px-4 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        {{-- Header --}}
        <div class="bg-gradient-to-r from-brew-dark to-brew-brown rounded-xl shadow-sm p-6 text-white">
            <h2 class="text-2xl font-bold">{{ $report['formatted_date'] ?? $date }}</h2>
            <p class="text-brew-cream/80 mt-1">Detail lengkap penjualan harian</p>
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
                <p class="text-sm text-gray-500">Rata-rata per Order</p>
                <p class="text-2xl font-bold text-brew-gold mt-1">
                    Rp {{ number_format($report['avg_order_value'] ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Item Terjual</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">
                    {{ collect($report['best_sellers'] ?? [])->sum('total_qty') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Hourly Chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Penjualan per Jam</h3>
                @if (!empty($report['hourly_breakdown']))
                    <div class="overflow-x-auto">
                        <div class="flex items-end space-x-1 min-w-max h-48">
                            @php
                                $maxRevenue = max(array_column($report['hourly_breakdown'], 'revenue')) ?: 1;
                            @endphp
                            @foreach ($report['hourly_breakdown'] as $hourData)
                                @php
                                    $height = ($hourData['revenue'] / $maxRevenue) * 100;
                                @endphp
                                <div class="flex flex-col items-center group">
                                    <div class="relative">
                                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-brew-dark text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            Rp {{ number_format($hourData['revenue'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="w-8 bg-brew-gold rounded-t transition-all duration-300 hover:bg-brew-brown cursor-pointer"
                                        style="height: {{ max($height, 2) }}%">
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1">{{ substr($hourData['hour'], 0, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>Belum ada data penjualan</p>
                    </div>
                @endif
            </div>

            {{-- Payment Methods --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">Metode Pembayaran</h3>
                @if (!empty($report['payment_methods']))
                    <div class="space-y-4">
                        @php
                            $paymentLabels = [
                                'cash' => ['label' => 'Tunai', 'color' => 'bg-green-500', 'icon' => '💵'],
                                'qris' => ['label' => 'QRIS', 'color' => 'bg-blue-500', 'icon' => '📱'],
                                'transfer' => ['label' => 'Transfer', 'color' => 'bg-purple-500', 'icon' => '🏦'],
                                'card' => ['label' => 'Kartu', 'color' => 'bg-orange-500', 'icon' => '💳'],
                            ];
                            $totalPayment = array_sum($report['payment_methods']);
                        @endphp
                        @foreach ($report['payment_methods'] as $method => $amount)
                            @php
                                $config = $paymentLabels[$method] ?? [
                                    'label' => ucfirst($method),
                                    'color' => 'bg-gray-500',
                                    'icon' => '💰',
                                ];
                                $percentage = $totalPayment > 0 ? ($amount / $totalPayment) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="flex items-center text-sm">
                                        <span class="mr-2">{{ $config['icon'] }}</span>
                                        {{ $config['label'] }}
                                    </span>
                                    <span class="text-sm font-medium">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $config['color'] }} h-2 rounded-full"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada transaksi</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Best Sellers --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">🏆 Menu Terlaris</h3>
                @if (!empty($report['best_sellers']) && count($report['best_sellers']) > 0)
                    <div class="space-y-3">
                        @foreach ($report['best_sellers'] as $index => $item)
                            <div class="flex items-center justify-between p-3 {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="w-8 h-8 {{ $index === 0 ? 'bg-yellow-400' : 'bg-brew-gold' }} text-brew-dark rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-brew-dark">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->category }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-brew-dark">{{ $item->total_qty }} pcs</p>
                                    <p class="text-sm text-green-600">
                                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada penjualan</p>
                    </div>
                @endif
            </div>

            {{-- Revenue by Category --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-brew-dark mb-4">📊 Pendapatan per Kategori</h3>
                @if (!empty($report['revenue_by_category']))
                    <div class="space-y-4">
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
                        @foreach ($report['revenue_by_category'] as $category => $revenue)
                            @php
                                $config = $categoryConfig[$category] ?? ['color' => 'bg-gray-500', 'icon' => '📦'];
                                $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="flex items-center font-medium text-brew-dark">
                                        <span class="mr-2">{{ $config['icon'] }}</span>
                                        {{ $category }}
                                    </span>
                                    <span class="text-sm text-gray-600">{{ round($percentage) }}%</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                                        <div class="{{ $config['color'] }} h-3 rounded-full transition-all duration-300"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-green-600 w-28 text-right">
                                        Rp {{ number_format($revenue, 0, ',', '.') }}
                                    </span>
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
        </div>

        {{-- Order List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-brew-dark">Daftar Pesanan</h3>
            </div>
            @if (!empty($report['orders']) && count($report['orders']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-6 text-sm font-semibold text-gray-600">No. Order</th>
                                <th class="text-left py-3 px-6 text-sm font-semibold text-gray-600">Customer</th>
                                <th class="text-left py-3 px-6 text-sm font-semibold text-gray-600">Items</th>
                                <th class="text-left py-3 px-6 text-sm font-semibold text-gray-600">Waktu</th>
                                <th class="text-right py-3 px-6 text-sm font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($report['orders'] as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-6">
                                        <span class="font-mono font-medium text-brew-dark">{{ $order->order_number }}</span>
                                    </td>
                                    <td class="py-3 px-6">
                                        {{ $order->customer?->name ?? 'Walk-in' }}
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-sm text-gray-600">
                                            {{ $order->orderItems->count() }} item(s)
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-sm text-gray-500">
                                        {{ $order->created_at->format('H:i') }}
                                    </td>
                                    <td class="py-3 px-6 text-right font-medium text-green-600">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-lg">Tidak ada pesanan pada tanggal ini</p>
                </div>
            @endif
        </div>
    </div>
@endsection
