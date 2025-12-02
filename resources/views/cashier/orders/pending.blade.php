@extends('layouts.cashierLayout')

@section('title', 'Pesanan Menunggu Pembayaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Menunggu Pembayaran</h1>
            <p class="text-gray-600">Pesanan yang belum dibayar</p>
        </div>
        <a href="{{ route('cashier.orders.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Pesanan Baru
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['pending'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['preparing'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Diproses</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['completed'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Selesai</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brew-gold/20 rounded-lg flex items-center justify-center">
                    <span class="text-brew-gold font-bold text-sm">Rp</span>
                </div>
                <div>
                    <p class="text-lg font-bold text-brew-dark">{{ number_format($stats['todayRevenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Revenue Hari Ini</p>
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

    <!-- Orders List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-semibold text-brew-dark">Daftar Pesanan Pending</h2>
        </div>

        @if($orders->isEmpty())
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-500">Tidak ada pesanan yang menunggu pembayaran</p>
            <a href="{{ route('cashier.orders.create') }}" class="inline-flex items-center mt-4 text-brew-gold hover:text-yellow-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pesanan Baru
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($orders as $order)
            <div class="p-4 hover:bg-gray-50 transition-colors" x-data="{ showPayment: false }">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-brew-dark">#{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Menunggu Bayar</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-2 space-y-1">
                            @foreach($order->items->take(3) as $item)
                            <p class="text-sm text-gray-600">
                                {{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}
                            </p>
                            @endforeach
                            @if($order->items->count() > 3)
                            <p class="text-xs text-gray-400">+{{ $order->items->count() - 3 }} item lainnya</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-lg font-bold text-brew-gold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <div class="flex gap-2 mt-2">
                            <button @click="showPayment = !showPayment"
                                    class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                Bayar
                            </button>
                            <form action="{{ route('cashier.orders.cancel', $order) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-600 text-sm rounded-lg hover:bg-red-200 transition-colors">
                                    Batal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (Expandable) -->
                <div x-show="showPayment" x-collapse class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <form action="{{ route('cashier.orders.payment', $order) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                <select name="payment_method" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit Card</option>
                                    <option value="credit">Credit Card</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                                <input type="number" name="amount_paid" required min="{{ $order->total_amount }}"
                                       value="{{ $order->total_amount }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" @click="showPayment = false"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
