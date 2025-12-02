{{--
    Admin Sidebar Component
    Navigation menu untuk admin dashboard
    - Desktop: Collapsible sidebar (tetap terlihat)
    - Mobile: Drawer/overlay yang bisa ditutup
--}}

{{-- Desktop Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 bg-brew-dark text-white transition-all duration-300 hidden lg:flex lg:flex-col"
    :class="sidebarOpen ? 'w-64' : 'w-20'">
    <div class="flex flex-col h-full">
        {{-- Logo --}}
        <div class="flex items-center justify-center h-16 border-b border-brew-brown/30">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-brew-dark font-display font-bold text-lg">M</span>
                </div>
                <span x-show="sidebarOpen" x-cloak class="font-display text-xl font-bold text-brew-cream">
                    MoodBrew
                </span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @include('components.partials.adminNavItems')
        </nav>

        {{-- User Info --}}
        <div class="border-t border-brew-brown/30 p-4">
            <div class="flex items-center" :class="sidebarOpen ? '' : 'justify-center'">
                <div class="w-8 h-8 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                    <span
                        class="text-brew-dark font-bold text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div x-show="sidebarOpen" x-cloak class="ml-3">
                    <p class="text-sm font-medium text-brew-cream">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-brew-cream/60">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Sidebar (Drawer) --}}
<aside x-show="mobileMenuOpen" x-cloak class="fixed inset-y-0 left-0 z-50 w-72 bg-brew-dark text-white lg:hidden"
    x-transition:enter="transform transition-transform duration-300 ease-in-out"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition-transform duration-300 ease-in-out"
    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
    <div class="flex flex-col h-full">
        {{-- Logo & Close Button --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-brew-brown/30">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-brew-dark font-display font-bold text-lg">M</span>
                </div>
                <span class="font-display text-xl font-bold text-brew-cream">MoodBrew</span>
            </a>
            <button @click="mobileMenuOpen = false" class="p-2 text-brew-cream hover:bg-brew-brown/50 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" @click="mobileMenuOpen = false">
            @include('components.partials.adminNavItems', ['showLabels' => true])
        </nav>

        {{-- User Info --}}
        <div class="border-t border-brew-brown/30 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-brew-dark font-bold">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-brew-cream">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-brew-cream/60">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>
