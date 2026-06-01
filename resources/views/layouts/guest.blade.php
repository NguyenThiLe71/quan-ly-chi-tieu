<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>
body{
background-image:url('/images/bg.jpg');
background-size:cover;
background-position:center;
background-repeat:no-repeat;
}
</style>

</head>

<body class="font-sans text-gray-900 antialiased">

<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

<div class="mb-4">
<a href="/">
<x-application-logo class="w-20 h-20 fill-white opacity-80" />
</a>
</div>

<div class="w-full sm:max-w-lg mt-6">

{{ $slot }}

</div>

</div>

</body>
</html>