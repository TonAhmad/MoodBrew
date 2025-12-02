@extends('layouts.cashierLayout')

@section('title', 'Buat Pesanan Baru')

@section('content')
<div class="space-y-6" x-data="orderForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Pesanan Baru</h1>
            <p class="text-gray-600">Buat pesanan manual untuk pelanggan</p>
        </div>
        <a href="{{ route('cashier.orders.pending') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('cashier.orders.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Menu Selection -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Customer Name -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan (Opsional)</label>
                    <input type="text" name="customer_name" placeholder="Masukkan nama pelanggan..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <!-- Menu Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="font-semibold text-brew-dark">Pilih Menu</h2>
                            <input type="text" x-model="searchQuery" placeholder="Cari menu..."
                                   class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="p-4 border-b border-gray-100 flex flex-wrap gap-2">
                        <button type="button" @click="filterCategory = ''" 
                                :class="filterCategory === '' ? 'bg-brew-gold text-brew-dark' : 'bg-gray-100 text-gray-700'"
                                class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                            Semua
                        </button>
                        @php
                            $categories = $menuItems->pluck('category')->unique();
                        @endphp
                        @foreach($categories as $category)
                        <button type="button" @click="filterCategory = '{{ $category }}'" 
                                :class="filterCategory === '{{ $category }}' ? 'bg-brew-gold text-brew-dark' : 'bg-gray-100 text-gray-700'"
                                class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                            {{ ucfirst($category) }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Menu Grid -->
                    <div class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                        @foreach($menuItems as $item)
                        <div x-show="(filterCategory === '' || '{{ $item->category }}' === filterCategory) && 
                                     (searchQuery === '' || '{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase()))"
                             @click="addItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})"
                             class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-brew-gold hover:bg-brew-cream/30 transition-all">
                            <div class="w-full h-20 bg-gray-100 rounded-lg mb-2 flex items-center justify-center overflow-hidden">
                                @if($item->image_url)
                                <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-2xl">☕</span>
                                @endif
                            </div>
                            <p class="font-medium text-sm text-brew-dark truncate">{{ $item->name }}</p>
                            <p class="text-sm text-brew-gold font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-4">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="font-semibold text-brew-dark">Ringkasan Pesanan</h2>
                    </div>

                    <!-- Selected Items -->
                    <div class="p-4 space-y-3 max-h-64 overflow-y-auto">
                        <template x-if="selectedItems.length === 0">
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-gray-400 text-sm">Belum ada item dipilih</p>
                            </div>
                        </template>

                        <template x-for="(item, index) in selectedItems" :key="index">
                            <div class="flex items-start gap-3 p-2 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-brew-dark" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="'Rp ' + item.price.toLocaleString('id-ID')"></span> x 
                                        <span x-text="item.quantity"></span>
                                    </p>
                                    <input type="hidden" :name="'items[' + index + '][menu_item_id]'" :value="item.id">
                                    <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                                    <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes"
                                           placeholder="Catatan..."
                                           class="mt-1 w-full px-2 py-1 text-xs border border-gray-200 rounded focus:ring-1 focus:ring-brew-gold">
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <p class="font-bold text-sm text-brew-gold" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="decreaseQuantity(index)"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300">
                                            -
                                        </button>
                                        <span class="w-6 text-center text-sm" x-text="item.quantity"></span>
                                        <button type="button" @click="increaseQuantity(index)"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300">
                                            +
                                        </button>
                                    </div>
                                    <button type="button" @click="removeItem(index)"
                                            class="text-xs text-red-500 hover:text-red-700">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Notes -->
                    <div class="p-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pesanan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan tambahan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brew-gold focus:border-brew-gold"></textarea>
                    </div>

                    <!-- Total & Submit -->
                    <div class="p-4 border-t border-gray-100 bg-brew-cream/30">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600">Total</span>
                            <span class="text-2xl font-bold text-brew-dark" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                        </div>
                        <button type="submit" :disabled="selectedItems.length === 0"
                                :class="selectedItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-brew-gold hover:bg-yellow-500'"
                                class="w-full py-3 text-brew-dark font-bold rounded-lg transition-colors">
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function orderForm() {
    return {
        selectedItems: [],
        searchQuery: '',
        filterCategory: '',
        
        addItem(id, name, price) {
            const existing = this.selectedItems.find(item => item.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                this.selectedItems.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    notes: ''
                });
            }
        },
        
        removeItem(index) {
            this.selectedItems.splice(index, 1);
        },
        
        increaseQuantity(index) {
            this.selectedItems[index].quantity++;
        },
        
        decreaseQuantity(index) {
            if (this.selectedItems[index].quantity > 1) {
                this.selectedItems[index].quantity--;
            } else {
                this.removeItem(index);
            }
        },
        
        calculateTotal() {
            return this.selectedItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }
    }
}
</script>
@endpush
@endsection
