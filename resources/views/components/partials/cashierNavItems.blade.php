{{--
    Cashier Navigation Items Partial
    Digunakan oleh desktop dan mobile sidebar
    
    Variables:
    - $showLabels (optional): Force show labels, digunakan di mobile
--}}

@php
    $forceShowLabels = $showLabels ?? false;
    $pendingCount = \App\Models\Order::pendingPayment()->count();
    $preparingCount = \App\Models\Order::where('status', 'preparing')->count();
    $needsAttentionCount = \App\Models\CustomerInteraction::whereIn('sentiment_type', ['negative', 'angry', 'frustrated'])
        ->whereNull('handled_by')->count();
@endphp

{{-- Dashboard --}}
<a href="{{ route('cashier.dashboard') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.dashboard') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
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

{{-- Pending Orders --}}
<a href="{{ route('cashier.orders.pending') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.orders.pending') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Pending Payment</span>
        @if ($pendingCount > 0)
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
        @endif
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Pending Payment</span>
        @if ($pendingCount > 0)
            <span x-show="sidebarOpen" x-cloak
                class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
        @endif
    @endif
</a>

{{-- In Progress --}}
<a href="{{ route('cashier.orders.preparing') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.orders.preparing') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">In Progress</span>
        @if ($preparingCount > 0)
            <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $preparingCount }}</span>
        @endif
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">In Progress</span>
        @if ($preparingCount > 0)
            <span x-show="sidebarOpen" x-cloak
                class="ml-auto bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $preparingCount }}</span>
        @endif
    @endif
</a>

{{-- Completed Today --}}
<a href="{{ route('cashier.orders.completed') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.orders.completed') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Completed</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Completed</span>
    @endif
</a>

{{-- Section: Quick Actions --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Quick Actions</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Quick Actions</p>
    </div>
@endif

{{-- New Order (Manual) --}}
<a href="{{ route('cashier.orders.create') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.orders.create') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">New Order</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">New Order</span>
    @endif
</a>

{{-- Menu Management --}}
<a href="{{ route('cashier.menu.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.menu.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Kelola Menu</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Kelola Menu</span>
    @endif
</a>

{{-- Section: Promo --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Promo</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Promo</p>
    </div>
@endif

{{-- Flash Sale --}}
<a href="{{ route('cashier.flashsale.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.flashsale.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Trigger Flash Sale</span>
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Trigger Flash Sale</span>
    @endif
</a>

{{-- Section: Empathy --}}
@if ($forceShowLabels)
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Customer Mood</p>
    </div>
@else
    <div x-show="sidebarOpen" x-cloak class="pt-4">
        <p class="px-3 text-xs font-semibold text-brew-cream/50 uppercase tracking-wider">Customer Mood</p>
    </div>
@endif

{{-- Empathy Radar --}}
<a href="{{ route('cashier.empathy.index') }}"
    class="flex items-center px-3 py-2.5 rounded-lg transition-colors
          {{ request()->routeIs('cashier.empathy.*') ? 'bg-brew-gold text-brew-dark' : 'text-brew-cream hover:bg-brew-brown/50' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    @if ($forceShowLabels)
        <span class="ml-3">Empathy Radar</span>
        @if ($needsAttentionCount > 0)
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $needsAttentionCount }}</span>
        @endif
    @else
        <span x-show="sidebarOpen" x-cloak class="ml-3">Empathy Radar</span>
        @if ($needsAttentionCount > 0)
            <span x-show="sidebarOpen" x-cloak
                class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $needsAttentionCount }}</span>
        @endif
    @endif
</a>
