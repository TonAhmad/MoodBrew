@extends('layouts.cashierLayout')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')

@section('content')
    <div class="space-y-6">
        {{-- Header dengan Stats & Action --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-brew-dark">Daftar Menu</h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $stats['availableItems'] }} tersedia dari {{ $stats['totalItems'] }} menu
                </p>
            </div>
            <a href="{{ route('cashier.menu.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-brew-brown hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Menu
            </a>
        </div>

        {{-- Category Stats (Scrollable on Mobile) --}}
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex-shrink-0 bg-white rounded-lg shadow-sm px-4 py-3 min-w-[120px]">
                <p class="text-xs text-gray-500">Coffee</p>
                <p class="text-lg font-bold text-brew-brown">{{ $stats['coffeeItems'] }}</p>
            </div>
            <div class="flex-shrink-0 bg-white rounded-lg shadow-sm px-4 py-3 min-w-[120px]">
                <p class="text-xs text-gray-500">Non-Coffee</p>
                <p class="text-lg font-bold text-blue-600">{{ $stats['nonCoffeeItems'] }}</p>
            </div>
            <div class="flex-shrink-0 bg-white rounded-lg shadow-sm px-4 py-3 min-w-[120px]">
                <p class="text-xs text-gray-500">Pastry</p>
                <p class="text-lg font-bold text-amber-600">{{ $stats['pastryItems'] }}</p>
            </div>
            <div class="flex-shrink-0 bg-white rounded-lg shadow-sm px-4 py-3 min-w-[120px]">
                <p class="text-xs text-gray-500">Main Course</p>
                <p class="text-lg font-bold text-green-600">{{ $stats['mainCourseItems'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('cashier.menu.index') }}"
                class="space-y-3 sm:space-y-0 sm:flex sm:items-center sm:gap-3">
                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari menu..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm">
                </div>

                {{-- Category Filter --}}
                <div class="sm:w-40">
                    <select name="category"
                        class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 sm:flex-none px-4 py-2.5 bg-brew-brown text-white rounded-lg hover:bg-brew-dark transition-colors text-sm">
                        Filter
                    </button>
                    @if ($search || $category)
                        <a href="{{ route('cashier.menu.index') }}"
                            class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-center text-sm">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Menu Grid --}}
        @if ($menuItems->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada menu</h3>
                <p class="text-gray-500 mb-4">Tambahkan menu pertama untuk memulai.</p>
                <a href="{{ route('cashier.menu.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-brew-gold text-brew-dark font-medium rounded-lg hover:bg-brew-brown hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Menu
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($menuItems as $item)
                    <div
                        class="bg-white rounded-xl shadow-sm overflow-hidden group hover:shadow-md transition-shadow {{ !$item->is_available ? 'opacity-60' : '' }}">
                        {{-- Menu Image Placeholder --}}
                        <div
                            class="h-32 bg-gradient-to-br from-brew-cream to-brew-light flex items-center justify-center relative">
                            <span class="text-4xl">
                                @switch($item->category)
                                    @case('coffee')
                                        ☕
                                    @break

                                    @case('non-coffee')
                                        🧋
                                    @break

                                    @case('pastry')
                                        🥐
                                    @break

                                    @case('main_course')
                                        🍽️
                                    @break

                                    @default
                                        🍴
                                @endswitch
                            </span>

                            {{-- Category Badge --}}
                            <span
                                class="absolute top-2 left-2 px-2 py-1 text-xs font-medium rounded-full
                        @switch($item->category)
                            @case('coffee') bg-amber-100 text-amber-800 @break
                            @case('non-coffee') bg-blue-100 text-blue-800 @break
                            @case('pastry') bg-orange-100 text-orange-800 @break
                            @case('main_course') bg-green-100 text-green-800 @break
                        @endswitch
                    ">
                                {{ $categories[$item->category] ?? $item->category }}
                            </span>

                            {{-- Availability Badge --}}
                            @if (!$item->is_available)
                                <span
                                    class="absolute top-2 right-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    Habis
                                </span>
                            @endif
                        </div>

                        {{-- Menu Info --}}
                        <div class="p-4">
                            <h4 class="font-semibold text-brew-dark truncate">{{ $item->name }}</h4>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2 h-10">
                                {{ $item->description ?? 'Tidak ada deskripsi' }}
                            </p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-lg font-bold text-brew-gold">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    Stok: {{ $item->stock_quantity }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                {{-- Toggle Availability --}}
                                <form method="POST" action="{{ route('cashier.menu.toggle', $item->id) }}" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full py-2 text-xs font-medium rounded-lg transition-colors
                                    {{ $item->is_available
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $item->is_available ? '✓ Tersedia' : '✗ Tidak Tersedia' }}
                                    </button>
                                </form>

                                {{-- Edit --}}
                                <a href="{{ route('cashier.menu.edit', $item->id) }}"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('cashier.menu.destroy', $item->id) }}"
                                    onsubmit="return confirm('Yakin hapus menu {{ $item->name }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($menuItems->hasPages())
                <div class="mt-6">
                    {{ $menuItems->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
