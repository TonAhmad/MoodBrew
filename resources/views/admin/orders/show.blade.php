@extends('layouts.adminLayout')

@section('title', 'Order Detail')
@section('page-title', 'Detail Pesanan')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Back Link --}}
        <div>
            <a href="{{ route('admin.orders.index') }}"
                class="flex items-center text-brew-brown hover:text-brew-dark transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Orders
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Order Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Header --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-brew-dark">{{ $order->order_number }}</h2>
                            <p class="text-gray-500">{{ $order->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        @php
                            $statusConfig = [
                                'pending_payment' => [
                                    'class' => 'bg-orange-100 text-orange-700',
                                    'label' => 'Pending Payment',
                                ],
                                'paid' => ['class' => 'bg-blue-100 text-blue-700', 'label' => 'Paid'],
                                'preparing' => ['class' => 'bg-yellow-100 text-yellow-700', 'label' => 'Preparing'],
                                'ready' => ['class' => 'bg-purple-100 text-purple-700', 'label' => 'Ready'],
                                'completed' => ['class' => 'bg-green-100 text-green-700', 'label' => 'Completed'],
                                'cancelled' => ['class' => 'bg-red-100 text-red-700', 'label' => 'Cancelled'],
                            ];
                            $config = $statusConfig[$order->status] ?? [
                                'class' => 'bg-gray-100 text-gray-700',
                                'label' => ucfirst($order->status),
                            ];
                        @endphp
                        <span class="px-4 py-2 {{ $config['class'] }} rounded-lg font-medium">
                            {{ $config['label'] }}
                        </span>
                    </div>

                    {{-- Update Status --}}
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST"
                        class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm text-gray-600">Update Status:</label>
                        <select name="status"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                            <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>
                                Pending Payment</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing
                            </option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                        <button type="submit"
                            class="px-4 py-2 bg-brew-gold text-brew-dark font-medium rounded-lg hover:bg-yellow-500 transition-colors">
                            Update
                        </button>
                    </form>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-brew-dark">Item Pesanan</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($order->orderItems as $item)
                            <div class="p-6 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    @if ($item->menuItem?->image)
                                        <img src="{{ asset('storage/' . $item->menuItem->image) }}"
                                            alt="{{ $item->menuItem->name }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-brew-cream rounded-lg flex items-center justify-center">
                                            <span class="text-2xl">☕</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-brew-dark">{{ $item->menuItem?->name ?? 'Deleted Item' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Rp {{ number_format($item->price_at_moment, 0, ',', '.') }} × {{ $item->quantity }}
                                        </p>
                                        @if ($item->notes)
                                            <p class="text-sm text-gray-400 mt-1">{{ $item->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="font-semibold text-brew-dark">
                                    Rp {{ number_format($item->price_at_moment * $item->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="p-6 bg-gray-50 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold text-brew-dark pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount - ($order->discount_amount ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="space-y-6">
                {{-- Customer Info --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-brew-dark mb-4">Customer</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium text-brew-dark">{{ $order->customer?->name ?? 'Walk-in Customer' }}</p>
                        </div>
                        @if ($order->customer?->email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-brew-dark">{{ $order->customer->email }}</p>
                            </div>
                        @endif
                        @if ($order->customer_mood)
                            <div>
                                <p class="text-sm text-gray-500">Mood saat Order</p>
                                @php
                                    $moodEmoji = [
                                        'happy' => '😊',
                                        'relaxed' => '😌',
                                        'energetic' => '⚡',
                                        'tired' => '😴',
                                        'stressed' => '😰',
                                    ];
                                @endphp
                                <p class="font-medium text-brew-dark">
                                    {{ $moodEmoji[$order->customer_mood] ?? '🙂' }} {{ ucfirst($order->customer_mood) }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-brew-dark mb-4">Pembayaran</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Metode</p>
                            @php
                                $paymentLabels = [
                                    'cash' => '💵 Tunai',
                                    'qris' => '📱 QRIS',
                                    'transfer' => '🏦 Transfer',
                                    'card' => '💳 Kartu',
                                ];
                            @endphp
                            <p class="font-medium text-brew-dark">
                                {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method ?? 'Belum Bayar') }}
                            </p>
                        </div>
                        @if ($order->paid_at)
                            <div>
                                <p class="text-sm text-gray-500">Waktu Bayar</p>
                                <p class="font-medium text-brew-dark">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cashier Info --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-brew-dark mb-4">Kasir</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Diproses oleh</p>
                            <p class="font-medium text-brew-dark">{{ $order->cashier?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if ($order->notes)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-brew-dark mb-4">Catatan</h3>
                        <p class="text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif

                {{-- Timeline --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-brew-dark mb-4">Timeline</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 text-sm">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-gray-600">Order dibuat</span>
                            <span class="text-gray-400">{{ $order->created_at->format('H:i') }}</span>
                        </div>
                        @if ($order->paid_at)
                            <div class="flex items-center space-x-3 text-sm">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <span class="text-gray-600">Pembayaran diterima</span>
                                <span class="text-gray-400">{{ $order->paid_at->format('H:i') }}</span>
                            </div>
                        @endif
                        @if ($order->completed_at)
                            <div class="flex items-center space-x-3 text-sm">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-gray-600">Order selesai</span>
                                <span class="text-gray-400">{{ $order->completed_at->format('H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
