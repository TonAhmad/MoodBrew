@extends('layouts.custLayout')

@section('title', 'Pesanan Saya - MoodBrew')

@section('content')
    <div class="pb-20">
        {{-- Header --}}
        <div class="bg-white p-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-brew-dark">📋 Pesanan Saya</h1>
        </div>

        {{-- Active Orders --}}
        @if($activeOrders->isNotEmpty())
            <div class="p-4">
                <h2 class="font-semibold text-brew-dark mb-3">🔄 Pesanan Aktif</h2>
                <div class="space-y-3">
                    @foreach($activeOrders as $order)
                        <a href="{{ route('customer.orders.show', $order->order_number) }}" 
                           class="block bg-white rounded-xl shadow-sm p-4 border-l-4 
                                  {{ $order->status === 'pending_payment' ? 'border-yellow-500' : 'border-blue-500' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-brew-dark">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $order->status === 'pending_payment' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $order->status === 'pending_payment' ? '💳 Bayar' : '👨‍🍳 Diproses' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $order->orderItems->count() }} item</span>
                                <span class="font-bold text-brew-gold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Order History --}}
        <div class="p-4">
            <h2 class="font-semibold text-brew-dark mb-3">📜 Riwayat Pesanan</h2>
            
            @if($orders->isEmpty())
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">📋</span>
                    </div>
                    <p class="text-gray-500">Belum ada riwayat pesanan</p>
                    <a href="{{ route('customer.menu.index') }}" class="text-brew-gold text-sm mt-2 inline-block">
                        Pesan sekarang →
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($orders as $order)
                        <a href="{{ route('customer.orders.show', $order->order_number) }}" 
                           class="block bg-white rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-brew-dark">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @php
                                    $statusConfig = match($order->status) {
                                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => '✅ Selesai'],
                                        'ready' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => '✅ Siap'],
                                        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => '❌ Dibatalkan'],
                                        'preparing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => '👨‍🍳 Diproses'],
                                        'pending_payment' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => '💳 Bayar'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => '📋 Status'],
                                    };
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $order->orderItems->count() }} item • Meja {{ $order->table_number }}</span>
                                <span class="font-bold text-brew-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
