<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') — {{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="h-full bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
        @include('partials.flash-bridge')

        <div class="flex min-h-full">
            @include('partials.admin-sidebar')

            <div
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm transition-opacity lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                x-on:click="sidebarOpen = false"
                x-cloak
            ></div>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-3 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur lg:hidden">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 p-2 text-slate-700 transition hover:bg-slate-50"
                        x-on:click="sidebarOpen = !sidebarOpen"
                        aria-label="Buka menu"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="truncate text-base font-semibold">@yield('title', 'Admin')</h1>
                </header>

                @hasSection('page_header')
                    <div class="hidden border-b border-slate-200/80 bg-white px-6 py-4 lg:block">
                        @yield('page_header')
                    </div>
                @endif

                <main class="flex-1 overflow-auto p-4 sm:p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @include('partials.toast')
        @stack('scripts')
    </body>
</html>
