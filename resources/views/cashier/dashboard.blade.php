@extends('layouts.cashierLayout')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Dashboard Kasir')

@section('content')
    <div class="space-y-6">
        {{-- Active Flash Sale Banner --}}
        @if ($activeFlashSale)
            <div class="bg-gradient-to-r from-brew-gold to-yellow-500 rounded-xl p-4 text-brew-dark">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <p class="font-bold">Flash Sale Aktif!</p>
                            <p class="text-sm">{{ $activeFlashSale->promo_code }} - Diskon
                                {{ $activeFlashSale->discount_percentage }}%</p>
                        </div>
                    </div>
                    <span class="text-sm font-medium">
                        @if ($activeFlashSale->ends_at)
                            Berakhir {{ $activeFlashSale->ends_at->diffForHumans() }}
                        @endif
                    </span>
                </div>
            </div>
        @endif

        {{-- Today Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Total Orders Today --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Hari Ini</p>
                        <p class="text-xl font-bold text-brew-dark">{{ $todayStats['totalOrders'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Pending Payment --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Menunggu Bayar</p>
                        <p class="text-xl font-bold text-orange-600">{{ $todayStats['pendingPayment'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Selesai</p>
                        <p class="text-xl font-bold text-green-600">{{ $todayStats['completed'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-brew-cream rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-brew-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pendapatan</p>
                        <p class="text-xl font-bold text-brew-gold">
                            Rp {{ number_format($todayStats['totalRevenue'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Orders Section --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-semibold text-brew-dark">Pesanan Menunggu Pembayaran</h3>
                    @if ($pendingOrders->count() > 0)
                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded-full">
                            {{ $pendingOrders->count() }}
                        </span>
                    @endif
                </div>
                <button class="text-brew-brown hover:text-brew-dark font-medium text-sm flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>

            <div class="p-6">
                @if ($pendingOrders->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">Tidak ada pesanan yang menunggu pembayaran</p>
                        <p class="text-sm text-gray-400 mt-1">Pesanan baru akan muncul di sini</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($pendingOrders as $order)
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-brew-gold transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-brew-dark text-lg">{{ $order->order_number }}</span>
                                            <span
                                                class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                                Menunggu
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Meja {{ $order->table_number ?? '-' }} •
                                            {{ $order->created_at->format('H:i') }}
                                            <span class="text-gray-400">({{ $order->created_at->diffForHumans() }})</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-brew-dark">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Customer Mood (Empathy Radar) --}}
                                @if ($order->customer_mood_summary)
                                    <div class="mb-3 p-2 bg-purple-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-purple-600">💭</span>
                                            <span
                                                class="text-sm text-purple-700">{{ $order->customer_mood_summary }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Order Items --}}
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Items:</p>
                                    <ul class="space-y-1">
                                        @foreach ($order->orderItems as $item)
                                            <li class="flex justify-between text-sm">
                                                <span class="text-gray-600">
                                                    {{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}
                                                    @if ($item->note)
                                                        <span class="text-gray-400 text-xs">({{ $item->note }})</span>
                                                    @endif
                                                </span>
                                                <span class="text-gray-700">
                                                    Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex space-x-2">
                                    <button
                                        class="flex-1 py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                        💵 Bayar Cash
                                    </button>
                                    <button
                                        class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                                        📱 Bayar QRIS
                                    </button>
                                    <button
                                        class="py-2 px-4 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
                                        Debit
                                    </button>
                                    <button
                                        class="py-2 px-4 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
