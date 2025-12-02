@extends('layouts.custLayout')

@section('title', 'Checkout - MoodBrew')

@section('content')
    <div class="pb-32">
        {{-- Header --}}
        <div class="bg-white p-4 border-b border-gray-100 flex items-center">
            <a href="{{ route('customer.cart.index') }}" class="mr-3">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-brew-dark">Checkout</h1>
        </div>

        <form action="{{ route('customer.orders.store') }}" method="POST" class="p-4 space-y-4">
            @csrf
            
            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-brew-dark mb-4">📋 Informasi Pelanggan</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nama</label>
                        <input type="text" name="customer_name" value="{{ session('customer_name', old('customer_name')) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50"
                            placeholder="Masukkan nama kamu">
                        @error('customer_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nomor Meja</label>
                        <input type="text" name="table_number" value="{{ session('table_number', old('table_number')) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50"
                            placeholder="Contoh: A1, B2, VIP1">
                        @error('table_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Catatan Tambahan (opsional)</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brew-gold/50"
                            placeholder="Catatan untuk pesanan ini...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-brew-dark mb-4">🛍️ Ringkasan Pesanan</h3>
                
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
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

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center text-lg">
                        <span class="font-semibold text-brew-dark">Total</span>
                        <span class="font-bold text-brew-gold">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-blue-50 rounded-xl p-4">
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
        </form>

        {{-- Place Order Button --}}
        <div class="fixed bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
            <div class="max-w-lg mx-auto">
                <button type="submit" form="checkout-form" onclick="document.querySelector('form').submit()"
                    class="w-full py-4 bg-brew-gold text-brew-dark font-bold rounded-xl hover:bg-yellow-400 transition-colors">
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </div>
@endsection
