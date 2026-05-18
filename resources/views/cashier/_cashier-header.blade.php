@props([
    'active' => null,
    'openBillsCount' => 0,
])

<header class="pc-header">
    <a href="{{ route('cashier.index') }}" class="brand" title="Kasir">
        <img
            src="{{ asset('images/logo/logo.png') }}"
            alt="Starrich Coffee &amp; Good Vibes"
            class="pc-header-logo"
            width="200"
            height="48"
            decoding="async"
            loading="eager"
        />
    </a>
    <div class="pc-header-meta">
        <span>Kasir: <strong>{{ auth()->user()->name }}</strong></span>
        <div class="pc-header-actions">
            <a
                href="{{ route('cashier.history') }}"
                title="Riwayat pesanan"
                @class(['pc-nav-active' => $active === 'history'])
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                History
            </a>
            <a
                href="{{ route('cashier.open-bills') }}"
                title="Open bill aktif"
                @class(['pc-nav-active' => $active === 'open-bills'])
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                </svg>
                Open Bill
                @if ($openBillsCount > 0)
                    <span class="pc-header-badge">{{ $openBillsCount }}</span>
                @endif
            </a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="pc-logout-btn" aria-label="Keluar">
                    <svg class="pc-header-logout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
