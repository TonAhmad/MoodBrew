@extends('layouts.landingLayout')

@section('title', 'MoodBrew - Cafe That Understands You')

@section('content')
    {{-- Hero Section --}}
    <section class="relative gradient-brew min-h-[90vh] flex items-center overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-32 h-32 border-2 border-brew-cream rounded-full"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 border-2 border-brew-cream rounded-full"></div>
            <div class="absolute top-1/2 left-1/3 w-24 h-24 border-2 border-brew-cream rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Hero Content --}}
                <div class="text-center lg:text-left fade-in-left">
                    <span
                        class="inline-block px-4 py-1 bg-brew-gold/20 text-brew-gold rounded-full text-sm font-medium mb-6">
                        ✨ AI-Powered Cafe Experience
                    </span>
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        Cafe yang
                        <span class="text-gradient">Memahami</span>
                        Perasaanmu
                    </h1>
                    <p class="text-brew-cream/80 text-lg md:text-xl mb-8 max-w-lg mx-auto lg:mx-0">
                        Ceritakan mood-mu hari ini, dan AI kami akan merekomendasikan minuman yang sempurna untukmu. Pesan
                        langsung dari meja, bayar di kasir.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-brew-gold text-brew-dark font-bold rounded-full hover:bg-yellow-400 transition-all transform hover:scale-105 shadow-lg">
                            <span>Mulai Pesan</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="#features"
                            class="inline-flex items-center justify-center px-8 py-4 border-2 border-brew-cream/30 text-brew-cream font-medium rounded-full hover:bg-brew-cream/10 transition-all">
                            Lihat Fitur
                        </a>
                    </div>
                </div>

                {{-- Hero Visual --}}
                <div class="hidden lg:flex justify-center fade-in-right">
                    <div class="relative">
                        {{-- Cafe Cup Illustration --}}
                        <div
                            class="w-80 h-80 bg-brew-cream/10 rounded-full flex items-center justify-center backdrop-blur-sm animate-float">
                            <div class="text-center">
                                <img src="{{ asset('assets/moodbrew.png') }}" alt="MoodBrew Logo" class="w-32 h-32 rounded-full mx-auto mb-4 shadow-2xl">
                                <p class="text-brew-cream font-display text-xl">Your Mood,<br>Your Cafe</p>
                            </div>
                        </div>
                        {{-- Floating Elements --}}
                        <div
                            class="absolute -top-4 -right-4 bg-brew-gold text-brew-dark px-4 py-2 rounded-full text-sm font-bold animate-bounce">
                            AI Powered
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm animate-pulse">
                            💬 "Feeling stressed..."
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="text-center mb-16 fade-in">
                <span class="text-brew-gold font-medium">Fitur Unggulan</span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-brew-dark mt-2 mb-4">
                    Pengalaman Ngopi yang Berbeda
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    MoodBrew menggabungkan teknologi AI dengan kehangatan cafe untuk memberikan pengalaman ngopi yang personal dan
                    memorable.
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1: AI Mood Recommendation --}}
                <div
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-100">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">🧠</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        AI Mood Detection
                    </h3>
                    <p class="text-gray-600">
                        Ceritakan perasaanmu, dan AI kami akan menganalisis mood-mu untuk merekomendasikan minuman yang
                        paling cocok.
                    </p>
                </div>

                {{-- Feature 2: QR Table Ordering --}}
                <div
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-200">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">📱</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Scan & Order
                    </h3>
                    <p class="text-gray-600">
                        Duduk di meja favoritmu, scan QR code, pilih menu atau minta rekomendasi AI, lalu bayar di kasir
                        saat siap.
                    </p>
                </div>

                {{-- Feature 3: Flash Sales --}}
                <a href="{{ route('landing.menu') }}"
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-300">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">⚡</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Flash Sales AI
                    </h3>
                    <p class="text-gray-600">
                        Promo dadakan dengan copywriting yang di-generate AI. Jangan lewatkan penawaran spesial di jam-jam
                        tertentu!
                    </p>
                </a>

                {{-- Feature 4: Silent Social Wall --}}
                <a href="{{ route('landing.vibewall') }}"
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-400">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">💭</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Silent Social Wall
                    </h3>
                    <p class="text-gray-600">
                        Bagikan vibe-mu secara anonim di wall cafe. Temukan kesamaan dengan pengunjung lain tanpa tekanan
                        sosmed.
                    </p>
                </a>

                {{-- Feature 5: Empathy Radar --}}
                <div
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-500">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">💝</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Empathy Radar
                    </h3>
                    <p class="text-gray-600">
                        Barista kami bisa melihat mood summary kamu, sehingga bisa memberikan layanan yang lebih personal
                        dan empati.
                    </p>
                </div>

                {{-- Feature 6: No Online Payment --}}
                <div
                    class="group p-8 bg-brew-light rounded-2xl hover:shadow-xl transition-all duration-300 border border-transparent hover:border-brew-gold/20 scale-in delay-600">
                    <div
                        class="w-14 h-14 bg-brew-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brew-gold/20 transition-colors">
                        <span class="text-3xl">💵</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Bayar di Kasir
                    </h3>
                    <p class="text-gray-600">
                        Tidak perlu ribet dengan pembayaran online. Pesan via app, bayar langsung di kasir dengan cash,
                        QRIS, atau debit.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="py-20 bg-brew-cream/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="text-center mb-16 fade-in">
                <span class="text-brew-gold font-medium">Cara Kerja</span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-brew-dark mt-2 mb-4">
                    Semudah 1, 2, 3
                </h2>
            </div>

            {{-- Steps --}}
            <div class="grid md:grid-cols-3 gap-8">
                {{-- Step 1 --}}
                <div class="text-center fade-in delay-100">
                    <div
                        class="w-16 h-16 bg-brew-brown text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Scan QR di Meja
                    </h3>
                    <p class="text-gray-600">
                        Duduk di meja manapun dan scan QR code untuk membuka menu digital MoodBrew.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center fade-in delay-300">
                    <div
                        class="w-16 h-16 bg-brew-brown text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Ceritakan Mood-mu
                    </h3>
                    <p class="text-gray-600">
                        Ketik perasaanmu atau pilih menu langsung. AI akan memberikan rekomendasi personal.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center fade-in delay-500">
                    <div
                        class="w-16 h-16 bg-brew-brown text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="font-display text-xl font-bold text-brew-dark mb-3">
                        Bayar & Nikmati
                    </h3>
                    <p class="text-gray-600">
                        Orderan masuk ke kasir. Bayar saat siap, dan nikmati minumanmu yang diantar ke meja.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 gradient-brew">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-6">
                Siap Mencoba Pengalaman Baru?
            </h2>
            <p class="text-brew-cream/80 text-lg mb-8 max-w-2xl mx-auto">
                Kunjungi MoodBrew dan rasakan bagaimana AI bisa membuat pengalaman ngopi-mu lebih personal dan berkesan. Tidak perlu daftar, langsung pesan!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-brew-gold text-brew-dark font-bold rounded-full hover:bg-yellow-400 transition-all transform hover:scale-105 shadow-lg">
                    <span>Mulai Pesan Sekarang</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <a href="{{ route('landing.menu') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white font-medium rounded-full hover:bg-white/20 transition-all backdrop-blur-sm">
                    Lihat Menu Kami
                </a>
            </div>
        </div>
    </section>
@endsection
