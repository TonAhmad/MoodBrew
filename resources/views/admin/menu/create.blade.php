@extends('layouts.adminLayout')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu Baru')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('admin.menu.index') }}"
               class="flex items-center text-brew-brown hover:text-brew-dark transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Menu
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-brew-dark">Tambah Menu Baru</h2>
                <p class="text-gray-500 text-sm mt-1">Isi data untuk menambahkan menu baru</p>
            </div>

            <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data"
                  class="p-6 space-y-5">
                @csrf

                {{-- Basic Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-200
                                      focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                      transition-all @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Cappuccino">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
<div>
    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
        Kategori <span class="text-red-500">*</span>
    </label>
    <select name="category" id="category" required
        class="w-full px-4 py-3 rounded-lg border border-gray-200
               focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
               transition-all @error('category') border-red-500 @enderror">
        <option value="">Pilih Kategori</option>
        @foreach ($categories as $key => $label)
            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
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
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="price" id="price" required min="0" step="500"
                                   value="{{ old('price') }}"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-200
                                          focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                          transition-all @error('price') border-red-500 @enderror"
                                   placeholder="25000">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-200
                                     focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                     transition-all resize-none @error('description') border-red-500 @enderror"
                              placeholder="Deskripsi singkat tentang menu ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Menu
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200
                                  focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                  transition-all @error('image') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-400">
                        Format: JPG, PNG. Maks 2MB.
                    </p>
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
                               value="{{ old('stock_quantity', 100) }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-200
                                      focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                      transition-all"
                               placeholder="100">
                    </div>

                    <div class="flex items-center pt-8">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1"
                                   {{ old('is_available', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4
                                       peer-focus:ring-brew-gold/20 rounded-full peer
                                       peer-checked:after:translate-x-full peer-checked:after:border-white
                                       after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                       after:bg-white after:border-gray-300 after:border after:rounded-full
                                       after:h-5 after:w-5 after:transition-all peer-checked:bg-brew-gold">
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>

                {{-- Flavor Profile Section (untuk AI) --}}
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
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200
                                           focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20
                                           outline-none transition-all text-sm">
                                <option value="low" {{ old('sweetness') === 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('sweetness', 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang
                                </option>
                                <option value="high" {{ old('sweetness') === 'high' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                        </div>

                        {{-- Bitterness --}}
                        <div>
                            <label for="bitterness" class="block text-xs font-medium text-gray-500 mb-2">
                                Tingkat Pahit
                            </label>
                            <select id="bitterness" name="bitterness"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200
                                           focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20
                                           outline-none transition-all text-sm">
                                <option value="low" {{ old('bitterness') === 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('bitterness', 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang
                                </option>
                                <option value="high" {{ old('bitterness') === 'high' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                        </div>

                        {{-- Strength --}}
                        <div>
                            <label for="strength" class="block text-xs font-medium text-gray-500 mb-2">
                                Kekuatan Rasa
                            </label>
                            <select id="strength" name="strength"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200
                                           focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20
                                           outline-none transition-all text-sm">
                                <option value="light" {{ old('strength') === 'light' ? 'selected' : '' }}>Ringan</option>
                                <option value="medium" {{ old('strength', 'medium') === 'medium' ? 'selected' : '' }}>
                                    Sedang
                                </option>
                                <option value="strong" {{ old('strength') === 'strong' ? 'selected' : '' }}>Kuat</option>
                            </select>
                        </div>
                    </div>

                    {{-- Flavor Notes --}}
                    <div class="mt-4">
                        <label for="flavor_notes" class="block text-xs font-medium text-gray-500 mb-2">
                            Catatan Rasa <span class="text-gray-400">(pisahkan dengan koma)</span>
                        </label>
                        <input type="text" id="flavor_notes" name="flavor_notes"
                               value="{{ old('flavor_notes') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200
                                      focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none
                                      transition-all text-sm"
                               placeholder="caramel, vanilla, chocolate">
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.menu.index') }}"
                       class="flex-1 py-3 px-4 bg-gray-100 text-gray-700 font-medium rounded-lg
                              hover:bg-gray-200 transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-brew-gold text-brew-dark font-semibold rounded-lg
                                   hover:bg-brew-brown hover:text-white transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
