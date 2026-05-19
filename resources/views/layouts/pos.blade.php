<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Kasir') — {{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link
            href="https://fonts.bunny.net/css?family=dm-sans:300,400,500,700|playfair-display:500,600&display=swap"
            rel="stylesheet"
        />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="flex h-full min-h-0 flex-col overflow-hidden bg-[#eff6ff] text-slate-900">
        @include('partials.flash-bridge')
        @yield('content')
        @include('partials.toast')
        @stack('scripts')
    </body>
</html>
