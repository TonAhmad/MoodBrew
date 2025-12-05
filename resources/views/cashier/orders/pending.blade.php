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
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brew-dark">{{ $stats['pendingPayment'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-12 h-12 bg-brew-gold/20 rounded-xl flex items-center justify-center">
                    <span class="text-brew-gold font-bold text-base">Rp</span>
                </div>
                <div>
                    <p class="text-lg font-bold text-brew-dark">{{ number_format($stats['totalRevenue'] ?? 0, 0, ',', '.') }}</p>
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
            <div class="p-5 hover:bg-gray-50 transition-colors border-l-4 border-transparent hover:border-brew-gold" 
                 x-data="{ showPayment: false, amountPaid: {{ $order->total_amount }}, changeAmount: 0 }">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-bold text-brew-dark">#{{ $order->order_number }}</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Menunggu Bayar</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $order->customer_name ?? 'Walk-in Customer' }}</span>
                            <span class="text-gray-400">•</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-3 space-y-1.5 bg-gray-50 rounded-lg p-3">
                            @foreach($order->items->take(3) as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">
                                    <span class="font-semibold text-brew-gold">{{ $item->quantity }}x</span> 
                                    {{ $item->menuItem->name ?? 'Item' }}
                                </span>
                                <span class="font-medium text-gray-900">Rp {{ number_format($item->price_at_moment * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            @if($order->items->count() > 3)
                            <p class="text-xs text-gray-500 italic mt-2">+{{ $order->items->count() - 3 }} item lainnya</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="lg:text-right space-y-3">
                        <div class="bg-brew-gold/10 rounded-xl p-4 border-2 border-brew-gold/30">
                            <p class="text-xs text-gray-600 mb-1">Total Pembayaran</p>
                            <p class="text-2xl font-bold text-brew-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex lg:justify-end gap-2">
                            <button @click="showPayment = !showPayment"
                                    class="flex-1 lg:flex-none px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Bayar
                            </button>
                            <form action="{{ route('cashier.orders.cancel', $order) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2.5 bg-red-100 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-200 transition-colors">
                                    ✕
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (Expandable) -->
                <div x-show="showPayment" 
                     x-collapse 
                     class="mt-5 p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200"
                     x-data="{
                        paymentMethod: 'cash',
                        calculateChange() {
                            this.changeAmount = this.amountPaid - {{ $order->total_amount }};
                        },
                        updatePaymentMethod(method) {
                            this.paymentMethod = method;
                            if (method !== 'cash') {
                                this.amountPaid = {{ $order->total_amount }};
                                this.changeAmount = 0;
                            }
                        }
                    }">
                    <form action="{{ route('cashier.orders.payment', $order) }}" method="POST">
                        @csrf
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Metode Pembayaran
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <button type="button" @click="updatePaymentMethod('cash')"
                                        :class="paymentMethod === 'cash' ? 'bg-green-600 text-white ring-2 ring-green-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                        class="p-4 rounded-xl border-2 border-gray-200 transition-all text-center">
                                    <div class="text-2xl mb-1">💵</div>
                                    <div class="text-sm font-semibold">Cash</div>
                                </button>
                                <button type="button" @click="updatePaymentMethod('qris')"
                                        :class="paymentMethod === 'qris' ? 'bg-blue-600 text-white ring-2 ring-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                        class="p-4 rounded-xl border-2 border-gray-200 transition-all text-center">
                                    <div class="text-2xl mb-1">📱</div>
                                    <div class="text-sm font-semibold">QRIS</div>
                                </button>
                                <button type="button" @click="updatePaymentMethod('debit')"
                                        :class="paymentMethod === 'debit' ? 'bg-purple-600 text-white ring-2 ring-purple-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                        class="p-4 rounded-xl border-2 border-gray-200 transition-all text-center">
                                    <div class="text-2xl mb-1">💳</div>
                                    <div class="text-sm font-semibold">Debit</div>
                                </button>
                                <button type="button" @click="updatePaymentMethod('credit')"
                                        :class="paymentMethod === 'credit' ? 'bg-orange-600 text-white ring-2 ring-orange-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                        class="p-4 rounded-xl border-2 border-gray-200 transition-all text-center">
                                    <div class="text-2xl mb-1">💳</div>
                                    <div class="text-sm font-semibold">Credit</div>
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                        </div>

                        <!-- Cash Payment Input -->
                        <div x-show="paymentMethod === 'cash'" class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Jumlah Bayar <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                                <input type="number" 
                                       name="amount_paid" 
                                       x-model="amountPaid"
                                       @input="calculateChange"
                                       :required="paymentMethod === 'cash'"
                                       min="0"
                                       value="{{ $order->total_amount }}"
                                       step="1000"
                                       placeholder="Masukkan jumlah uang"
                                       class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-brew-gold focus:border-brew-gold transition-all text-lg font-semibold">
                            </div>
                            
                            <!-- Quick Amount Buttons -->
                            <div class="grid grid-cols-4 gap-2 mt-3">
                                <button type="button" @click="amountPaid = {{ $order->total_amount }}; calculateChange()"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">Pas</button>
                                <button type="button" @click="amountPaid = 50000; calculateChange()"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">50K</button>
                                <button type="button" @click="amountPaid = 100000; calculateChange()"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">100K</button>
                                <button type="button" @click="amountPaid = 200000; calculateChange()"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">200K</button>
                            </div>
                            
                            <!-- Change Display -->
                            <div class="mt-4 p-4 rounded-xl" 
                                 :class="changeAmount >= 0 ? 'bg-green-50 border-2 border-green-200' : 'bg-red-50 border-2 border-red-200'">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold" :class="changeAmount >= 0 ? 'text-green-700' : 'text-red-700'">Kembalian:</span>
                                    <span class="text-xl font-bold" :class="changeAmount >= 0 ? 'text-green-900' : 'text-red-900'">
                                        Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.max(0, changeAmount))"></span>
                                    </span>
                                </div>
                                <p x-show="changeAmount < 0" class="text-xs text-red-600 mt-1">
                                    ⚠️ Uang kurang Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.abs(changeAmount))"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Non-Cash Payment Info -->
                        <div x-show="paymentMethod !== 'cash'" class="mb-5">
                            <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-xl">
                                <div class="flex items-center gap-2 text-blue-800 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">Pembayaran Non-Tunai</span>
                                </div>
                                <p class="text-sm text-blue-700">Jumlah yang harus dibayar:</p>
                                <p class="text-2xl font-bold text-blue-900 mt-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <input type="hidden" name="amount_paid" :value="{{ $order->total_amount }}">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showPayment = false"
                                    class="px-6 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="paymentMethod === 'cash' && changeAmount < 0"
                                    :class="(paymentMethod === 'cash' && changeAmount < 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                    class="px-6 py-2.5 text-white font-semibold rounded-xl transition-colors shadow-sm hover:shadow-md">
                                ✓ Proses Pembayaran
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
