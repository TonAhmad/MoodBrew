@extends('layouts.adminLayout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Today Orders --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pesanan Hari Ini</p>
                        <p class="text-2xl font-bold text-brew-dark mt-1">{{ $stats['todayOrders'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Orders --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Menunggu Pembayaran</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pendingOrders'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Today Revenue --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            Rp {{ number_format($stats['todayRevenue'] ?? 0, 0, ',', '.') }}
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

            {{-- Monthly Revenue --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-bold text-brew-gold mt-1">
                            Rp {{ number_format($stats['monthlyRevenue'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-brew-cream rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-brew-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Menu Items --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Menu Items</p>
                        <p class="text-lg font-semibold text-brew-dark">
                            {{ $stats['availableMenuItems'] ?? 0 }} / {{ $stats['totalMenuItems'] ?? 0 }}
                            <span class="text-sm font-normal text-gray-500">tersedia</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Total Customers --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Customers</p>
                        <p class="text-lg font-semibold text-brew-dark">{{ $stats['totalCustomers'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Orders --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Orders</p>
                        <p class="text-lg font-semibold text-brew-dark">{{ $stats['totalOrders'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Orders --}}
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-brew-dark">Pesanan Terbaru</h3>
                </div>
                <div class="p-6">
                    @if ($recentOrders->isEmpty())
                        <p class="text-gray-500 text-center py-8">Belum ada pesanan</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-brew-dark">{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">
                                            Meja {{ $order->table_number ?? '-' }} •
                                            {{ $order->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-brew-dark">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </p>
                                        <span
                                            class="inline-flex px-2 py-1 text-xs rounded-full
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $order->status === 'pending_payment' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $order->status === 'paid_preparing' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $order->status === 'served' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Menu Items --}}
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-brew-dark">Menu Terlaris</h3>
                </div>
                <div class="p-6">
                    @if ($topMenuItems->isEmpty())
                        <p class="text-gray-500 text-center py-8">Belum ada data menu</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($topMenuItems as $index => $item)
                                <div class="flex items-center space-x-4">
                                    <span
                                        class="w-8 h-8 bg-brew-cream rounded-full flex items-center justify-center text-brew-brown font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="font-medium text-brew-dark">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($item->category) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-brew-dark">{{ $item->order_items_count }} terjual</p>
                                        <p class="text-sm text-gray-500">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
