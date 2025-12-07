<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - MoodBrew Kasir</title>

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

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div x-show="mobileMenuOpen" x-cloak @click="mobileMenuOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        {{-- Sidebar --}}
        @include('components.sidebarCashier')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
            {{-- Top Navbar --}}
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-4">
                        {{-- Mobile Menu Toggle --}}
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-gray-600 hover:text-brew-brown lg:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        {{-- Desktop Sidebar Toggle --}}
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="text-gray-600 hover:text-brew-brown hidden lg:block p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-brew-dark">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- User Menu --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 text-gray-700 hover:text-brew-brown">
                                <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center">
                                    <span
                                        class="text-brew-dark font-bold text-sm">{{ substr(auth()->user()->name ?? 'K', 0, 1) }}</span>
                                </div>
                                <span class="hidden sm:inline">{{ auth()->user()->name ?? 'Kasir' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Kasir' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
                <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} MoodBrew. All rights reserved.</p>
                    <p class="mt-2 sm:mt-0">Kasir Panel v1.0</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
