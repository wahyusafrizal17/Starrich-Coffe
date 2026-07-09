@props([
    'active' => null,
    'openBillsCount' => 0,
])

<header class="pc-header">
    <a href="{{ route('cashier.index') }}" class="pc-header-brand" title="Kasir">
        <img
            src="{{ asset('images/logo/logo.png') }}"
            alt="{{ config('app.name', 'Starrich') }}"
            class="pc-header-logo"
            width="160"
            height="40"
            decoding="async"
            loading="eager"
        />
    </a>
    <div class="pc-header-meta">
        <span class="pc-header-user">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" class="pc-header-logout-form">
            @csrf
            <button type="submit" class="pc-header-logout" aria-label="Keluar">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
            </button>
        </form>
    </div>
</header>
