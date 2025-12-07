@extends('layouts.adminLayout')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.menu.index') }}"
                class="flex items-center text-brew-brown hover:text-brew-dark transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Menu
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.menu.update', $menu) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Current Image --}}
                @if ($menu->image_path)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                        <img src="{{ asset('storage/' . $menu->image_path) }}" alt="{{ $menu->name }}"
                            class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                    </div>
                @endif

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Menu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('category') border-red-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('category', $menu->category) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" id="price" value="{{ old('price', $menu->price) }}" required
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('description') border-red-500 @enderror">{{ old('description', $menu->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $menu->image_path ? 'Ganti Gambar' : 'Upload Gambar' }}
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maksimal 2MB. {{ $menu->image_path ? 'Biarkan kosong jika tidak ingin mengganti.' : '' }}</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stock & Availability --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                            value="{{ old('stock_quantity', $menu->stock_quantity) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                    </div>

                    <div class="flex items-center pt-8">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1"
                                {{ old('is_available', $menu->is_available) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brew-gold/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brew-gold">
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>

                {{-- Flavor Profile Section (untuk AI) --}}
                @php
                    $flavorProfile = $menu->flavor_profile ?? [];
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
                            value="{{ old('flavor_notes', is_array($flavorProfile['notes'] ?? null) ? implode(', ', $flavorProfile['notes']) : '') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all text-sm"
                            placeholder="caramel, vanilla, chocolate">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.menu.index') }}"
                        class="px-6 py-2 text-gray-600 hover:text-brew-dark transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
