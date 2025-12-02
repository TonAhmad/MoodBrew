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
                @if ($menu->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                            class="w-32 h-32 object-cover rounded-lg">
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
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"
                                {{ old('category', $menu->category) === $category ? 'selected' : '' }}>
                                {{ $category }}
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
                        Ganti Gambar
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold @error('image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti gambar</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Flavor Profile --}}
                <div>
                    <label for="flavor_profile" class="block text-sm font-medium text-gray-700 mb-1">
                        Flavor Profile (untuk AI)
                    </label>
                    <input type="text" name="flavor_profile" id="flavor_profile"
                        value="{{ old('flavor_profile', $menu->flavor_profile) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold"
                        placeholder="Contoh: rich, creamy, chocolate notes">
                </div>

                {{-- Mood Tags --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mood Tags (untuk AI)
                    </label>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $moods = ['energetic', 'relaxed', 'happy', 'focused', 'cozy'];
                            $currentMoods = old('mood_tags', $menu->mood_tags ?? []);
                            if (is_string($currentMoods)) {
                                $currentMoods = json_decode($currentMoods, true) ?? [];
                            }
                        @endphp
                        @foreach ($moods as $mood)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="mood_tags[]" value="{{ $mood }}"
                                    {{ in_array($mood, $currentMoods) ? 'checked' : '' }}
                                    class="w-4 h-4 text-brew-gold border-gray-300 rounded focus:ring-brew-gold">
                                <span class="text-sm text-gray-700 capitalize">{{ $mood }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Availability --}}
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_available" id="is_available" value="1"
                        {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                        class="w-4 h-4 text-brew-gold border-gray-300 rounded focus:ring-brew-gold">
                    <label for="is_available" class="text-sm text-gray-700">Tersedia untuk dijual</label>
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
