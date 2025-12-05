{{-- 
    Navbar Component - MoodBrew Landing Page
    Responsive navigation with mobile menu support
--}}
<nav x-data="{ mobileMenuOpen: false }" class="bg-white/95 backdrop-blur-sm shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('landing.home') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-brew-brown rounded-full flex items-center justify-center">
                        <span class="text-brew-cream font-display font-bold text-lg">M</span>
                    </div>
                    <span class="font-display text-xl font-bold text-brew-dark">MoodBrew</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('landing.home') }}"
                    class="text-brew-brown hover:text-brew-gold transition-colors font-medium">
                    Home
                </a>
                <a href="{{ route('landing.menu') }}" class="text-gray-600 hover:text-brew-gold transition-colors">
                    Menu
                </a>
                <a href="{{ route('landing.vibewall') }}" class="text-gray-600 hover:text-brew-gold transition-colors">
                    Vibe Wall
                </a>
                <a href="#features" class="text-gray-600 hover:text-brew-gold transition-colors">
                    Fitur
                </a>
            </div>

            {{-- Auth Buttons (Desktop) --}}
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('staff.login') }}"
                    class="text-gray-500 hover:text-brew-gold transition-colors text-sm">
                    Staff
                </a>
                <a href="{{ route('login') }}"
                    class="bg-brew-brown text-white px-6 py-2.5 rounded-full hover:bg-brew-dark transition-colors font-medium shadow-md">
                    Mulai Pesan
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="text-brew-brown p-2 rounded-lg hover:bg-brew-cream transition-colors"
                    aria-label="Toggle menu">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('landing.home') }}"
                class="block px-3 py-2 text-brew-brown font-medium rounded-lg hover:bg-brew-cream transition-colors">
                Home
            </a>
            <a href="{{ route('landing.menu') }}" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-brew-cream transition-colors">
                Menu
            </a>
            <a href="{{ route('landing.vibewall') }}" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-brew-cream transition-colors">
                Vibe Wall
            </a>
            <a href="#features" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-brew-cream transition-colors">
                Fitur
            </a>

            <div class="pt-3 border-t border-gray-100 space-y-2">
                <a href="{{ route('staff.login') }}"
                    class="block px-3 py-2 text-center text-gray-500 text-sm rounded-lg hover:bg-brew-cream transition-colors">
                    Staff Login
                </a>
                <a href="{{ route('login') }}"
                    class="block px-3 py-2 text-center bg-brew-brown text-white font-medium rounded-full hover:bg-brew-dark transition-colors">
                    Mulai Pesan
                </a>
            </div>
        </div>
    </div>
</nav>
