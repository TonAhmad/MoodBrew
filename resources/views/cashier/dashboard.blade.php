@extends('layouts.cashierLayout')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Dashboard Kasir')

@section('content')
    <div class="space-y-6" x-data="cashierDashboard()">
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
                                <div class="flex gap-2">
                                    <button @click="openPaymentModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }}, 'cash')"
                                        class="flex-1 py-2.5 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Cash
                                    </button>
                                    <button @click="processPayment({{ $order->id }}, 'qris', {{ $order->total_amount }})"
                                        class="flex-1 py-2.5 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        QRIS
                                    </button>
                                    <button @click="processPayment({{ $order->id }}, 'debit', {{ $order->total_amount }})"
                                        class="py-2.5 px-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        Debit
                                    </button>
                                    <button @click="cancelOrder({{ $order->id }}, '{{ $order->order_number }}')"
                                        class="py-2.5 px-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="showPaymentModal" 
             x-cloak
             @keydown.escape.window="showPaymentModal = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div x-show="showPaymentModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showPaymentModal = false"
                     class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal panel --}}
                <div x-show="showPaymentModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form @submit.prevent="submitPayment" class="p-6">
                        {{-- Header --}}
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-brew-dark flex items-center">
                                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Proses Pembayaran Cash
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">Order: <span x-text="selectedOrder.number" class="font-semibold"></span></p>
                        </div>

                        {{-- Total Amount --}}
                        <div class="mb-6 p-4 bg-blue-50 rounded-xl">
                            <p class="text-sm text-blue-700 mb-1">Total Pembayaran</p>
                            <p class="text-3xl font-bold text-blue-900">
                                Rp <span x-text="formatNumber(selectedOrder.total)"></span>
                            </p>
                        </div>

                        {{-- Amount Paid Input --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Uang Diterima <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   x-model="amountPaid"
                                   @input="calculateChange"
                                   min="0"
                                   step="1000"
                                   required
                                   class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="0">
                            
                            {{-- Quick amount buttons --}}
                            <div class="grid grid-cols-4 gap-2 mt-3">
                                <button type="button" @click="setAmount(selectedOrder.total)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">Pas</button>
                                <button type="button" @click="setAmount(50000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">50K</button>
                                <button type="button" @click="setAmount(100000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">100K</button>
                                <button type="button" @click="setAmount(200000)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">200K</button>
                            </div>
                        </div>

                        {{-- Change Amount --}}
                        <div class="mb-6 p-4 rounded-xl" :class="changeAmount >= 0 ? 'bg-green-50' : 'bg-red-50'">
                            <p class="text-sm mb-1" :class="changeAmount >= 0 ? 'text-green-700' : 'text-red-700'">Kembalian</p>
                            <p class="text-2xl font-bold" :class="changeAmount >= 0 ? 'text-green-900' : 'text-red-900'">
                                Rp <span x-text="formatNumber(Math.max(0, changeAmount))"></span>
                            </p>
                            <p x-show="changeAmount < 0" class="text-xs text-red-600 mt-1">
                                ⚠️ Uang kurang Rp <span x-text="formatNumber(Math.abs(changeAmount))"></span>
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-3">
                            <button type="button" 
                                    @click="showPaymentModal = false"
                                    class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="changeAmount < 0 || !amountPaid"
                                    :class="changeAmount >= 0 && amountPaid ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                                    class="flex-1 px-4 py-3 text-white font-semibold rounded-xl transition-colors">
                                ✓ Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function cashierDashboard() {
    return {
        showPaymentModal: false,
        selectedOrder: {
            id: null,
            number: '',
            total: 0,
            method: 'cash'
        },
        amountPaid: 0,
        changeAmount: 0,

        openPaymentModal(orderId, orderNumber, total, method) {
            this.selectedOrder = {
                id: orderId,
                number: orderNumber,
                total: total,
                method: method
            };
            this.amountPaid = 0;
            this.changeAmount = -total;
            this.showPaymentModal = true;
        },

        setAmount(amount) {
            this.amountPaid = amount;
            this.calculateChange();
        },

        calculateChange() {
            this.changeAmount = this.amountPaid - this.selectedOrder.total;
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        },

        async processPayment(orderId, method, total) {
            if (method === 'cash') {
                this.openPaymentModal(orderId, `#${orderId}`, total, method);
                return;
            }

            // For QRIS and Debit, process directly
            if (!confirm(`Konfirmasi pembayaran dengan ${method.toUpperCase()}?`)) return;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('payment_method', method);
            formData.append('amount_paid', total);

            try {
                const response = await fetch(`/cashier/orders/${orderId}/payment`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal memproses pembayaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async submitPayment() {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('payment_method', this.selectedOrder.method);
            formData.append('amount_paid', this.amountPaid);

            try {
                const response = await fetch(`/cashier/orders/${this.selectedOrder.id}/payment`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal memproses pembayaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async cancelOrder(orderId, orderNumber) {
            if (!confirm(`Yakin ingin membatalkan pesanan ${orderNumber}?`)) return;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            try {
                const response = await fetch(`/cashier/orders/${orderId}`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal membatalkan pesanan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    }
}
</script>
@endpush
