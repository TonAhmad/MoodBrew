@extends('layouts.landingLayout')

@section('title', 'Daftar - MoodBrew')

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
                    Buat Akun Baru
                </h1>
                <p class="text-gray-600 mt-2">
                    Bergabung untuk pengalaman ngopi yang lebih personal
                </p>
            </div>

            {{-- Register Form --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form method="POST" action="#" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" required autocomplete="name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="John Doe">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="nama@email.com">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="Minimal 8 karakter">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="Ulangi password">
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required
                            class="w-4 h-4 mt-1 text-brew-gold border-gray-300 rounded focus:ring-brew-gold">
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            Saya setuju dengan
                            <a href="#" class="text-brew-gold hover:text-brew-brown">Syarat & Ketentuan</a>
                            dan
                            <a href="#" class="text-brew-gold hover:text-brew-brown">Kebijakan Privasi</a>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full py-3 px-4 bg-brew-brown text-white font-medium rounded-xl hover:bg-brew-dark focus:outline-none focus:ring-2 focus:ring-brew-gold focus:ring-offset-2 transition-all transform hover:scale-[1.02]">
                        Daftar
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau</span>
                    </div>
                </div>

                {{-- Guest Option --}}
                <a href="{{ route('landing.home') }}"
                    class="w-full py-3 px-4 bg-brew-cream text-brew-brown font-medium rounded-xl hover:bg-brew-gold/20 transition-all flex items-center justify-center space-x-2">
                    <span>☕</span>
                    <span>Lanjut Sebagai Guest</span>
                </a>
            </div>

            {{-- Login Link --}}
            <p class="text-center mt-6 text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-brew-gold hover:text-brew-brown font-medium transition-colors">
                    Masuk di sini
                </a>
            </p>
        </div>
    </section>
@endsection
