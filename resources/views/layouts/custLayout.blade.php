<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MoodBrew') - Pesan dengan AI</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/moodbrew.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/moodbrew.png') }}">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brew-brown': '#4A3728',
                        'brew-cream': '#F5E6D3',
                        'brew-gold': '#C9A227',
                        'brew-dark': '#2C1810',
                        'brew-light': '#FDF8F3',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="bg-brew-light font-sans antialiased">
    {{-- Top Header - Mobile --}}
    <header class="bg-white shadow-sm sticky top-0 z-50 lg:hidden">
        <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('landing.home') }}" class="flex items-center space-x-2">
                <img src="{{ asset('assets/moodbrew.png') }}" alt="MoodBrew Logo" class="w-8 h-8 rounded-full object-cover">
                <span class="font-display font-bold text-brew-dark">MoodBrew</span>
            </a>

            <div class="flex items-center space-x-3">
                @if (session('table_number'))
                    <span class="px-3 py-1 bg-brew-cream text-brew-brown text-sm rounded-full">
                        Meja {{ session('table_number') }}
                    </span>
                @endif
                @if(session('customer_name'))
                    <span class="text-sm text-gray-600">👋 {{ session('customer_name') }}</span>
                @endif
                {{-- Logout Button Mobile --}}
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Desktop Header --}}
    <header class="hidden lg:block bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('landing.home') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('assets/moodbrew.png') }}" alt="MoodBrew Logo" class="w-10 h-10 rounded-full object-cover">
                    <span class="font-bold text-xl text-brew-dark">MoodBrew</span>
                </a>

                {{-- Desktop Navigation --}}
                <nav class="flex items-center space-x-8">
                    <a href="{{ route('customer.home') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.home') ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span>AI Chat</span>
                    </a>
                    <a href="{{ route('customer.menu.index') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.menu.*') ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Menu</span>
                    </a>
                    <a href="{{ route('customer.vibewall.index') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.vibewall.*') ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Vibe Wall</span>
                    </a>
                    <a href="{{ route('customer.orders.index') }}" 
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.orders.*') && !request()->routeIs('customer.orders.checkout') ? 'bg-brew-cream text-brew-brown' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Pesanan</span>
                    </a>
                </nav>

                {{-- Right Side - Cart & User Info --}}
                <div class="flex items-center space-x-6">
                    @if(session('customer_name'))
                        <div class="flex items-center space-x-2 text-gray-600">
                            @if (session('table_number'))
                                <span class="px-3 py-1 bg-brew-cream text-brew-brown text-sm rounded-full">
                                    Meja {{ session('table_number') }}
                                </span>
                            @endif
                            <span class="text-sm">👋 {{ session('customer_name') }}</span>
                        </div>
                    @endif
                    
                    {{-- Cart Button --}}
                    <a href="{{ route('customer.cart.index') }}" 
                       class="relative flex items-center space-x-2 px-4 py-2 bg-brew-gold text-brew-dark rounded-lg hover:bg-yellow-400 transition-colors font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Keranjang</span>
                        @php
                            $cartCount = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
                        @endphp
                        @if($cartCount > 0)
                            <span id="cartBadgeDesktop" data-cart-badge class="ml-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @else
                            <span id="cartBadgeDesktop" data-cart-badge class="ml-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                        @endif
                    </a>

                    {{-- Logout Button Desktop --}}
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-lg mx-auto min-h-screen lg:max-w-none">
        @yield('content')
    </main>

    {{-- Bottom Navigation - Mobile Only --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="max-w-lg mx-auto px-4 py-2 flex justify-around">
            <a href="{{ route('customer.home') }}"
                class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('customer.home') ? 'text-brew-gold' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="text-xs mt-1">AI Chat</span>
            </a>
            <a href="{{ route('customer.menu.index') }}" 
               class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('customer.menu.*') ? 'text-brew-gold' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-xs mt-1">Menu</span>
            </a>
            <a href="{{ route('customer.cart.index') }}" 
               class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('customer.cart.*', 'customer.orders.checkout') ? 'text-brew-gold' : 'text-gray-500' }} relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php
                    $cartCount = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
                @endphp
                @if($cartCount > 0)
                    <span id="cartBadge" data-cart-badge class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                @else
                    <span id="cartBadge" data-cart-badge class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                @endif
                <span class="text-xs mt-1">Keranjang</span>
            </a>
            <a href="{{ route('customer.vibewall.index') }}" 
               class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('customer.vibewall.*') ? 'text-brew-gold' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <span class="text-xs mt-1">Vibe Wall</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>

</html>
