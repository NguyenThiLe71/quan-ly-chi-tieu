<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Phần mềm quản lý chi tiêu cá nhân</title>

        <!-- 1. Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
        
        <!-- 2. Bootstrap CSS (Quan trọng: Nạp TRƯỚC Vite) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- 3. Swiper & Khác -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ time() }}">
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <!-- 4. Vite (Chứa Tailwind và CSS Luxury của ông) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .force-peach-bg {
                background: linear-gradient(to bottom right, #fee2e2, #ffffff, #ffedd5) !important;
            }
            /* Xử lý nhanh gạch chân và font chữ Itim */
            a { text-decoration: none !important; }
            body { font-family: 'Itim', cursive !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen force-peach-bg">
            
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white/30 backdrop-blur-md shadow-sm border-b border-white/20">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- 5. Bootstrap JS (Kích hoạt bộ não cho nút ẩn hiện) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        @stack('scripts')
    </body>
</html>