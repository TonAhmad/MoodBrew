@extends('layouts.landingLayout')

@section('title', 'Login Staff - MoodBrew')

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
                    Login Staff
                </h1>
                <p class="text-gray-600 mt-2">
                    Masuk untuk mengelola MoodBrew
                </p>
            </div>

            {{-- Login Form --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('staff.login') }}" class="space-y-6">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="staff@moodbrew.id">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                            placeholder="••••••••">
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="w-4 h-4 text-brew-gold border-gray-300 rounded focus:ring-brew-gold">
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Ingat saya
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full py-3 px-4 bg-brew-brown text-white font-medium rounded-xl hover:bg-brew-dark focus:outline-none focus:ring-2 focus:ring-brew-gold focus:ring-offset-2 transition-all transform hover:scale-[1.02]">
                        Masuk
                    </button>
                </form>
            </div>

            {{-- Customer Access Link --}}
            <div class="text-center mt-6 space-y-2">
                <p class="text-gray-600">
                    Customer?
                    <a href="{{ route('login') }}"
                        class="text-brew-gold hover:text-brew-brown font-medium transition-colors">
                        Pesan tanpa login di sini
                    </a>
                </p>
                <p class="text-gray-400 text-sm">
                    <a href="{{ route('landing.home') }}" class="hover:text-brew-brown transition-colors">
                        ← Kembali ke Home
                    </a>
                </p>
            </div>
        </div>
    </section>
@endsection
