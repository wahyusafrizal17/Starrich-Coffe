@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
@endphp

<aside class="vx-sidebar">
    <div class="vx-sidebar-brand">
        <img
            src="{{ asset('images/logo/logo.png') }}"
            alt="{{ config('app.name', 'Starrich') }}"
            decoding="async"
            loading="eager"
        />
        <div class="min-w-0">
            <p class="vx-sidebar-brand-title">{{ config('app.name', 'Starrich') }}</p>
            <p class="vx-sidebar-brand-sub">Management System</p>
        </div>
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
                <a href="{{ route('admin.discounts.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.discounts.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                    Diskon
                </a>
                <a href="{{ route('admin.categories.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A1.5 1.5 0 0 1 4.5 6h3.879c.265 0 .52.105.707.293L10.5 7.5h9A1.5 1.5 0 0 1 21 9v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18V7.5Z"/></svg>
                    Kategori
                </a>
                <a href="{{ route('admin.order-addons.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.order-addons.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15M6 12h.008v.008H6V12Zm3 0h.008v.008H9V12Zm3 0h.008v.008H12V12Zm3 0h.008v.008H15V12Z"/></svg>
                    Tambahan pesanan
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
                <a href="{{ route('admin.inflows.index') }}" class="vx-sidebar-link {{ request()->routeIs('admin.inflows.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6M2.25 18.75a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18.75V7.5a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.53 1.53 0 0 1-1.09-.45L12 3m0 0 2.121-2.121a1.53 1.53 0 0 1 1.09-.45H18.75A2.25 2.25 0 0 1 21 4.5v14.25a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h.379a1.53 1.53 0 0 1 1.09.45L9 6m3-3v3"/></svg>
                    Pemasukan / Modal
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

            <p class="vx-sidebar-section">Sistem</p>
            <nav class="vx-sidebar-nav">
                <a href="{{ route('profile.edit') }}" class="vx-sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.127.763.42 1.484.848 2.07L17.25 9.75a1.125 1.125 0 0 1 0 1.59l-2.036 2.036a1.125 1.125 0 0 1-1.59 0l-1.214-1.214a7.461 7.461 0 0 0-1.624-.948l-.213-1.281c-.09-.543-.56-.94-1.11-.94h-1.094c-.55 0-1.02.397-1.11.94l-.213 1.28a7.46 7.46 0 0 1-1.624.948l-1.214.461a1.125 1.125 0 0 1-1.37-.488l-.547-.948a1.125 1.125 0 0 1 .26-1.43l1.003-.827a7.541 7.541 0 0 1 0-1.875l-1.003-.827a1.125 1.125 0 0 1-.26-1.43l.547-.948a1.125 1.125 0 0 1 1.37-.49l1.214.461a7.461 7.461 0 0 1 1.624-.948l.213-1.28ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z"/></svg>
                    Pengaturan
                </a>
            </nav>
        @endif
    </div>
</aside>
