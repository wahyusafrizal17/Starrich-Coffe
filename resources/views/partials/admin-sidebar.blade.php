@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
@endphp

<aside
    class="vx-sidebar flex shrink-0 flex-col self-stretch lg:static lg:translate-x-0"
    :class="{ 'is-open': sidebarOpen }"
>
    <div class="vx-sidebar-brand">
        <img
            src="{{ asset('images/logo/logo.png') }}"
            alt="{{ config('app.name', 'Starrich') }}"
            decoding="async"
            loading="eager"
        />
    </div>

    <div class="min-h-0 flex-1 overflow-y-auto">
        @if ($isAdmin)
            <p class="vx-sidebar-section">Ringkasan</p>
            <nav class="vx-sidebar-nav">
                <a href="{{ route('dashboard') }}" class="vx-sidebar-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75 12 3l8.25 6.75v9a1.5 1.5 0 0 1-1.5 1.5h-4.5v-6h-4.5v6h-4.5a1.5 1.5 0 0 1-1.5-1.5v-9Z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.reports.index') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 9 7.5l3.75 3.75L21 4.5M21 4.5h-4.5M21 4.5V9m0 10.5H3"/></svg>
                    Laporan penjualan
                </a>
                <a href="{{ route('admin.reports.profit-loss') }}" class="vx-sidebar-link {{ request()->routeIs('admin.reports.profit-loss') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.5 4.5L21.75 7.5M21.75 7.5H16.5M21.75 7.5V12.75"/></svg>
                    Laporan laba rugi
                </a>
            </nav>
        @endif

        <p class="vx-sidebar-section">Operasional</p>
        <nav class="vx-sidebar-nav">
            <a href="{{ route('cashier.index') }}" class="vx-sidebar-link {{ request()->routeIs('cashier.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.5l1.05 4.2M6 16.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm12 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-12-3h12.36a1.5 1.5 0 0 0 1.47-1.2L21.75 6H4.8"/></svg>
                Kasir
            </a>
            @if ($isAdmin)
                <a href="{{ route('admin.products.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-8.25 4.5-8.25-4.5M12 12v9.75M20.25 7.5v9l-8.25 4.5L3.75 16.5v-9L12 3l8.25 4.5Z"/></svg>
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A1.5 1.5 0 0 1 4.5 6h3.879c.265 0 .52.105.707.293L10.5 7.5h9A1.5 1.5 0 0 1 21 9v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18V7.5Z"/></svg>
                    Kategori
                </a>
            @endif
        </nav>

        @if ($isAdmin)
            <p class="vx-sidebar-section">Keuangan</p>
            <nav class="vx-sidebar-nav">
                <a href="{{ route('admin.expenses.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M3.75 6.75h16.5a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                    Pengeluaran
                </a>
                <a href="{{ route('admin.assets.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.assets.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    Aset & Peralatan
                </a>
            </nav>

            <p class="vx-sidebar-section">Manajemen</p>
            <nav class="vx-sidebar-nav">
                <a href="{{ route('admin.users.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0v.75H4.5v-.75Z"/></svg>
                    Pengguna
                </a>
            </nav>
        @endif

        <p class="vx-sidebar-section">Akun</p>
        <nav class="vx-sidebar-nav">
            <a href="{{ route('profile.edit') }}" class="vx-sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.094c.55 0 1.02.398 1.11.94l.213 1.28a7.46 7.46 0 0 1 1.624.948l1.214-.46a1.125 1.125 0 0 1 1.37.488l.547.948a1.125 1.125 0 0 1-.26 1.43l-1.003.827a7.541 7.541 0 0 1 0 1.875l1.003.827a1.125 1.125 0 0 1 .26 1.43l-.547.948a1.125 1.125 0 0 1-1.37.49l-1.214-.461a7.461 7.461 0 0 1-1.624.948l-.213 1.281c-.09.543-.56.94-1.11.94h-1.094c-.55 0-1.02-.397-1.11-.94l-.213-1.281a7.46 7.46 0 0 1-1.624-.948l-1.214.46a1.125 1.125 0 0 1-1.37-.488l-.547-.948a1.125 1.125 0 0 1 .26-1.43l1.003-.827a7.541 7.541 0 0 1 0-1.875l-1.003-.827a1.125 1.125 0 0 1-.26-1.43l.547-.948a1.125 1.125 0 0 1 1.37-.49l1.214.461a7.461 7.461 0 0 1 1.624-.948l.213-1.28ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z"/></svg>
                Profil
            </a>
        </nav>
    </div>
</aside>
