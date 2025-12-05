@extends('layouts.custLayout')

@section('title', 'Checkout - MoodBrew')

@section('content')
    <div class="pb-32 lg:pb-8">
        {{-- Desktop Container --}}
        <div class="lg:max-w-7xl lg:mx-auto lg:px-8 lg:py-6">
            {{-- Header --}}
            <div class="bg-white p-4 border-b border-gray-100 flex items-center lg:border-0 lg:mb-6">
                <a href="{{ route('customer.cart.index') }}" class="mr-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl lg:text-3xl font-bold text-brew-dark">Checkout</h1>
                    <p class="hidden lg:block text-gray-600 text-sm mt-1">Lengkapi informasi pesanan</p>
                </div>
            </div>

            <form action="{{ route('customer.orders.store') }}" method="POST" class="lg:grid lg:grid-cols-3 lg:gap-6">
                {{-- Main Content (Mobile: full width, Desktop: 2 columns) --}}
                <div class="lg:col-span-2 p-4 lg:p-0 space-y-4">
                @csrf
                
                {{-- Customer Info --}}
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                    <h3 class="font-semibold text-brew-dark mb-4 text-lg lg:text-xl">📋 Informasi Pelanggan</h3>
                
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm lg:text-base text-gray-600 mb-2 font-medium">Nama</label>
                            <input type="text" name="customer_name" value="{{ session('customer_name', old('customer_name')) }}" required
                                class="w-full px-4 py-3 lg:py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold focus:border-transparent transition-all"
                                placeholder="Masukkan nama kamu">
                            @error('customer_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm lg:text-base text-gray-600 mb-2 font-medium">Nomor Meja</label>
                            <input type="text" name="table_number" value="{{ session('table_number', old('table_number')) }}" required
                                class="w-full px-4 py-3 lg:py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold focus:border-transparent transition-all"
                                placeholder="Contoh: A1, B2, VIP1">
                            @error('table_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm lg:text-base text-gray-600 mb-2 font-medium">Catatan Tambahan (opsional)</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-3 lg:py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brew-gold focus:border-transparent transition-all"
                                placeholder="Catatan untuk pesanan ini...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Order Summary Mobile --}}
                <div class="lg:hidden bg-white rounded-xl shadow-lg p-4">
                    <h3 class="font-semibold text-brew-dark mb-4 text-lg">🛍️ Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-brew-cream rounded-lg flex items-center justify-center">
                                        <span class="text-xl">{{ $item['menu_item']->category === 'coffee' ? '☕' : '🥤' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-brew-dark">{{ $item['menu_item']->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price_at_moment'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="font-semibold text-brew-dark">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t-2 border-gray-200">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-semibold text-brew-dark">Total</span>
                            <span class="font-bold text-brew-gold">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Info Mobile --}}
                <div class="lg:hidden bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">💡</span>
                        <div>
                            <h4 class="font-semibold text-blue-800">Pembayaran di Kasir</h4>
                            <p class="text-sm text-blue-600 mt-1">
                                Pesanan akan dikirim ke kasir. Kamu bisa membayar langsung di kasir saat pesanan siap.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Sidebar Summary --}}
            <div class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-brew-dark mb-4 text-xl">🛍️ Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3 mb-6 max-h-[400px] overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                            <div class="flex items-start space-x-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="w-14 h-14 bg-brew-cream rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-2xl">{{ $item['menu_item']->category === 'coffee' ? '☕' : '🥤' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-brew-dark truncate">{{ $item['menu_item']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price_at_moment'], 0, ',', '.') }}</p>
                                    <p class="text-sm font-semibold text-brew-gold mt-1">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t-2 border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Subtotal ({{ count($cartItems) }} item)</span>
                            <span class="font-semibold text-brew-dark">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xl mt-3">
                            <span class="font-bold text-brew-dark">Total</span>
                            <span class="font-bold text-brew-gold">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-200">
                        <div class="flex items-start space-x-2">
                            <span class="text-xl">💡</span>
                            <div>
                                <h4 class="font-semibold text-blue-800 text-sm">Pembayaran di Kasir</h4>
                                <p class="text-xs text-blue-600 mt-1">
                                    Bayar di kasir saat pesanan siap
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-all shadow-md hover:shadow-lg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </form>

        {{-- Mobile Place Order Button --}}
        <div class="lg:hidden fixed bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
            <div class="max-w-lg mx-auto">
                <button type="submit" onclick="document.querySelector('form').submit()"
                    class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors shadow-md">
                    Pesan Sekarang
                </button>
            </div>
        </div>
        </div>
    </div>
@endsection
