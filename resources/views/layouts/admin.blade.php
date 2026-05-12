<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') — {{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
        @include('partials.admin-styles')
    </head>
    <body class="vx-app h-full overflow-hidden text-slate-900" x-data="{ sidebarOpen: false }">
        @include('partials.flash-bridge')

        <div class="flex h-screen items-stretch overflow-hidden">
            @include('partials.admin-sidebar')

            <div
                class="fixed inset-0 z-40 bg-slate-900/35 backdrop-blur-sm transition-opacity lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                x-on:click="sidebarOpen = false"
                x-cloak
            ></div>

            <div class="flex min-h-0 min-w-0 flex-1 flex-col">
                <header class="vx-topbar">
                    <div class="vx-topbar-inner">
                        <button
                            type="button"
                            class="vx-topbar-burger lg:hidden"
                            x-on:click="sidebarOpen = !sidebarOpen"
                            aria-label="Buka menu"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <h1 class="vx-topbar-title truncate">@yield('title', 'Admin')</h1>
                            <div class="vx-breadcrumbs">
                                @hasSection('breadcrumbs')
                                    @yield('breadcrumbs')
                                @else
                                    <a href="{{ route('dashboard') }}">Beranda</a>
                                    <span class="vx-sep">/</span>
                                    <span class="vx-current">@yield('title', 'Admin')</span>
                                @endif
                            </div>
                        </div>

                        <div class="vx-topbar-user" x-data="{ userMenu: false }" x-on:keydown.escape.window="userMenu = false">
                            <button
                                type="button"
                                class="vx-topbar-user-btn"
                                x-on:click="userMenu = !userMenu"
                                aria-haspopup="menu"
                                :aria-expanded="userMenu.toString()"
                            >
                                <div class="hidden sm:block vx-meta">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <small>{{ ucfirst(auth()->user()->role) }}</small>
                                </div>
                                <span class="vx-avatar" aria-hidden="true">
                                    {{ \Illuminate\Support\Str::of(auth()->user()->name)->trim()->upper()->substr(0, 1) }}
                                </span>
                            </button>

                            <div
                                class="vx-user-menu"
                                x-show="userMenu"
                                x-transition.opacity.duration.150ms
                                x-on:click.outside="userMenu = false"
                                x-cloak
                                role="menu"
                            >
                                <div class="vx-user-menu-head">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <small>{{ auth()->user()->email }}</small>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="vx-user-menu-item" role="menuitem">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0v.75H4.5v-.75Z"/></svg>
                                    Profil saya
                                </a>
                                <div class="vx-user-menu-sep"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="vx-user-menu-item is-danger" role="menuitem">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="min-h-0 flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
                    @hasSection('page_header')
                        <div class="vx-page-head">
                            @yield('page_header')
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        @include('partials.toast')
        @stack('scripts')
    </body>
</html>
