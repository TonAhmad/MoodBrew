@extends('layouts.custLayout')

@section('title', 'Keranjang - MoodBrew')

@section('content')
    <div class="pb-32 lg:pb-8">
        {{-- Desktop Container --}}
        <div class="lg:max-w-7xl lg:mx-auto lg:px-8 lg:py-6">
            {{-- Header --}}
            <div class="bg-white p-4 border-b border-gray-100 lg:border-0 lg:mb-6">
                <h1 class="text-xl lg:text-3xl font-bold text-brew-dark">🛒 Keranjang Belanja</h1>
                <p class="hidden lg:block text-gray-600 mt-1">Review pesanan sebelum checkout</p>
            </div>

            @if($cartItems->isEmpty())
                {{-- Empty Cart --}}
                <div class="flex flex-col items-center justify-center py-20 px-4">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-5xl">🛒</span>
                    </div>
                    <h2 class="text-xl font-bold text-brew-dark mb-2">Keranjang Kosong</h2>
                    <p class="text-gray-500 text-center mb-6">Yuk, pilih menu favoritmu dulu!</p>
                    <a href="{{ route('customer.menu.index') }}" 
                       class="px-6 py-3 bg-brew-gold text-brew-dark font-semibold rounded-xl hover:bg-yellow-400 transition-colors">
                        Lihat Menu
                    </a>
                </div>
            @else
                {{-- Desktop Grid Layout --}}
                <div class="lg:grid lg:grid-cols-3 lg:gap-6" x-data="cartManager()">
                    {{-- Cart Items (Mobile: full width, Desktop: 2 columns) --}}
                    <div class="lg:col-span-2">
                        <div class="p-4 lg:p-0 space-y-3">
                            @foreach($cartItems as $item)
                                <div class="bg-white rounded-xl shadow-sm p-4 lg:p-5 hover:shadow-md transition-shadow" x-data="{ qty: {{ $item['quantity'] }} }">
                                    <div class="flex space-x-3 lg:space-x-4">
                                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-brew-cream rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-3xl lg:text-4xl">{{ $item['menu_item']->category === 'coffee' ? '☕' : '🥤' }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-semibold text-brew-dark lg:text-lg">{{ $item['menu_item']->name }}</h3>
                                                    @if($item['note'])
                                                        <p class="text-xs lg:text-sm text-gray-500 mt-1">📝 {{ $item['note'] }}</p>
                                                    @endif
                                                    <p class="text-brew-gold font-bold mt-1 lg:text-lg">Rp {{ number_format($item['price_at_moment'], 0, ',', '.') }}</p>
                                                </div>
                                                <button @click="removeItem({{ $item['menu_item_id'] }})" 
                                                    class="text-red-500 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex justify-between items-center mt-3 lg:mt-4">
                                                <div class="flex items-center space-x-3">
                                                    <button @click="qty = Math.max(1, qty - 1); updateQuantity({{ $item['menu_item_id'] }}, qty)"
                                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors">
                                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </button>
                                                    <span class="w-8 text-center font-semibold lg:text-lg" x-text="qty"></span>
                                                    <button @click="qty = Math.min(10, qty + 1); updateQuantity({{ $item['menu_item_id'] }}, qty)"
                                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors">
                                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs lg:text-sm text-gray-500">Subtotal</p>
                                                    <p class="font-bold text-brew-dark lg:text-lg">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Clear Cart --}}
                            <div class="text-center pt-2">
                                <button @click="clearCart()" class="text-red-500 text-sm hover:text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg transition-colors">
                                    🗑️ Kosongkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Desktop Sidebar Summary --}}
                    <div class="hidden lg:block lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                            <h3 class="font-bold text-brew-dark text-xl mb-4">Ringkasan Pesanan</h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal ({{ $cartCount }} item)</span>
                                    <span class="font-semibold">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Biaya Admin</span>
                                    <span class="font-semibold">Rp 0</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-brew-dark">Total</span>
                                        <span class="text-2xl font-bold text-brew-dark">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('customer.orders.checkout') }}" 
                               class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors flex items-center justify-center space-x-2 shadow-md hover:shadow-lg">
                                <span>Lanjut ke Checkout</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="font-semibold text-brew-dark mb-3 text-sm">💡 Info</h4>
                                <ul class="text-xs text-gray-600 space-y-2">
                                    <li>✓ Gratis biaya admin</li>
                                    <li>✓ Pesanan diproses maksimal 15 menit</li>
                                    <li>✓ Bisa ambil di tempat atau delivery</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Cart Summary (Fixed Bottom) --}}
                <div class="lg:hidden fixed bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
                    <div class="max-w-lg mx-auto">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600">Total ({{ $cartCount }} item)</span>
                            <span class="text-xl font-bold text-brew-dark">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('customer.orders.checkout') }}" 
                           class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors flex items-center justify-center space-x-2">
                            <span>Checkout</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function cartManager() {
    return {
        async updateQuantity(menuItemId, quantity) {
            try {
                const response = await fetch('{{ route("customer.cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menu_item_id: menuItemId, quantity: quantity })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Refresh page to update totals
                    location.reload();
                }
            } catch (error) {
                console.error('Error updating cart:', error);
            }
        },
        
        async removeItem(menuItemId) {
            if (!confirm('Hapus item ini dari keranjang?')) return;
            
            try {
                const response = await fetch('{{ route("customer.cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menu_item_id: menuItemId })
                });
                
                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error removing item:', error);
            }
        },
        
        async clearCart() {
            if (!confirm('Kosongkan semua item dari keranjang?')) return;
            
            window.location.href = '{{ route("customer.cart.clear") }}';
        }
    }
}
</script>
@endpush
