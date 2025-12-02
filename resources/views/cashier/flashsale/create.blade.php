@extends('layouts.cashierLayout')

@section('title', 'Buat Flash Sale')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Trigger Flash Sale</h1>
            <p class="text-gray-600">Buat promo flash sale untuk menarik pelanggan</p>
        </div>
        <a href="{{ route('cashier.flashsale.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- AI Status Banner -->
    @if(!$isAiAvailable)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800">🔧 AI Copywriting Tidak Tersedia</h3>
                <p class="text-sm text-amber-700 mt-1">
                    Fitur generate promo copy otomatis dengan AI sedang dalam pengembangan. 
                    Silakan input nama promo secara manual.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('cashier.flashsale.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @csrf
                
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
                    <h2 class="font-semibold text-brew-dark flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Detail Flash Sale
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Promo Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Promo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               value="{{ old('name') }}"
                               placeholder="Contoh: Happy Hour Sore, Promo Hujan, dll..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <p class="mt-1 text-xs text-gray-500">Nama yang menarik untuk promo Anda</p>
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Persentase Diskon <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="discount_percentage" required
                                   value="{{ old('discount_percentage', 15) }}"
                                   min="5" max="50"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Diskon antara 5% - 50%</p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi Promo <span class="text-red-500">*</span>
                        </label>
                        <select name="duration_hours" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                            <option value="1" {{ old('duration_hours') == 1 ? 'selected' : '' }}>1 Jam</option>
                            <option value="2" {{ old('duration_hours', 2) == 2 ? 'selected' : '' }}>2 Jam</option>
                            <option value="3" {{ old('duration_hours') == 3 ? 'selected' : '' }}>3 Jam</option>
                            <option value="4" {{ old('duration_hours') == 4 ? 'selected' : '' }}>4 Jam</option>
                            <option value="6" {{ old('duration_hours') == 6 ? 'selected' : '' }}>6 Jam</option>
                            <option value="12" {{ old('duration_hours') == 12 ? 'selected' : '' }}>12 Jam</option>
                            <option value="24" {{ old('duration_hours') == 24 ? 'selected' : '' }}>24 Jam</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Flash sale akan otomatis berakhir setelah durasi habis</p>
                    </div>

                    <!-- Trigger Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Trigger (Opsional)</label>
                        <textarea name="trigger_reason" rows="2"
                                  placeholder="Contoh: Jam sepi sore, hujan deras, dll..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">{{ old('trigger_reason') }}</textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <a href="{{ route('cashier.flashsale.index') }}"
                       class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Aktifkan Flash Sale
                    </button>
                </div>
            </form>
        </div>

        <!-- Suggestions Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Tips Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-semibold text-brew-dark mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tips Flash Sale
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <span>Gunakan saat jam sepi (14:00-16:00)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <span>Diskon 15-20% optimal untuk volume</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <span>Durasi 2 jam cukup untuk urgency</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <span>Share kode promo di sosial media</span>
                    </li>
                </ul>
            </div>

            <!-- Suggestions -->
            @if(count($suggestions) > 0)
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <h3 class="font-semibold text-blue-800 mb-3">💡 Rekomendasi</h3>
                @foreach($suggestions as $suggestion)
                <div class="bg-white p-3 rounded-lg mb-2 last:mb-0">
                    <p class="text-sm text-blue-700 font-medium">{{ $suggestion['message'] }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        → {{ $suggestion['suggested_discount'] }}% / {{ $suggestion['suggested_duration'] }} jam
                    </p>
                </div>
                @endforeach
            </div>
            @endif

            <!-- AI Feature Preview -->
            <div class="bg-gray-100 rounded-xl p-4 border-2 border-dashed border-gray-300">
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-600 mb-1">AI Copywriting</h4>
                    <p class="text-xs text-gray-500 mb-3">
                        Coming soon! AI akan otomatis generate copy promo yang menarik
                    </p>
                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-600 text-xs rounded-full">
                        🔧 Under Development
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
