@php
    $user = auth()->user();
@endphp

<div
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-slate-200/80 bg-white shadow-lg transition-transform duration-200 ease-out lg:static lg:translate-x-0 lg:shadow-none"
    :class="{ '!translate-x-0': sidebarOpen }"
>
    <div class="flex h-14 items-center gap-2 border-b border-slate-100 px-4 lg:h-16">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-700 text-sm font-bold text-white">X</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-slate-900">{{ config('app.name', 'Starrich') }}</p>
            <p class="truncate text-xs text-slate-500">{{ $user->name }}</p>
        </div>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto p-3">
        @if ($user->isAdmin())
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <span class="text-lg leading-none" aria-hidden="true">▣</span>
                Dashboard
            </a>
        @endif

        <a
            href="{{ route('cashier.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('cashier.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
        >
            <span class="text-lg leading-none" aria-hidden="true">🛒</span>
            Kasir
        </a>

        @if ($user->isAdmin())
            <a
                href="{{ route('admin.categories.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <span class="text-lg leading-none" aria-hidden="true">📁</span>
                Kategori
            </a>
            <a
                href="{{ route('admin.products.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <span class="text-lg leading-none" aria-hidden="true">📦</span>
                Produk
            </a>
            <a
                href="{{ route('admin.reports.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <span class="text-lg leading-none" aria-hidden="true">📊</span>
                Laporan
            </a>
            <a
                href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <span class="text-lg leading-none" aria-hidden="true">👥</span>
                Pengguna
            </a>
        @endif

        <a
            href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50' }}"
        >
            <span class="text-lg leading-none" aria-hidden="true">⚙</span>
            Profil
        </a>
    </nav>
    <div class="border-t border-slate-100 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Keluar
            </button>
        </form>
    </div>
</div>
