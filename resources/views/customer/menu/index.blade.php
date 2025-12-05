@extends('layouts.custLayout')

@section('title', 'Menu - MoodBrew')

@section('content')
    <div class="pb-20 lg:pb-8">
        {{-- Desktop Layout Container --}}
        <div class="lg:max-w-7xl lg:mx-auto lg:px-8 lg:py-6">
            
            {{-- Desktop Header --}}
            <div class="hidden lg:block mb-8">
                <h1 class="text-3xl font-bold text-brew-dark">Menu MoodBrew ☕</h1>
                <p class="text-gray-600 mt-1">Temukan minuman yang cocok dengan mood-mu</p>
            </div>

            {{-- Header with Search --}}
            <div class="bg-white sticky top-14 lg:top-0 z-40 border-b border-gray-100 p-4 lg:rounded-xl lg:shadow-sm lg:mb-6 lg:border lg:static">
                <form method="GET" action="{{ route('customer.menu.index') }}" class="relative lg:max-w-md">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" 
                        placeholder="Cari menu..."
                        class="w-full px-4 py-3 pl-10 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold/50">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>
            </div>

            {{-- Flash Sales Banner (Global Promo) --}}
            @if($flashSales->isNotEmpty())
                @php $currentPromo = $flashSales->first(); @endphp
                <div class="p-4 lg:p-6 bg-gradient-to-r from-red-500 to-orange-500 lg:rounded-xl lg:mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between text-white mb-3 lg:mb-0">
                        <div>
                            <h3 class="font-bold flex items-center text-lg lg:text-xl">
                                <span class="text-xl mr-2">🔥</span> {{ $currentPromo->name }}
                            </h3>
                            <p class="text-white/80 text-sm mt-1">
                                Diskon {{ $currentPromo->discount_percentage }}% untuk semua menu!
                            </p>
                        </div>
                        <div class="mt-3 lg:mt-0 flex items-center space-x-4">
                            <span class="text-xs lg:text-sm bg-white/20 px-3 py-1 rounded-full">
                                Kode: <strong>{{ $currentPromo->promo_code }}</strong>
                            </span>
                            @if($currentPromo->ends_at)
                                <span class="text-xs lg:text-sm bg-white/20 px-3 py-1 rounded-full">
                                    ⏰ Berakhir {{ $currentPromo->ends_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="lg:flex lg:gap-8">
                {{-- Desktop Sidebar --}}
                <div class="hidden lg:block lg:w-64 lg:flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-sm p-4 sticky top-24">
                        <h3 class="font-bold text-brew-dark mb-4">Kategori</h3>
                        <div class="space-y-2">
                            <a href="{{ route('customer.menu.index') }}" 
                               class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                                      {{ !($filters['category'] ?? null) ? 'bg-brew-brown text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                📋 Semua Menu
                            </a>
                            @foreach($categories as $key => $cat)
                                <a href="{{ route('customer.menu.index', ['category' => $key]) }}" 
                                   class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                                          {{ ($filters['category'] ?? '') === $key ? 'bg-brew-brown text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                    {{ $cat['icon'] }} {{ $cat['name'] }} <span class="text-xs opacity-70">({{ $cat['count'] }})</span>
                                </a>
                            @endforeach
                        </div>

                        {{-- Desktop Sort Options --}}
                        <h3 class="font-bold text-brew-dark mb-4 mt-6">Urutkan</h3>
                        <div class="space-y-2">
                            <a href="{{ route('customer.menu.index', array_merge(request()->query(), ['sort' => 'name'])) }}"
                               class="block px-4 py-2 rounded-lg text-sm {{ ($filters['sort'] ?? 'name') === 'name' ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-50' }}">
                                A-Z
                            </a>
                            <a href="{{ route('customer.menu.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => 'asc'])) }}"
                               class="block px-4 py-2 rounded-lg text-sm {{ ($filters['sort'] ?? '') === 'price' ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-50' }}">
                                Harga Terendah
                            </a>
                            <a href="{{ route('customer.menu.index', array_merge(request()->query(), ['sort' => 'popularity'])) }}"
                               class="block px-4 py-2 rounded-lg text-sm {{ ($filters['sort'] ?? '') === 'popularity' ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-50' }}">
                                Terpopuler
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="flex-1">
                    {{-- Mobile Categories (Horizontal Scroll) --}}
                    <div class="p-4 lg:hidden">
                        <div class="flex space-x-2 overflow-x-auto pb-2">
                            <a href="{{ route('customer.menu.index') }}" 
                               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                                      {{ !($filters['category'] ?? null) ? 'bg-brew-brown text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Semua
                            </a>
                            @foreach($categories as $key => $cat)
                                <a href="{{ route('customer.menu.index', ['category' => $key]) }}" 
                                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors flex items-center space-x-1
                                          {{ ($filters['category'] ?? '') === $key ? 'bg-brew-brown text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    <span>{{ $cat['icon'] }}</span>
                                    <span>{{ $cat['name'] }}</span>
                                    <span class="text-xs opacity-70">({{ $cat['count'] }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Popular Section --}}
                    @if(!($filters['category'] ?? null) && !($filters['search'] ?? null) && $popularItems->isNotEmpty())
                        <div class="px-4 mb-4 lg:px-0 lg:mb-6">
                            <h3 class="font-bold text-brew-dark mb-3 flex items-center text-lg">
                                <span class="text-xl mr-2">⭐</span> Populer
                            </h3>
                            <div class="flex space-x-3 overflow-x-auto pb-2 lg:grid lg:grid-cols-4 lg:gap-4 lg:overflow-visible">
                                @foreach($popularItems as $item)
                                    <a href="{{ route('customer.menu.show', $item->slug) }}" 
                                       class="flex-shrink-0 w-32 lg:w-full bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="w-full h-16 lg:h-24 bg-brew-cream rounded-lg mb-2 flex items-center justify-center">
                                            <span class="text-2xl lg:text-4xl">{{ $item->category === 'coffee' ? '☕' : '🥤' }}</span>
                                        </div>
                                        <p class="font-semibold text-brew-dark text-xs lg:text-sm truncate">{{ $item->name }}</p>
                                        <p class="text-brew-gold font-bold text-sm lg:text-base">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Menu Grid --}}
                    <div class="px-4 lg:px-0">
                        <h3 class="font-bold text-brew-dark mb-3 text-lg">
                            @if($filters['category'] ?? null)
                                {{ $categories[$filters['category']]['icon'] ?? '📋' }} {{ $categories[$filters['category']]['name'] ?? 'Menu' }}
                            @elseif($filters['search'] ?? null)
                                🔍 Hasil Pencarian "{{ $filters['search'] }}"
                            @else
                                📋 Semua Menu
                            @endif
                        </h3>
                        
                        @if($menuItems->isEmpty())
                            <div class="text-center py-12 bg-white rounded-xl">
                                <span class="text-4xl block mb-3">😢</span>
                                <p class="text-gray-500">Menu tidak ditemukan</p>
                                <a href="{{ route('customer.menu.index') }}" class="text-brew-gold text-sm mt-2 inline-block">
                                    ← Lihat semua menu
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4">
                                @foreach($menuItems as $item)
                                    <a href="{{ route('customer.menu.show', $item->slug) }}" 
                                       class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow group">
                                        <div class="w-full h-28 lg:h-40 bg-brew-cream flex items-center justify-center relative">
                                            <span class="text-4xl lg:text-6xl group-hover:scale-110 transition-transform">
                                                {{ $item->category === 'coffee' ? '☕' : ($item->category === 'pastry' ? '🥐' : '🥤') }}
                                            </span>
                                            @if(!$item->is_available || $item->stock_quantity <= 0)
                                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                    <span class="text-white text-xs font-semibold bg-red-500 px-2 py-1 rounded">Habis</span>
                                                </div>
                                            @elseif($flashSales->isNotEmpty())
                                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                    -{{ $flashSales->first()->discount_percentage }}%
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3 lg:p-4">
                                            <p class="font-semibold text-brew-dark text-sm lg:text-base truncate">{{ $item->name }}</p>
                                            <p class="text-gray-500 text-xs lg:text-sm truncate">{{ $item->description ?? 'Minuman nikmat' }}</p>
                                            <div class="flex items-center justify-between mt-2">
                                                @if($flashSales->isNotEmpty())
                                                    <div>
                                                        <p class="text-gray-400 text-xs line-through">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                        <p class="text-red-500 font-bold text-sm lg:text-base">
                                                            Rp {{ number_format($flashSales->first()->calculateFinalPrice($item->price), 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <p class="text-brew-gold font-bold lg:text-lg">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                @endif
                                                @if($item->is_available && $item->stock_quantity > 0)
                                                    <button onclick="event.preventDefault(); addToCart({{ $item->id }})" 
                                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-brew-gold rounded-full flex items-center justify-center text-brew-dark hover:bg-yellow-400 transition-colors">
                                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            <div class="mt-6">
                                {{ $menuItems->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
async function addToCart(menuItemId) {
    try {
        const response = await fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ menu_item_id: menuItemId, quantity: 1 })
        });
        
        const data = await response.json();
        if (data.success) {
            showToast(data.message);
            if (data.cart_count) {
                updateCartBadge(data.cart_count);
            }
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
    const badges = document.querySelectorAll('[data-cart-badge]');
    badges.forEach(badge => {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });
}
</script>
@endpush
