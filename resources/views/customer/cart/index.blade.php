@extends('layouts.custLayout')

@section('title', 'Keranjang - MoodBrew')

@section('content')
    <div class="pb-32">
        {{-- Header --}}
        <div class="bg-white p-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-brew-dark">🛒 Keranjang</h1>
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
            {{-- Cart Items --}}
            <div class="p-4 space-y-3" x-data="cartManager()">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4" x-data="{ qty: {{ $item['quantity'] }} }">
                        <div class="flex space-x-3">
                            <div class="w-20 h-20 bg-brew-cream rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-3xl">{{ $item['menu_item']->category === 'coffee' ? '☕' : '🥤' }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-brew-dark">{{ $item['menu_item']->name }}</h3>
                                        @if($item['note'])
                                            <p class="text-xs text-gray-500">📝 {{ $item['note'] }}</p>
                                        @endif
                                    </div>
                                    <button @click="removeItem({{ $item['menu_item_id'] }})" 
                                        class="text-red-500 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-brew-gold font-bold">Rp {{ number_format($item['price_at_moment'], 0, ',', '.') }}</p>
                                    <div class="flex items-center space-x-2">
                                        <button @click="qty = Math.max(1, qty - 1); updateQuantity({{ $item['menu_item_id'] }}, qty)"
                                            class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-6 text-center font-semibold" x-text="qty"></span>
                                        <button @click="qty = Math.min(10, qty + 1); updateQuantity({{ $item['menu_item_id'] }}, qty)"
                                            class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-right text-sm text-gray-500 mt-1">
                                    Subtotal: <span class="font-semibold text-brew-dark">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Clear Cart --}}
                <div class="text-center pt-2">
                    <button @click="clearCart()" class="text-red-500 text-sm hover:text-red-600">
                        🗑️ Kosongkan Keranjang
                    </button>
                </div>
            </div>

            {{-- Cart Summary (Fixed Bottom) --}}
            <div class="fixed bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
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
