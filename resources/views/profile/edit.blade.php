@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('breadcrumbs')
    <a href="{{ $user->homeUrl() }}">Beranda</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Pengaturan</span>
@endsection

@section('content')
    @if ($user->isAdmin())
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            @foreach ([$domainStatus, $hostingStatus] as $status)
                <div class="vx-stat">
                    <span class="vx-stat-icon {{ $status['icon'] }}">
                        @if ($status['label'] === 'Domain')
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m12.866 3.015a9 9 0 0 1-1.414 2.414M4.757 9.597a9 9 0 0 0 0 12.806m14.486 0a9 9 0 0 0 0-12.806"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3"/></svg>
                        @endif
                    </span>
                    <div class="min-w-0">
                        <p class="vx-stat-label">Jatuh tempo {{ $status['label'] }}</p>
                        <p class="vx-stat-value text-xl">{{ $status['value'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $status['caption'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div class="{{ $user->isAdmin() ? '' : 'md:col-span-2' }}">
            <div class="vx-table-wrap flex h-full flex-col">
                <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                    <div>
                        <h2>Ubah password</h2>
                        <p>Perbarui password untuk menjaga keamanan akun</p>
                    </div>
                </div>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        @if ($user->isAdmin())
            <div>
                <div class="vx-table-wrap flex h-full flex-col">
                    <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                        <div>
                            <h2>Jatuh tempo pembayaran</h2>
                            <p>Atur tanggal domain dan hosting</p>
                        </div>
                    </div>
                    @include('profile.partials.billing-settings-form')
                </div>
            </div>
        @endif
    </div>
@endsection
