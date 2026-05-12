@extends('layouts.pos')

@section('title', 'Riwayat pesanan')

@section('content')
    @include('cashier._pos-coffee-styles')

    <style>
        .ch-page { display: flex; flex-direction: column; height: 100dvh; }
        .ch-content {
            flex: 1;
            min-height: 0;
            overflow: auto;
            padding: 20px;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
        }
        .ch-card {
            background: #fff;
            border: 1px solid var(--caramel-light);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .ch-card + .ch-card { margin-top: 14px; }
        .ch-card-pad-sm { padding: 14px 16px; }
        .ch-grid-filter { display: grid; grid-template-columns: 1fr 1fr 1.4fr auto; gap: 10px; align-items: end; }
        .ch-label { display: block; font-size: 11px; font-weight: 600; color: var(--brown-mid); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
        .ch-input, .ch-select {
            width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid var(--caramel-light);
            background: #fff; font-size: 13px; color: var(--brown-dark); transition: border-color 0.15s ease;
        }
        .ch-input:focus, .ch-select:focus { outline: none; border-color: var(--caramel); box-shadow: 0 0 0 3px rgba(59,130,246,0.18); }
        .ch-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none; border: 1px solid transparent; transition: background 0.15s ease, color 0.15s ease;
        }
        .ch-btn-primary { background: var(--caramel); color: #fff; }
        .ch-btn-primary:hover { background: #1d4ed8; }
        .ch-btn-ghost { background: #fff; color: var(--brown-mid); border-color: var(--caramel-light); }
        .ch-btn-ghost:hover { background: var(--cream); }

        .ch-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .ch-stat { display: flex; align-items: center; gap: 12px; padding: 14px 16px; }
        .ch-stat-icon {
            width: 40px; height: 40px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
            background: var(--caramel-light); color: var(--caramel);
        }
        .ch-stat-icon svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 1.8; }
        .ch-stat-label { font-size: 11px; font-weight: 600; color: var(--brown-light); text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
        .ch-stat-value { font-size: 18px; font-weight: 700; color: var(--brown-dark); margin: 2px 0 0; }

        .ch-list { display: flex; flex-direction: column; gap: 10px; }
        .ch-row {
            display: grid; grid-template-columns: 1.6fr 1.6fr 1fr 1.2fr auto; gap: 12px;
            padding: 14px 16px; align-items: center; border-radius: 12px;
            background: #fff; border: 1px solid var(--caramel-light);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ch-row:hover { border-color: var(--caramel); box-shadow: 0 4px 12px rgba(37,99,235,0.08); }
        .ch-row .ch-id { font-family: 'Menlo', ui-monospace, monospace; font-size: 12px; color: var(--brown-light); }
        .ch-row .ch-trx-title { font-weight: 600; color: var(--brown-dark); font-size: 14px; }
        .ch-row .ch-trx-meta { font-size: 12px; color: var(--brown-light); margin-top: 2px; }
        .ch-row .ch-total { font-weight: 700; color: var(--brown-dark); text-align: right; }
        .ch-row .ch-method { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: var(--caramel-light); color: var(--caramel); }
        .ch-row .ch-method.cash { background: #dcfce7; color: #15803d; }
        .ch-row .ch-method.transfer { background: #ede9fe; color: #6d28d9; }
        .ch-row .ch-method.qris { background: #fef3c7; color: #b45309; }
        .ch-row .ch-method.split { background: #cffafe; color: #0e7490; }
        .ch-actions { display: flex; gap: 6px; justify-content: flex-end; }
        .ch-icon-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--caramel-light);
            background: #fff; color: var(--brown-mid); cursor: pointer; text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        .ch-icon-btn:hover { background: var(--caramel); color: #fff; border-color: var(--caramel); }
        .ch-icon-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; }

        .ch-empty { padding: 60px 20px; text-align: center; color: var(--brown-light); font-size: 14px; }
        .ch-pagination { margin-top: 16px; }

        @media (max-width: 768px) {
            .ch-grid-filter { grid-template-columns: 1fr 1fr; }
            .ch-grid-filter > button { grid-column: 1 / -1; }
            .ch-row { grid-template-columns: 1fr 1fr; }
            .ch-row .ch-total, .ch-row .ch-actions { grid-column: 1 / -1; justify-content: flex-end; text-align: right; }
        }
    </style>

    <div class="pos-coffee ch-page">
        <header class="pc-header">
            <div class="brand">
                <img
                    src="{{ asset('images/logo/logo.png') }}"
                    alt="Starrich Coffee &amp; Good Vibes"
                    class="pc-header-logo"
                    width="200"
                    height="48"
                    decoding="async"
                    loading="eager"
                />
            </div>
            <div class="pc-header-meta">
                <span>Kasir: <strong>{{ auth()->user()->name }}</strong></span>
                <div class="pc-header-actions">
                    <a href="{{ route('cashier.index') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                        </svg>
                        Kembali ke kasir
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

        <div class="ch-content">
            <h1 style="font-size:22px;font-weight:700;color:var(--brown-dark);margin:0 0 4px">Riwayat Pesanan</h1>
            <p style="font-size:13px;color:var(--brown-light);margin:0 0 18px">Catatan semua transaksi kasir.</p>

            <div class="ch-stats">
                <div class="ch-card ch-card-pad-sm ch-stat">
                    <span class="ch-stat-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.8 2.1c.72.2 1.45-.34 1.45-1.09v-1.01M3.75 4.5v.75A60.07 60.07 0 0 1 18 7.5m0 0v.75a60.07 60.07 0 0 0 2.25.18M18 12.75v.75a60.07 60.07 0 0 1-15.8 2.1c-.72.2-1.45-.34-1.45-1.09V13.5"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="ch-stat-label">Total penjualan</p>
                        <p class="ch-stat-value">{{ format_rupiah($sumTotal) }}</p>
                    </div>
                </div>
                <div class="ch-card ch-card-pad-sm ch-stat">
                    <span class="ch-stat-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="ch-stat-label">Jumlah transaksi</p>
                        <p class="ch-stat-value">{{ number_format($countTrx, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="ch-card ch-card-pad-sm" style="margin-bottom:14px">
                <form method="GET" class="ch-grid-filter">
                    <div>
                        <label class="ch-label" for="from">Dari</label>
                        <input id="from" type="date" name="from" value="{{ $from?->format('Y-m-d') }}" class="ch-input" />
                    </div>
                    <div>
                        <label class="ch-label" for="to">Sampai</label>
                        <input id="to" type="date" name="to" value="{{ $to?->format('Y-m-d') }}" class="ch-input" />
                    </div>
                    <div>
                        <label class="ch-label" for="q">Cari</label>
                        <input id="q" type="search" name="q" value="{{ $q }}" placeholder="No transaksi atau nama kasir…" class="ch-input" />
                    </div>
                    <button type="submit" class="ch-btn ch-btn-primary">Terapkan</button>
                </form>
            </div>

            @if ($transactions->count() === 0)
                <div class="ch-card ch-empty">Belum ada transaksi pada filter ini.</div>
            @else
                <div class="ch-list">
                    @foreach ($transactions as $t)
                        @php
                            $itemsLabel = $t->details->sum('qty');
                            $method = $t->metode_pembayaran ?? 'cash';
                        @endphp
                        <div class="ch-row">
                            <div>
                                <p class="ch-id">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="ch-trx-title">{{ $t->created_at->format('d M Y · H:i') }}</p>
                                <p class="ch-trx-meta">Kasir: {{ $t->user?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="ch-trx-meta">{{ $itemsLabel }} item</p>
                                <p class="ch-trx-title" style="font-weight:500;font-size:13px;color:var(--brown-mid)">
                                    {{ $t->details->take(3)->pluck('product.nama_produk')->filter()->implode(', ') }}{{ $t->details->count() > 3 ? ', …' : '' }}
                                </p>
                            </div>
                            <div>
                                <span class="ch-method {{ in_array($method, ['cash','transfer','qris','split']) ? $method : '' }}">
                                    {{ strtoupper($method) }}
                                </span>
                            </div>
                            <div class="ch-total">{{ format_rupiah($t->total) }}</div>
                            <div class="ch-actions">
                                <a href="{{ route('cashier.invoice', $t) }}" class="ch-icon-btn" title="Lihat struk" aria-label="Lihat struk" target="_blank" rel="noopener">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </a>
                                <a href="{{ route('cashier.invoice', $t) }}?print=1" class="ch-icon-btn" title="Cetak ulang" aria-label="Cetak ulang" target="_blank" rel="noopener">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="ch-pagination">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
@endsection
