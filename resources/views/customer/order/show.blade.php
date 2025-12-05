@extends('layouts.custLayout')

@section('title', 'Pesanan ' . $order->order_number . ' - MoodBrew')

@section('content')
    <div class="pb-20 lg:pb-8">
        {{-- Desktop Container --}}
        <div class="lg:max-w-5xl lg:mx-auto lg:px-8 lg:py-6">
            {{-- Header --}}
            <div class="bg-white p-4 border-b border-gray-100 flex items-center lg:border-0 lg:mb-4">
                <a href="{{ route('customer.orders.index') }}" class="mr-3 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg lg:text-2xl font-bold text-brew-dark">{{ $order->order_number }}</h1>
                    <p class="text-xs lg:text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'pending_payment' => 'from-yellow-400 to-orange-400',
                    'preparing' => 'from-blue-400 to-indigo-400',
                    'ready' => 'from-green-400 to-emerald-400',
                    'completed' => 'from-gray-400 to-gray-500',
                    'cancelled' => 'from-red-400 to-red-500',
                ];
            @endphp
            <div class="bg-gradient-to-r {{ $statusColors[$order->status] ?? 'from-gray-400 to-gray-500' }} p-6 lg:p-8 text-white text-center rounded-xl shadow-lg">
                <div class="text-5xl lg:text-6xl mb-3">{{ $statusInfo['icon'] }}</div>
                <h2 class="text-2xl lg:text-3xl font-bold">{{ $statusInfo['label'] }}</h2>
                <p class="text-white/90 mt-2 lg:text-lg">{{ $statusInfo['description'] }}</p>
            </div>

            {{-- Order Progress --}}
            <div class="p-4 lg:p-6 bg-white rounded-xl shadow-md mt-4">
                <div class="flex items-center justify-between">
                    @php
                        $steps = [
                            ['status' => 'pending_payment', 'label' => 'Bayar', 'icon' => '💳'],
                            ['status' => 'preparing', 'label' => 'Diproses', 'icon' => '👨‍🍳'],
                            ['status' => 'ready', 'label' => 'Siap', 'icon' => '✅'],
                        ];
                        $currentIndex = match($order->status) {
                            'pending_payment' => 0,
                            'preparing' => 1,
                            'ready', 'completed' => 2,
                            default => -1,
                        };
                    @endphp
                    @foreach($steps as $index => $step)
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-full flex items-center justify-center text-lg lg:text-2xl transition-all
                                {{ $index <= $currentIndex ? 'bg-brew-gold shadow-md' : 'bg-gray-200' }}">
                                {{ $step['icon'] }}
                            </div>
                            <span class="text-xs lg:text-sm mt-2 {{ $index <= $currentIndex ? 'text-brew-dark font-semibold' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if($index < count($steps) - 1)
                            <div class="flex-1 h-1 lg:h-2 mx-2 rounded-full transition-all {{ $index < $currentIndex ? 'bg-brew-gold' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Order Details --}}
            <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
                {{-- Customer Info --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
                    <h3 class="font-semibold text-brew-dark mb-4 text-lg lg:text-xl">👤 Informasi</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama</span>
                        <span class="text-brew-dark font-medium">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Meja</span>
                        <span class="text-brew-dark font-medium">{{ $order->table_number }}</span>
                    </div>
                    @if($order->notes)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Catatan</span>
                            <span class="text-brew-dark">{{ $order->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
                    <h3 class="font-semibold text-brew-dark mb-4 text-lg lg:text-xl">🛍️ Pesanan</h3>
                <div class="space-y-3">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center space-x-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                            <div class="w-12 h-12 bg-brew-cream rounded-lg flex items-center justify-center">
                                <span class="text-xl">{{ $item->menuItem->category === 'coffee' ? '☕' : '🥤' }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-brew-dark">{{ $item->menuItem->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }}x @ Rp {{ number_format($item->price_at_moment, 0, ',', '.') }}</p>
                                @if($item->note)
                                    <p class="text-xs text-gray-400">📝 {{ $item->note }}</p>
                                @endif
                            </div>
                            <p class="font-semibold text-brew-dark">
                                Rp {{ number_format($item->price_at_moment * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

                {{-- Payment Summary --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
                    <h3 class="font-semibold text-brew-dark mb-4 text-lg lg:text-xl">💳 Pembayaran</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-brew-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="font-semibold text-brew-dark">Total</span>
                        <span class="font-bold text-brew-gold text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->payment_method)
                        <div class="flex justify-between pt-2 text-green-600">
                            <span>✅ Dibayar via {{ ucfirst($order->payment_method) }}</span>
                            <span>{{ $order->paid_at?->format('H:i') }}</span>
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                            <p class="text-yellow-700 text-sm">
                                💡 Pembayaran dilakukan di kasir saat pesanan siap
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mood Summary --}}
            @if($order->customer_mood_summary)
                <div class="bg-purple-50 rounded-xl p-4">
                    <h3 class="font-semibold text-purple-800 mb-2">💭 Mood saat memesan</h3>
                    <p class="text-purple-700 text-sm">{{ $order->customer_mood_summary }}</p>
                </div>
            @endif
        </div>

                {{-- Action Buttons --}}
                @if($order->status === 'ready')
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 lg:p-6 text-center shadow-md">
                        <span class="text-4xl lg:text-5xl block mb-3">🎉</span>
                        <p class="font-semibold text-green-800 text-lg lg:text-xl">Pesananmu sudah siap!</p>
                        <p class="text-green-700 text-sm lg:text-base mt-2">Silakan ambil di counter dengan menyebutkan nomor pesanan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
