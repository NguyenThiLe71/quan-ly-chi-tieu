<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background-image: url('/images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <div class="mb-4">
            <a href="/">
                <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 fill-white opacity-80">
                    <path d="M305.8 81.125C305.8 80.915 305.684 80.725 305.508 80.615L257.733 53.078C257.547 52.969 257.324 52.969 257.139 53.078L209.363 80.615C209.188 80.725 209.07 80.915 209.07 81.125V136.199L161.295 163.736C161.109 163.846 160.887 163.846 160.701 163.736L112.926 136.199V81.125C112.926 80.915 112.809 80.725 112.633 80.615L64.857 53.078C64.672 52.969 64.449 52.969 64.264 53.078L16.488 80.615C16.313 80.725 16.195 80.915 16.195 81.125V233.914C16.195 234.125 16.313 234.314 16.488 234.426L64.264 261.963C64.449 262.072 64.672 262.072 64.857 261.963L112.633 234.426C112.809 234.316 112.926 234.125 112.926 233.914V178.84L160.701 206.377C160.887 206.486 161.109 206.486 161.295 206.377L209.07 178.84V233.914C209.07 234.125 209.188 234.316 209.363 234.426L257.139 261.963C257.324 262.072 257.547 262.072 257.733 261.963L305.508 234.426C305.684 234.316 305.8 234.125 305.8 233.914V81.125Z" />
                </svg>
            </a>
        </div>

        <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>

    </div>

</body>
</html>