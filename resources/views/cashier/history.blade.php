@extends('layouts.pos')

@section('title', 'Riwayat pesanan')

@section('content')
    @include('cashier._pos-coffee-styles')
    @include('cashier._cashier-list-styles')

    <div class="pos-coffee ch-page">
        @include('cashier._cashier-header', ['active' => 'history', 'openBillsCount' => $openBillsCount])

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
                <form method="GET" class="ch-grid-filter ch-grid-filter--history">
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
                        <input id="q" type="search" name="q" value="{{ $q }}" placeholder="No transaksi, nama pelanggan, atau kasir…" class="ch-input" />
                    </div>
                    <div>
                        <label class="ch-label" for="status">Status</label>
                        <select id="status" name="status" class="ch-input">
                            <option value="">Semua</option>
                            <option value="paid" @selected(($status ?? '') === 'paid')>Lunas</option>
                            <option value="open" @selected(($status ?? '') === 'open')>Open Bill</option>
                        </select>
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
                            $methodLabel = $t->isOpen() ? 'OPEN BILL' : strtoupper($method);
                        @endphp
                        <div class="ch-row">
                            <div>
                                <p class="ch-id">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="ch-trx-title">{{ $t->created_at->format('d M Y · H:i') }}</p>
                                <p class="ch-trx-meta">Kasir: {{ $t->user?->name ?? '—' }}</p>
                                @if ($t->nama_pelanggan)
                                    <p class="ch-trx-meta">Pelanggan: {{ $t->nama_pelanggan }}</p>
                                @endif
                            </div>
                            <div>
                                <p class="ch-trx-meta">{{ $itemsLabel }} item</p>
                                <p class="ch-trx-title" style="font-weight:500;font-size:13px;color:var(--brown-mid)">
                                    {{ $t->details->take(3)->pluck('product.nama_produk')->filter()->implode(', ') }}{{ $t->details->count() > 3 ? ', …' : '' }}
                                </p>
                            </div>
                            <div>
                                <span class="ch-method {{ $t->isOpen() ? 'open_bill' : (in_array($method, ['cash','transfer','qris','split','open_bill']) ? $method : '') }}">
                                    {{ $methodLabel }}
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
