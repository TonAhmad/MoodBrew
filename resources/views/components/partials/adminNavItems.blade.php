{{--
    Admin Navigation Items Partial
    Digunakan oleh desktop dan mobile sidebar
    
    Variables:
    - $showLabels (optional): Force show labels, digunakan di mobile
--}}

@php
    $forceShowLabels = $showLabels ?? false;
@endphp

{{-- Dashboard --}}
<a href="{{ route('admin.dashboard') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.dashboard') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Dashboard</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Dashboard</span>
    @endif
</a>

{{-- Section: Menu --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Menu</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Menu</p>
    </div>
@endif

{{-- Menu Items --}}
<a href="{{ route('admin.menu.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.menu.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Menu Items</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Menu Items</span>
    @endif
</a>

{{-- Section: Orders --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Orders</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Orders</p>
    </div>
@endif

{{-- All Orders --}}
<a href="{{ route('admin.orders.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.orders.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">All Orders</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">All Orders</span>
    @endif
</a>

{{-- Reports --}}
<a href="{{ route('admin.reports.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.reports.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Laporan</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Laporan</span>
    @endif
</a>

{{-- Section: Users --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Users</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Users</p>
    </div>
@endif

{{-- Staff Management --}}
<a href="{{ route('admin.staff.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.staff.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Staff</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Staff</span>
    @endif
</a>

{{-- Section: AI --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">AI Features</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">AI Features</p>
    </div>
@endif

{{-- Vibe Wall --}}
<a href="{{ route('admin.vibewall.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.vibewall.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Vibe Wall</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Vibe Wall</span>
    @endif
</a>

{{-- Mood Analytics --}}
<a href="{{ route('admin.analytics.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('admin.analytics.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Analytics</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Analytics</span>
    @endif
</a>

{{-- Section: System --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">System</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">System</p>
    </div>
@endif

{{-- Logout --}}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        class="w-full flex items-center px-3 py-2.5 rounded-lg text-brew-cream hover:bg-red-500/20 hover:text-red-300 transition-colors">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        @if ($forceShowLabels)
            <span class="ml-3">Logout</span>
        @else
            <span x-show="sidebarOpen" x-cloak class="ml-3">Logout</span>
        @endif
    </button>
</form>
