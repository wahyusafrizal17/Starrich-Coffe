@props([
    'active' => null,
    'openBillsCount' => 0,
    'alpine' => false,
])

@php
    $isAdmin = auth()->user()->isAdmin();
@endphp

<nav class="bottom-nav" aria-label="Navigasi kasir">
    <div class="nav-dock">
        <a
            href="{{ route('cashier.index') }}"
            @class(['nav-item', 'active' => $active === 'kasir'])
            title="Kasir"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"/></svg>
            <span>Kasir</span>
        </a>

        <a
            href="{{ route('cashier.history') }}"
            @class(['nav-item', 'active' => $active === 'history'])
            title="Riwayat"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>
            <span>Riwayat</span>
        </a>

        @if ($alpine)
            <button type="button" class="nav-item center" title="Keranjang" x-on:click="openCartDock()">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h14l-1.2 10.2A2 2 0 0 1 14.8 20H7.2a2 2 0 0 1-2-1.8L4 8Z"/><path d="M18 8a3 3 0 0 0 0-6"/></svg>
                <span>Keranjang</span>
                <span class="nav-fab-badge" x-show="cart.length > 0" x-text="cart.length" x-cloak></span>
            </button>
        @else
            <a href="{{ route('cashier.index') }}" class="nav-item center" title="Kasir">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h14l-1.2 10.2A2 2 0 0 1 14.8 20H7.2a2 2 0 0 1-2-1.8L4 8Z"/><path d="M18 8a3 3 0 0 0 0-6"/></svg>
                <span>Keranjang</span>
            </a>
        @endif

        <a
            href="{{ route('cashier.open-bills') }}"
            @class(['nav-item', 'active' => $active === 'open-bills'])
            title="Open Bill"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M9 9h6M9 13h6M9 17h3"/></svg>
            @if ($openBillsCount > 0)
                <span class="nav-badge">{{ $openBillsCount }}</span>
            @endif
            <span>Open Bill</span>
        </a>

        @if ($isAdmin)
            <a
                href="{{ route('dashboard') }}"
                @class(['nav-item', 'active' => $active === 'admin'])
                title="Admin"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.9 3.1-6.5 7-6.5s7 2.6 7 6.5"/></svg>
                <span>Admin</span>
            </a>
        @else
            <form method="POST" action="{{ route('logout') }}" class="nav-item-form">
                @csrf
                <button type="submit" class="nav-item" title="Keluar">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.9 3.1-6.5 7-6.5s7 2.6 7 6.5"/></svg>
                    <span>Keluar</span>
                </button>
            </form>
        @endif
    </div>
</nav>
