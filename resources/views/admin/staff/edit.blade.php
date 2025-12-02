@extends('layouts.adminLayout')

@section('title', 'Edit Kasir')
@section('page-title', 'Edit Kasir')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('admin.staff.index') }}" class="text-gray-500 hover:text-brew-brown">
                        Kelola Staff
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="ml-1 text-gray-700 font-medium">Edit Kasir</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-brew-gold rounded-full flex items-center justify-center">
                        <span class="text-brew-dark font-bold text-xl">{{ strtoupper(substr($staff->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-brew-dark">{{ $staff->name }}</h2>
                        <p class="text-gray-500 text-sm">Terdaftar {{ $staff->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $staff->name) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $staff->email) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('email') border-red-500 @enderror"
                        placeholder="kasir@moodbrew.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Divider --}}
                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Ganti Password (Opsional)</span>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Baru
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all @error('password') border-red-500 @enderror"
                        placeholder="Kosongkan jika tidak ingin mengganti">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all"
                        placeholder="Ulangi password baru">
                </div>

                {{-- Info Box --}}
                <div class="p-4 bg-amber-50 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="text-sm text-amber-700">
                            <p class="font-medium">Perhatian:</p>
                            <p>Jika Anda mengganti password, kasir harus menggunakan password baru untuk login selanjutnya.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 py-3 px-4 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-brew-brown hover:text-white transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.staff.index') }}"
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
                <p class="text-gray-500 text-sm mb-4">Tindakan ini tidak dapat dibatalkan. Hati-hati!</p>
                <form method="POST" action="{{ route('admin.staff.destroy', $staff->id) }}"
                    onsubmit="return confirm('Yakin ingin menghapus kasir {{ $staff->name }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Hapus Akun Kasir
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
