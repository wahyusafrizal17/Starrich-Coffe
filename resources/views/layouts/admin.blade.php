<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') — {{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        </style>
        @include('partials.admin-styles')
    </head>
    <body
        class="vx-app h-full overflow-hidden text-slate-900 antialiased"
        x-data="{
            sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
            mobileOpen: false,
        }"
        x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebarOpen', v))"
    >
        @include('partials.flash-bridge')

        <div class="flex h-screen overflow-hidden">
            <div
                class="vx-sidebar-shell hidden lg:block"
                :class="{ 'is-collapsed': !sidebarOpen }"
            >
                @include('partials.admin-sidebar')
            </div>

            <div
                class="vx-sidebar-shell lg:hidden"
                :class="mobileOpen ? 'is-open' : 'is-collapsed'"
            >
                @include('partials.admin-sidebar')
            </div>

            <div
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm transition-opacity lg:hidden"
                x-show="mobileOpen"
                x-transition.opacity
                x-on:click="mobileOpen = false"
                x-cloak
            ></div>

            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
                <header class="vx-topbar">
                    <div class="vx-topbar-inner">
                        <button
                            type="button"
                            class="vx-topbar-burger lg:hidden"
                            x-on:click="mobileOpen = !mobileOpen"
                            aria-label="Buka menu"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="vx-topbar-burger hidden lg:inline-flex"
                            x-on:click="sidebarOpen = !sidebarOpen"
                            :title="sidebarOpen ? 'Tutup sidebar' : 'Buka sidebar'"
                            aria-label="Toggle sidebar"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="min-w-0 hidden sm:block">
                            <p class="vx-topbar-context">Panel Admin · {{ config('app.name', 'Starrich') }}</p>
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
                                    <small>{{ auth()->user()->email }}</small>
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
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.127.763.42 1.484.848 2.07L17.25 9.75a1.125 1.125 0 0 1 0 1.59l-2.036 2.036a1.125 1.125 0 0 1-1.59 0l-1.214-1.214a7.461 7.461 0 0 0-1.624-.948l-.213-1.281c-.09-.543-.56-.94-1.11-.94h-1.094c-.55 0-1.02-.397-1.11-.94l-.213-1.281a7.46 7.46 0 0 1-1.624-.948l-1.214.461a1.125 1.125 0 0 1-1.37-.488l-.547-.948a1.125 1.125 0 0 1 .26-1.43l1.003-.827a7.541 7.541 0 0 1 0-1.875l-1.003-.827a1.125 1.125 0 0 1-.26-1.43l.547-.948a1.125 1.125 0 0 1 1.37-.49l1.214.461a7.461 7.461 0 0 1 1.624-.948l.213-1.28ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z"/></svg>
                                    Pengaturan
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

                <main class="min-h-0 flex-1 overflow-auto p-6 lg:p-8">
                    @include('partials.billing-due-alerts')

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
