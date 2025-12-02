@extends('layouts.cashierLayout')

@section('title', 'Pesanan Selesai')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Pesanan Selesai</h1>
            <p class="text-gray-600">Pesanan yang siap diambil & sudah selesai</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cashier.orders.pending') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending ({{ $stats['pending'] ?? 0 }})
            </a>
            <a href="{{ route('cashier.orders.preparing') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Diproses ({{ $stats['preparing'] ?? 0 }})
            </a>
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

    <!-- Ready Orders Section -->
    @php
        $readyOrders = $orders->where('status', 'ready');
        $completedOrders = $orders->where('status', 'completed');
    @endphp

    @if($readyOrders->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
        <div class="bg-green-50 px-4 py-3 border-b border-green-200">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                <h2 class="font-semibold text-green-800">Siap Diambil ({{ $readyOrders->count() }})</h2>
            </div>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($readyOrders as $order)
            <div class="p-4 bg-green-50/30 hover:bg-green-50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl font-bold text-green-600">#{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">SIAP</span>
                        </div>
                        <p class="text-lg font-medium text-gray-800">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                        <p class="text-sm text-gray-500">Dibayar: {{ $order->paid_at ? $order->paid_at->format('H:i') : '-' }}</p>
                        
                        <!-- Order Items -->
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($order->items as $item)
                            <span class="px-2 py-1 bg-white border border-gray-200 rounded text-sm">
                                {{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <form action="{{ route('cashier.orders.status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-colors">
                                ✓ Selesai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Completed Orders Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Riwayat Selesai Hari Ini ({{ $completedOrders->count() }})</h2>
        </div>

        @if($completedOrders->isEmpty() && $readyOrders->isEmpty())
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-gray-500">Belum ada pesanan yang selesai hari ini</p>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($completedOrders as $order)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-gray-700">#{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">Selesai</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                        <p class="text-xs text-gray-400">
                            Selesai: {{ $order->completed_at ? $order->completed_at->format('H:i') : $order->updated_at->format('H:i') }}
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-bold text-brew-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($order->payment_method ?? 'cash') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
