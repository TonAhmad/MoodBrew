@extends('layouts.adminLayout')

@section('title', 'Menu Management')
@section('page-title', 'Menu Management')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-gray-600">Kelola semua menu item cafe</p>
            </div>
            <a href="{{ route('admin.menu.create') }}"
                class="px-6 py-2.5 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Menu
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('admin.menu.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama menu..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="availability"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="">Semua</option>
                        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Tersedia
                        </option>
                        <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Tidak
                            Tersedia</option>
                    </select>
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-brew-brown text-white font-semibold rounded-lg hover:bg-brew-dark transition-colors">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'category', 'availability']))
                    <a href="{{ route('admin.menu.index') }}" class="px-6 py-2 text-gray-600 hover:text-brew-dark">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Menu List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if ($menuItems->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Menu</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Kategori</th>
                                <th class="text-right py-4 px-6 text-sm font-semibold text-gray-600">Harga</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-600">Status</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-600">Terjual</th>
                                <th class="text-right py-4 px-6 text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($menuItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">
                                            @if ($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                                    alt="{{ $item->name }}"
                                                    class="w-12 h-12 object-cover rounded-lg">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-brew-cream rounded-lg flex items-center justify-center">
                                                    <span class="text-brew-brown text-lg">☕</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-brew-dark">{{ $item->name }}</p>
                                                @if ($item->description)
                                                    <p class="text-sm text-gray-500 line-clamp-1">
                                                        {{ Str::limit($item->description, 40) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-1 bg-brew-cream text-brew-brown text-sm rounded">
                                            {{ $item->category }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-medium text-brew-dark">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.menu.toggle', $item) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1 rounded-full text-sm font-medium transition-colors {{ $item->is_available ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                                {{ $item->is_available ? 'Tersedia' : 'Habis' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-6 text-center text-gray-600">
                                        {{ $item->order_items_count ?? 0 }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.menu.edit', $item) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.menu.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('Hapus menu {{ $item->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-200">
                    {{ $menuItems->links() }}
                </div>
            @else
                <div class="text-center py-16 text-gray-500">
                    <span class="text-5xl block mb-4">☕</span>
                    <p class="text-lg">Belum ada menu item</p>
                    <a href="{{ route('admin.menu.create') }}"
                        class="inline-block mt-4 px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                        Tambah Menu Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
