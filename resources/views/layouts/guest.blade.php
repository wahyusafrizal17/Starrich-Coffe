<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased text-slate-900">
        <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-teal-800 via-teal-700 to-slate-900 px-4 py-10">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-lg font-bold text-white ring-1 ring-white/20">
                    X
                </div>
                <h1 class="text-xl font-bold text-white">{{ config('app.name', 'Starrich') }}</h1>
                <p class="mt-1 text-sm text-teal-100/90">Masuk untuk melanjutkan</p>
            </div>

            <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white/95 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8">
                {{ $slot }}
            </div>
        </div>
        @include('partials.toast')
    </body>
</html>
