@extends('layouts.cashierLayout')

@section('title', 'Catat Interaksi Pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Catat Interaksi</h1>
            <p class="text-gray-600">Rekam feedback atau keluhan pelanggan</p>
        </div>
        <a href="{{ route('cashier.empathy.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

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
            <form action="{{ route('cashier.empathy.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @csrf
                
                <div class="p-4 border-b border-gray-100 bg-brew-cream/30">
                    <h2 class="font-semibold text-brew-dark flex items-center">
                        <svg class="w-5 h-5 mr-2 text-brew-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Detail Interaksi
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Interaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Interaksi <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="interaction_type" value="complaint" class="peer sr-only" required {{ old('interaction_type') == 'complaint' ? 'checked' : '' }}>
                                <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                    <span class="text-2xl block mb-1">😤</span>
                                    <span class="text-sm font-medium text-gray-700">Keluhan</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="interaction_type" value="feedback" class="peer sr-only" {{ old('interaction_type') == 'feedback' ? 'checked' : '' }}>
                                <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                    <span class="text-2xl block mb-1">💭</span>
                                    <span class="text-sm font-medium text-gray-700">Feedback</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="interaction_type" value="praise" class="peer sr-only" {{ old('interaction_type') == 'praise' ? 'checked' : '' }}>
                                <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                    <span class="text-2xl block mb-1">🥰</span>
                                    <span class="text-sm font-medium text-gray-700">Pujian</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="interaction_type" value="question" class="peer sr-only" {{ old('interaction_type') == 'question' ? 'checked' : '' }}>
                                <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <span class="text-2xl block mb-1">❓</span>
                                    <span class="text-sm font-medium text-gray-700">Pertanyaan</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Channel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sumber Interaksi <span class="text-red-500">*</span>
                        </label>
                        <select name="channel" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                            <option value="in-store" {{ old('channel') == 'in-store' ? 'selected' : '' }}>🏪 Di Toko (Langsung)</option>
                            <option value="online" {{ old('channel') == 'online' ? 'selected' : '' }}>💬 Online (Chat/DM)</option>
                            <option value="phone" {{ old('channel') == 'phone' ? 'selected' : '' }}>📞 Telepon</option>
                        </select>
                    </div>

                    <!-- Customer Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pesan/Keluhan Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="customer_message" required rows="4"
                                  placeholder="Tulis detail keluhan, feedback, atau pujian dari pelanggan..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">{{ old('customer_message') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Jelaskan sedetail mungkin untuk analisis yang akurat</p>
                    </div>

                    <!-- Staff Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Staff (Opsional)</label>
                        <textarea name="staff_notes" rows="2"
                                  placeholder="Tindakan yang sudah dilakukan, solusi yang diberikan..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">{{ old('staff_notes') }}</textarea>
                    </div>

                    <!-- Resolved Checkbox -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_resolved" value="1" id="is_resolved"
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                               {{ old('is_resolved') ? 'checked' : '' }}>
                        <label for="is_resolved" class="text-sm text-gray-700">Sudah ditangani/resolved</label>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <a href="{{ route('cashier.empathy.index') }}"
                       class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                        Simpan Interaksi
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Sentiment Classification Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-semibold text-brew-dark mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Klasifikasi Otomatis
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 p-2 bg-red-50 rounded">
                        <span>😤</span>
                        <span class="text-red-700">Keluhan → Sentimen Negatif</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-yellow-50 rounded">
                        <span>💭</span>
                        <span class="text-yellow-700">Feedback → Sentimen Netral</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-green-50 rounded">
                        <span>🥰</span>
                        <span class="text-green-700">Pujian → Sentimen Positif</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-blue-50 rounded">
                        <span>❓</span>
                        <span class="text-blue-700">Pertanyaan → Sentimen Netral</span>
                    </div>
                </div>
            </div>

            <!-- AI Feature Status -->
            @if($isAiAvailable)
            <div class="bg-purple-50 rounded-xl p-4 border-2 border-purple-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h4 class="font-medium text-purple-800 mb-1">✨ AI Sentiment Analysis Active</h4>
                    <p class="text-xs text-purple-700 mb-2">
                        AI akan otomatis menganalisis sentimen customer & memberikan saran respons
                    </p>
                    <div class="space-y-1 text-xs text-purple-600">
                        <p>✓ Auto sentiment detection</p>
                        <p>✓ Emotion analysis</p>
                        <p>✓ Suggested responses</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full mt-3">
                        ✓ Ready
                    </span>
                </div>
            </div>
            @else
            <div class="bg-gray-100 rounded-xl p-4 border-2 border-dashed border-gray-300">
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-600 mb-1">AI Sentiment Analysis</h4>
                    <p class="text-xs text-gray-500 mb-3">
                        Sentimen akan diklasifikasi berdasarkan tipe interaksi. Configure AI_API_KEY untuk analisis otomatis.
                    </p>
                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-600 text-xs rounded-full">
                        ⚙️ Not Configured
                    </span>
                </div>
            </div>
            @endif

            <!-- Tips -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <h3 class="font-semibold text-blue-800 mb-3">💡 Tips Menangani</h3>
                <ul class="space-y-2 text-sm text-blue-700">
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Dengarkan dengan empati</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Catat detail lengkap</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Tawarkan solusi konkret</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Follow up jika perlu</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
