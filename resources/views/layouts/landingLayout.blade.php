<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="MoodBrew - Cafe dengan AI yang memahami mood kamu. Dapatkan rekomendasi minuman sesuai perasaanmu hari ini.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MoodBrew - Coffee That Understands You')</title>

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
    </style>

    {{-- Alpine.js for interactivity --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
