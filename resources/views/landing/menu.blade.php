@extends('layouts.landingLayout')

@section('title', 'Menu - MoodBrew')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-brew-brown to-brew-dark text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center fade-in">
                <h1 class="font-display text-4xl md:text-5xl font-bold mb-4">
                    ☕ Menu Kami
                </h1>
                <p class="text-brew-cream/80 text-lg max-w-2xl mx-auto">
                    Pilih sendiri atau biarkan AI merekomendasikan minuman yang cocok dengan mood-mu hari ini
                </p>
            </div>
        </div>
    </section>

    {{-- Flash Sale Banner --}}
    @if($flashSale)
    <section class="bg-gradient-to-r from-brew-gold to-yellow-500 py-4 scale-in">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-3 text-brew-dark">
                <span class="text-2xl">⚡</span>
                <p class="font-bold">
                    Flash Sale Aktif! Diskon {{ $flashSale->discount_percentage }}% - {{ $flashSale->promo_code }}
                </p>
                <span class="text-sm">
                    Berakhir {{ $flashSale->ends_at->diffForHumans() }}
                </span>
            </div>
        </div>
    </section>
    @endif

    {{-- Menu Grid --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach($menuByCategory as $category => $items)
                <div class="mb-12">
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-brew-dark mb-6 capitalize fade-in">
                        {{ str_replace('_', ' ', $category) }}
                    </h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($items as $item)
                            <div class="bg-brew-light rounded-2xl p-6 hover:shadow-xl transition-all border border-transparent hover:border-brew-gold/20 scale-in transform hover:scale-105">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg text-brew-dark">{{ $item->name }}</h3>
                                    @if($flashSale)
                                        <div class="text-right">
                                            <p class="text-sm text-gray-400 line-through">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            <p class="text-lg font-bold text-brew-gold">Rp {{ number_format($item->getCurrentPrice(), 0, ',', '.') }}</p>
                                        </div>
                                    @else
                                        <p class="text-lg font-bold text-brew-gold">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                @if($item->description)
                                    <p class="text-gray-600 text-sm mb-4">{{ $item->description }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="px-3 py-1 bg-brew-gold/20 text-brew-gold rounded-full text-xs font-medium">
                                        {{ $item->is_available && $item->stock_quantity > 0 ? '✓ Tersedia' : '✗ Habis' }}
                                    </span>
                                    @if($item->flavor_profile && isset($item->flavor_profile['mood_tags']))
                                        <div class="flex gap-1">
                                            @foreach(array_slice($item->flavor_profile['mood_tags'], 0, 2) as $tag)
                                                <span class="text-xs text-gray-500">#{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 gradient-brew">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-6">
                Siap Memesan dengan AI?
            </h2>
            <p class="text-brew-cream/80 text-lg mb-8">
                Ceritakan mood-mu dan biarkan AI merekomendasikan minuman yang sempurna!
            </p>
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center px-8 py-4 bg-brew-gold text-brew-dark font-bold rounded-full hover:bg-yellow-400 transition-all transform hover:scale-105 shadow-lg">
                🤖 Mulai Pesan Sekarang
            </a>
        </div>
    </section>
@endsection
