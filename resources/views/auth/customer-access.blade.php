@extends('layouts.landingLayout')

@section('title', 'Mulai Pesan - MoodBrew')

@section('content')
    <section
        class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-brew-cream to-white">
        <div class="max-w-md w-full">
            {{-- Logo & Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('landing.home') }}" class="inline-flex items-center space-x-2 mb-6">
                    <div class="w-12 h-12 bg-brew-brown rounded-full flex items-center justify-center">
                        <span class="text-brew-cream font-display font-bold text-xl">M</span>
                    </div>
                </a>
                <h1 class="font-display text-3xl font-bold text-brew-dark">
                    Mulai Pesan di MoodBrew ☕
                </h1>
                <p class="text-gray-600 mt-2">
                    Isi data singkat untuk mulai. Tidak perlu password, langsung pesan!
                </p>
            </div>

            {{-- Quick Access Form --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form method="POST" action="{{ route('customer.access') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Panggilan
                        </label>
                        <input type="text" id="name" name="name" required autocomplete="name"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="Panggil saya...">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="email@contoh.com">
                        <p class="mt-1 text-xs text-gray-500">Untuk menerima notifikasi pesanan</p>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Table Number (Optional) --}}
                    <div>
                        <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Meja <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" id="table_number" name="table_number"
                            value="{{ old('table_number', request('table')) }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="Contoh: A5">
                        <p class="mt-1 text-xs text-gray-500">Bisa diisi nanti atau dari scan QR meja</p>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full py-4 px-4 bg-brew-brown text-white font-bold rounded-xl hover:bg-brew-dark focus:outline-none focus:ring-2 focus:ring-brew-gold focus:ring-offset-2 transition-all transform hover:scale-[1.02] flex items-center justify-center space-x-2">
                        <span>Mulai Pesan dengan AI</span>
                        <span>🤖</span>
                    </button>
                </form>

                {{-- Info --}}
                <div class="mt-6 p-4 bg-brew-cream/50 rounded-xl">
                    <div class="flex items-start space-x-3">
                        <span class="text-xl">💡</span>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium text-brew-dark mb-1">Mengapa tanpa password?</p>
                            <p>Kami ingin kamu langsung bisa menikmati pengalaman MoodBrew tanpa ribet. Ceritakan mood-mu,
                                dapatkan rekomendasi, dan bayar di kasir saat siap!</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Staff Login Link --}}
            <p class="text-center mt-6 text-gray-500 text-sm">
                Staff?
                <a href="{{ route('staff.login') }}" class="text-brew-gold hover:text-brew-brown font-medium transition-colors">
                    Login di sini
                </a>
            </p>
        </div>
    </section>
@endsection
