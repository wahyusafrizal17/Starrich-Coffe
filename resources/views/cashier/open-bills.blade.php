@extends('layouts.pos')

@section('title', 'Open Bill')

@php
    $posPayload = [
        'products' => [],
        'categories' => [],
        'checkoutUrl' => route('cashier.checkout'),
        'openBillsUrl' => route('cashier.open-bills.data'),
        'payOpenBillUrlTemplate' => route('cashier.open-bills.pay', ['transaction' => '__ID__']),
        'invoiceUrlTemplate' => route('cashier.invoice', ['transaction' => '__ID__']),
        'openBills' => $openBillsPayload,
        'csrf' => csrf_token(),
    ];
@endphp

@section('content')
    @include('cashier._pos-coffee-styles')
    @include('cashier._cashier-list-styles')

    <div
        class="pos-coffee ch-page"
        x-data="StarrichPos({{ \Illuminate\Support\Js::from($posPayload) }})"
        x-on:keydown.escape.window="if (payModalOpen) closePaymentModal()"
    >
        @include('cashier._cashier-header', ['active' => 'open-bills', 'openBillsCount' => $openBillsCount])

        <div class="ch-content">
            <h1 style="font-size:22px;font-weight:700;color:var(--brown-dark);margin:0 0 4px">Open Bill</h1>
            <p style="font-size:13px;color:var(--brown-light);margin:0 0 18px">Tagihan yang belum lunas. Pilih untuk memproses pembayaran.</p>

            <div class="ch-stats">
                <div class="ch-card ch-card-pad-sm ch-stat">
                    <span class="ch-stat-icon ch-stat-icon--amber">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.8 2.1c.72.2 1.45-.34 1.45-1.09v-1.01M3.75 4.5v.75A60.07 60.07 0 0 1 18 7.5m0 0v.75a60.07 60.07 0 0 0 2.25.18M18 12.75v.75a60.07 60.07 0 0 1-15.8 2.1c-.72.2-1.45-.34-1.45-1.09V13.5"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="ch-stat-label">Total tagihan</p>
                        <p class="ch-stat-value">{{ format_rupiah($sumTotal) }}</p>
                    </div>
                </div>
                <div class="ch-card ch-card-pad-sm ch-stat">
                    <span class="ch-stat-icon ch-stat-icon--amber">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="ch-stat-label">Jumlah open bill</p>
                        <p class="ch-stat-value">{{ number_format($countBills, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="ch-card ch-card-pad-sm" style="margin-bottom:14px">
                <form method="GET" class="ch-grid-filter ch-grid-filter--open-bills">
                    <div>
                        <label class="ch-label" for="q">Cari</label>
                        <input
                            id="q"
                            type="search"
                            name="q"
                            value="{{ $q }}"
                            placeholder="No transaksi, nama pelanggan, atau kasir…"
                            class="ch-input"
                        />
                    </div>
                    <button type="submit" class="ch-btn ch-btn-primary">Terapkan</button>
                </form>
            </div>

            @if ($transactions->isEmpty())
                <div class="ch-card ch-empty">Tidak ada open bill aktif.</div>
            @else
                <div class="ch-list">
                    @foreach ($transactions as $t)
                        @php
                            $itemsLabel = $t->details->sum('qty');
                            $billPayload = $t->toOpenBillArray();
                        @endphp
                        <div class="ch-row" x-show="openBills.some((b) => b.id === {{ $t->id }})">
                            <div>
                                <p class="ch-id">
                                    #{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}@if ($t->nama_pelanggan) / {{ $t->nama_pelanggan }}@endif
                                </p>
                                <p class="ch-trx-title">{{ $t->created_at->format('d M Y · H:i') }}</p>
                                <p class="ch-trx-meta">Kasir: {{ $t->user?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="ch-trx-meta">{{ $itemsLabel }} item</p>
                                @if ($t->order_type)
                                    <p class="ch-trx-meta">{{ $t->order_type === 'take' ? 'Take Away' : 'Dine In' }}</p>
                                @endif
                                <p class="ch-trx-title" style="font-weight:500;font-size:13px;color:var(--brown-mid)">
                                    {{ $t->details->take(3)->pluck('product.nama_produk')->filter()->implode(', ') }}{{ $t->details->count() > 3 ? ', …' : '' }}
                                </p>
                            </div>
                            <div>
                                <span class="ch-method open_bill">OPEN BILL</span>
                            </div>
                            <div class="ch-total">{{ format_rupiah($t->total) }}</div>
                            <div class="ch-actions">
                                <button
                                    type="button"
                                    class="ch-btn ch-btn-primary"
                                    x-on:click="openSettleModal({{ \Illuminate\Support\Js::from($billPayload) }})"
                                    :disabled="paying"
                                >
                                    Bayar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @include('cashier._pay-modal')
    </div>
@endsection
