@extends('layouts.custLayout')

@section('title', $menuItem->name . ' - MoodBrew')

@section('content')
    <div class="pb-24 lg:pb-8">
        {{-- Desktop Container --}}
        <div class="lg:max-w-6xl lg:mx-auto lg:px-8 lg:py-8">
            
            {{-- Back Button --}}
            <div class="p-4 flex items-center lg:px-0 lg:mb-4">
                <a href="{{ route('customer.menu.index') }}" class="flex items-center text-gray-600 hover:text-brew-dark transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Menu
                </a>
            </div>

            {{-- Desktop Grid Layout --}}
            <div class="lg:grid lg:grid-cols-2 lg:gap-12">
                {{-- Product Image --}}
                <div class="w-full h-64 lg:h-96 bg-brew-cream flex items-center justify-center relative lg:rounded-2xl lg:shadow-lg">
                    <span class="text-8xl lg:text-9xl">{{ $menuItem->category === 'coffee' ? '☕' : ($menuItem->category === 'pastry' ? '🥐' : '🥤') }}</span>
                    @if($flashSale)
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold lg:text-base lg:px-4 lg:py-2">
                            -{{ $flashSale['discount_percent'] }}%
                        </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="p-4 bg-white -mt-4 rounded-t-3xl relative lg:mt-0 lg:rounded-none lg:p-0">
                    {{-- Category & Name --}}
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="px-2 py-1 bg-brew-cream text-brew-brown text-xs lg:text-sm rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $menuItem->category)) }}
                            </span>
                            <h1 class="text-2xl lg:text-3xl font-bold text-brew-dark mt-2">{{ $menuItem->name }}</h1>
                        </div>
                        <div class="text-right">
                            @if($flashSale)
                                <p class="text-gray-400 line-through text-sm lg:text-base">Rp {{ number_format($flashSale['original_price'], 0, ',', '.') }}</p>
                                <p class="text-2xl lg:text-3xl font-bold text-red-500">Rp {{ number_format($flashSale['sale_price'], 0, ',', '.') }}</p>
                                <p class="text-xs text-red-500 mt-1">{{ $flashSale['promo_name'] ?? 'Promo!' }}</p>
                            @else
                                <p class="text-2xl lg:text-3xl font-bold text-brew-gold">Rp {{ number_format($menuItem->price, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <p class="text-gray-600 mb-4 lg:text-lg lg:mb-6">
                        {{ $menuItem->description ?? 'Minuman nikmat dengan cita rasa yang khas dari MoodBrew.' }}
                    </p>

                    {{-- Info Cards for Desktop --}}
                    <div class="hidden lg:grid lg:grid-cols-3 lg:gap-4 lg:mb-6">
                        @if($menuItem->flavor_profile && isset($menuItem->flavor_profile['acidity']))
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Flavor</h4>
                                <p class="font-semibold text-brew-dark">
                                    Acidity: {{ $menuItem->flavor_profile['acidity'] ?? '-' }}/5, 
                                    Body: {{ $menuItem->flavor_profile['body'] ?? '-' }}/5
                                </p>
                            </div>
                        @endif
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Stok</h4>
                            <p class="font-semibold {{ $menuItem->stock_quantity > 10 ? 'text-green-600' : ($menuItem->stock_quantity > 0 ? 'text-orange-500' : 'text-red-500') }}">
                                {{ $menuItem->stock_quantity > 10 ? 'Tersedia' : ($menuItem->stock_quantity > 0 ? 'Sisa ' . $menuItem->stock_quantity : 'Habis') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Kategori</h4>
                            <p class="font-semibold text-brew-dark">{{ ucfirst(str_replace('_', ' ', $menuItem->category)) }}</p>
                        </div>
                    </div>

                    {{-- Flavor Profile (Mobile) --}}
                    @if($menuItem->flavor_profile && isset($menuItem->flavor_profile['acidity']))
                        <div class="mb-4 lg:hidden">
                            <h3 class="font-semibold text-brew-dark mb-2">Flavor Profile</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-brew-cream text-brew-brown text-sm rounded-full">
                                    Acidity: {{ $menuItem->flavor_profile['acidity'] }}/5
                                </span>
                                <span class="px-3 py-1 bg-brew-cream text-brew-brown text-sm rounded-full">
                                    Body: {{ $menuItem->flavor_profile['body'] ?? '-' }}/5
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Mood Tags --}}
                    @if($menuItem->mood_tags && is_array($menuItem->mood_tags))
                        <div class="mb-4 lg:mb-6">
                            <h3 class="font-semibold text-brew-dark mb-2 lg:text-lg">Cocok untuk</h3>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $moodEmojis = [
                                        'happy' => '😊',
                                        'relaxed' => '😌',
                                        'energetic' => '⚡',
                                        'tired' => '😴',
                                        'stressed' => '😤',
                                        'romantic' => '🥰',
                                    ];
                                @endphp
                                @foreach($menuItem->mood_tags as $mood)
                                    <span class="px-3 py-1 lg:px-4 lg:py-2 bg-purple-50 text-purple-700 text-sm lg:text-base rounded-full">
                                        {{ $moodEmojis[$mood] ?? '✨' }} {{ ucfirst($mood) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Stock Info (Mobile) --}}
                    <div class="flex items-center space-x-2 text-sm mb-6 lg:hidden">
                        @if($menuItem->stock_quantity > 10)
                            <span class="text-green-600">✓ Tersedia</span>
                        @elseif($menuItem->stock_quantity > 0)
                            <span class="text-orange-500">⚠ Stok terbatas ({{ $menuItem->stock_quantity }})</span>
                        @else
                            <span class="text-red-500">✗ Stok habis</span>
                        @endif
                    </div>

                    {{-- Quantity Selector & Add to Cart --}}
                    @if($menuItem->is_available && $menuItem->stock_quantity > 0)
                        <div x-data="{ quantity: 1, maxQty: {{ min($menuItem->stock_quantity, 10) }} }" class="mb-4 lg:bg-gray-50 lg:rounded-xl lg:p-6">
                            <h3 class="font-semibold text-brew-dark mb-3 lg:text-lg">Jumlah</h3>
                            <div class="flex items-center space-x-4 mb-4">
                                <button @click="quantity = Math.max(1, quantity - 1)"
                                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 lg:bg-white rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 lg:hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="text-xl lg:text-2xl font-bold text-brew-dark w-8 text-center" x-text="quantity"></span>
                                <button @click="quantity = Math.min(maxQty, quantity + 1)"
                                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-100 lg:bg-white rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 lg:hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Note Input --}}
                            <div class="mb-4">
                                <label class="text-sm text-gray-600 block mb-1">Catatan (opsional)</label>
                                <input type="text" x-ref="note" placeholder="Contoh: less sugar, extra shot"
                                    class="w-full px-4 py-2 lg:py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50">
                            </div>

                            {{-- Add to Cart Button --}}
                            <button @click="addToCartWithDetails({{ $menuItem->id }}, quantity, $refs.note.value)"
                                class="w-full py-4 lg:py-5 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors flex items-center justify-center space-x-2 text-lg">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 lg:p-6 text-center">
                            <span class="text-red-500 font-semibold lg:text-lg">😢 Maaf, menu ini sedang tidak tersedia</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Items --}}
            @if($relatedItems->isNotEmpty())
                <div class="p-4 bg-gray-50 lg:bg-transparent lg:p-0 lg:mt-12">
                    <h3 class="font-bold text-brew-dark mb-3 lg:text-xl lg:mb-6">Mungkin kamu juga suka</h3>
                    <div class="flex space-x-3 overflow-x-auto pb-2 lg:grid lg:grid-cols-4 lg:gap-4 lg:overflow-visible">
                        @foreach($relatedItems as $item)
                            @if($item->id !== $menuItem->id)
                                <a href="{{ route('customer.menu.show', $item->slug) }}" 
                                   class="flex-shrink-0 w-32 lg:w-full bg-white rounded-xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="w-full h-16 lg:h-24 bg-brew-cream rounded-lg mb-2 flex items-center justify-center">
                                        <span class="text-2xl lg:text-4xl">{{ $item->category === 'coffee' ? '☕' : '🥤' }}</span>
                                    </div>
                                    <p class="font-semibold text-brew-dark text-xs lg:text-sm truncate">{{ $item->name }}</p>
                                    <p class="text-brew-gold font-bold text-sm lg:text-base">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
async function addToCartWithDetails(menuItemId, quantity, note) {
    try {
        const response = await fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                menu_item_id: menuItemId, 
                quantity: quantity,
                note: note
            })
        });
        
        const data = await response.json();
        if (data.success) {
            showToast(data.message);
            updateCartBadge(data.cart_count);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Gagal menambahkan ke keranjang', 'error');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-20 lg:bottom-8 left-1/2 -translate-x-1/2 px-4 py-2 rounded-lg text-white text-sm z-50 
        ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 2000);
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = count;
        badge.classList.remove('hidden');
    }
}
</script>
@endpush
