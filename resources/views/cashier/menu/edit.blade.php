@extends('layouts.cashierLayout')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                <li>
                    <a href="{{ route('cashier.menu.index') }}" class="text-gray-500 hover:text-brew-brown">
                        Kelola Menu
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="ml-1 text-gray-700 font-medium">Edit Menu</span>
                    </div>
                </li>
            </ol>
        </nav>
            
        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-brew-cream to-brew-light rounded-xl flex items-center justify-center">
                        <span class="text-2xl">
                            @switch($menuItem->category)
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
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-brew-dark">{{ $menuItem->name }}</h2>
                        <p class="text-gray-500 text-sm">{{ $categories[$menuItem->category] ?? $menuItem->category }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('cashier.menu.update', $menuItem->id) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Basic Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            value="{{ old('name', $menuItem->name) }}"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('category') border-red-500 @enderror">
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('category', $menuItem->category) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" id="price" name="price" required min="0" step="500"
                                value="{{ old('price', $menuItem->price) }}"
                                class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all resize-none @error('description') border-red-500 @enderror">{{ old('description', $menuItem->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Image --}}
                <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2"> Gambar Menu </label>
                <input type="file"id="image" name="image" accept="image/*" class="w-full px-4 py-3 rounded-lg border border-gray-200
                 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20
                outline-none transition-all @error('image') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-400">
                Format: JPG, PNG. Maks 2MB. </p>
                @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                </div>
                {{-- Stock & Availability --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                            value="{{ old('stock_quantity', $menuItem->stock_quantity) }}"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all">
                    </div>

                    <div class="flex items-center pt-8">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1"
                                {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brew-gold/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brew-gold">
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>

                {{-- Flavor Profile Section (untuk AI) --}}
                @php
                    $flavorProfile = $menuItem->flavor_profile ?? [];
                @endphp
                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">
                        Profil Rasa <span class="font-normal text-gray-400">(Untuk AI Recommendation)</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Sweetness --}}
                        <div>
                            <label for="sweetness" class="block text-xs font-medium text-gray-500 mb-2">
                                Tingkat Manis
                            </label>
                            <select id="sweetness" name="sweetness"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm">
                                <option value="low"
                                    {{ old('sweetness', $flavorProfile['sweetness'] ?? '') === 'low' ? 'selected' : '' }}>
                                    Rendah</option>
                                <option value="medium"
                                    {{ old('sweetness', $flavorProfile['sweetness'] ?? 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang</option>
                                <option value="high"
                                    {{ old('sweetness', $flavorProfile['sweetness'] ?? '') === 'high' ? 'selected' : '' }}>
                                    Tinggi</option>
                            </select>
                        </div>

                        {{-- Bitterness --}}
                        <div>
                            <label for="bitterness" class="block text-xs font-medium text-gray-500 mb-2">
                                Tingkat Pahit
                            </label>
                            <select id="bitterness" name="bitterness"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm">
                                <option value="low"
                                    {{ old('bitterness', $flavorProfile['bitterness'] ?? '') === 'low' ? 'selected' : '' }}>
                                    Rendah</option>
                                <option value="medium"
                                    {{ old('bitterness', $flavorProfile['bitterness'] ?? 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang</option>
                                <option value="high"
                                    {{ old('bitterness', $flavorProfile['bitterness'] ?? '') === 'high' ? 'selected' : '' }}>
                                    Tinggi</option>
                            </select>
                        </div>

                        {{-- Strength --}}
                        <div>
                            <label for="strength" class="block text-xs font-medium text-gray-500 mb-2">
                                Kekuatan Rasa
                            </label>
                            <select id="strength" name="strength"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm">
                                <option value="light"
                                    {{ old('strength', $flavorProfile['strength'] ?? '') === 'light' ? 'selected' : '' }}>
                                    Ringan</option>
                                <option value="medium"
                                    {{ old('strength', $flavorProfile['strength'] ?? 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang</option>
                                <option value="strong"
                                    {{ old('strength', $flavorProfile['strength'] ?? '') === 'strong' ? 'selected' : '' }}>
                                    Kuat</option>
                            </select>
                        </div>
                    </div>

                    {{-- Flavor Notes --}}
                    <div class="mt-4">
                        <label for="flavor_notes" class="block text-xs font-medium text-gray-500 mb-2">
                            Catatan Rasa <span class="text-gray-400">(pisahkan dengan koma)</span>
                        </label>
                        <input type="text" id="flavor_notes" name="flavor_notes"
                            value="{{ old('flavor_notes', implode(', ', $flavorProfile['notes'] ?? [])) }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm"
                            placeholder="caramel, vanilla, chocolate">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 py-3 px-4 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-brew-brown hover:text-white transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('cashier.menu.index') }}"
                        class="flex-1 py-3 px-4 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-red-200">
            <div class="p-6">
                <h3 class="text-lg font-medium text-red-600 mb-2">Zona Berbahaya</h3>
                <p class="text-gray-500 text-sm mb-4">Menu yang sudah pernah dipesan akan dinonaktifkan, bukan dihapus.</p>
                <form method="POST" action="{{ route('cashier.menu.destroy', $menuItem->id) }}"
                    onsubmit="return confirm('Yakin ingin menghapus menu {{ $menuItem->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Hapus Menu
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
