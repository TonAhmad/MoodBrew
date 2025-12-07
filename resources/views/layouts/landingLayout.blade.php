<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="MoodBrew - Cafe dengan AI yang memahami mood kamu. Dapatkan rekomendasi minuman sesuai perasaanmu hari ini.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MoodBrew - Cafe That Understands You')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/moodbrew.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/moodbrew.png') }}">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brew-brown': '#4A3728',
                        'brew-cream': '#F5E6D3',
                        'brew-gold': '#C9A227',
                        'brew-dark': '#2C1810',
                        'brew-light': '#FDF8F3',
                    },
                    fontFamily: {
                        'display': ['Georgia', 'serif'],
                        'body': ['system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Custom Styles --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        .gradient-brew {
            background: linear-gradient(135deg, #4A3728 0%, #2C1810 100%);
        }

        .text-gradient {
            background: linear-gradient(135deg, #C9A227 0%, #F5E6D3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Scroll Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }

        .fade-in-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-left.active {
            opacity: 1;
            transform: translateX(0);
        }

        .fade-in-right {
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-right.active {
            opacity: 1;
            transform: translateX(0);
        }

        .scale-in {
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .scale-in.active {
            opacity: 1;
            transform: scale(1);
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Stagger animation delays */
        .delay-100 { transition-delay: 0.1s; }
        .delay-200 { transition-delay: 0.2s; }
        .delay-300 { transition-delay: 0.3s; }
        .delay-400 { transition-delay: 0.4s; }
        .delay-500 { transition-delay: 0.5s; }
        .delay-600 { transition-delay: 0.6s; }
    </style>

    {{-- Alpine.js for interactivity --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Scroll Animation Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    @stack('styles')
</head>

<body class="bg-brew-light font-body antialiased">
    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    @stack('scripts')
</body>

</html>
