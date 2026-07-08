@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
    <div
        x-data="{
            showDetail: false,
            trx: null,
            showOpenBills: false,
            openDetail(data) {
                this.trx = data;
                this.showDetail = true;
                document.body.classList.add('overflow-y-hidden');
            },
            closeDetail() {
                this.showDetail = false;
                this.trx = null;
                document.body.classList.remove('overflow-y-hidden');
            },
            openOpenBills() {
                this.showOpenBills = true;
                document.body.classList.add('overflow-y-hidden');
            },
            closeOpenBills() {
                this.showOpenBills = false;
                document.body.classList.remove('overflow-y-hidden');
            },
            closeTopModal() {
                if (this.showDetail) {
                    this.closeDetail();
                } else if (this.showOpenBills) {
                    this.closeOpenBills();
                }
            },
        }"
        @keydown.escape.window="closeTopModal()"
    >
    @include('partials.dashboard-filter-strip', [
        'period' => $period,
        'filterAction' => route('admin.reports.index'),
    ])

    <div class="space-y-4 mb-5">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-success">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.8 2.1c.72.2 1.45-.34 1.45-1.09v-1.01M3.75 4.5v.75A60.07 60.07 0 0 1 18 7.5m0 0v.75a60.07 60.07 0 0 0 2.25.18M18 12.75v.75a60.07 60.07 0 0 1-15.8 2.1c-.72.2-1.45-.34-1.45-1.09V13.5"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Total penjualan</p>
                    <p class="vx-stat-value">{{ format_rupiah($sumTotal) }}</p>
                </div>
            </div>
            <button type="button" class="vx-stat is-clickable" @click="openOpenBills()">
                <span class="vx-stat-icon vx-bg-warning">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Open bill belum lunas</p>
                    <p class="vx-stat-value text-amber-700">{{ format_rupiah($openBillTotal) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $openBillCount }} tagihan outstanding · semua periode</p>
                </div>
            </button>
        </div>
    </div>

    <div class="vx-table-wrap">
        <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
            <div>
                <h2>Transaksi</h2>
                <p>Pantau transaksi · {{ $periodLabel }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($hasActiveColumnFilters)
                    <a href="{{ route('admin.reports.index', $filterQuery) }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                        Reset filter kolom
                    </a>
                @endif
                <a href="{{ route('admin.reports.export', $exportQuery) }}" class="vx-btn vx-btn-primary vx-btn-sm">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                    Export CSV
                </a>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.reports.index') }}">
            @foreach ($filterQuery as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="overflow-x-auto">
                <table class="vx-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Waktu</th>
                            <th>Kasir</th>
                            <th>Pembayaran</th>
                            <th class="vx-text-end">Total</th>
                            <th class="vx-text-end">Detail</th>
                        </tr>
                        <tr class="vx-table-filter-row">
                            <th>
                                <input
                                    type="text"
                                    name="filter_id"
                                    value="{{ $columnFilters['filter_id'] }}"
                                    placeholder="#ID"
                                    class="vx-table-col-filter"
                                    onchange="this.form.submit()"
                                >
                            </th>
                            <th>
                                <input
                                    type="date"
                                    name="filter_waktu"
                                    value="{{ $columnFilters['filter_waktu'] }}"
                                    class="vx-table-col-filter"
                                    onchange="this.form.submit()"
                                >
                            </th>
                            <th>
                                <input
                                    type="text"
                                    name="filter_kasir"
                                    value="{{ $columnFilters['filter_kasir'] }}"
                                    placeholder="Cari kasir…"
                                    class="vx-table-col-filter"
                                    onchange="this.form.submit()"
                                >
                            </th>
                            <th>
                                <select name="filter_pembayaran" class="vx-table-col-filter" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    <option value="cash" @selected($columnFilters['filter_pembayaran'] === 'cash')>Cash</option>
                                    <option value="transfer" @selected($columnFilters['filter_pembayaran'] === 'transfer')>Transfer</option>
                                    <option value="qris" @selected($columnFilters['filter_pembayaran'] === 'qris')>QRIS</option>
                                    <option value="split" @selected($columnFilters['filter_pembayaran'] === 'split')>Split</option>
                                </select>
                            </th>
                            <th>
                                <input
                                    type="text"
                                    name="filter_total"
                                    value="{{ $columnFilters['filter_total'] }}"
                                    placeholder="Nominal…"
                                    class="vx-table-col-filter"
                                    onchange="this.form.submit()"
                                >
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="font-mono text-xs text-slate-500">#{{ $t->id }}</td>
                                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                                <td class="text-slate-600">{{ $t->cashierDisplayName() }}</td>
                                <td>
                                    <span class="vx-badge {{ $t->paymentMethodBadgeClass() }}">
                                        {{ $t->paymentMethodLabel() }}
                                    </span>
                                </td>
                                <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($t->total) }}</td>
                                <td class="vx-text-end">
                                    @php
                                        $detailPayload = [
                                            'id' => $t->id,
                                            'waktu' => $t->created_at->format('d M Y H:i'),
                                            'kasir' => $t->cashierDisplayName(),
                                            'pembayaran' => $t->paymentMethodLabel(),
                                            'total' => format_rupiah($t->total),
                                            'bayar' => format_rupiah($t->bayar),
                                            'kembalian' => format_rupiah($t->kembalian),
                                            'items' => $t->details->map(fn ($d) => [
                                                'nama' => $d->product->nama_produk ?? '—',
                                                'qty' => (int) $d->qty,
                                                'subtotal' => format_rupiah($d->subtotal),
                                            ])->values()->all(),
                                            'splits' => collect($t->payment_splits ?? [])
                                                ->map(fn ($s) => [
                                                    'metode' => match ($s['metode'] ?? '') {
                                                        'cash' => 'Cash',
                                                        'transfer' => 'Transfer',
                                                        'qris' => 'QRIS',
                                                        default => ucfirst($s['metode'] ?? '—'),
                                                    },
                                                    'jumlah' => format_rupiah((int) ($s['jumlah'] ?? 0)),
                                                ])
                                                ->values()
                                                ->all(),
                                        ];
                                    @endphp
                                    <button
                                        type="button"
                                        class="vx-btn-detail"
                                        @click="openDetail(@js($detailPayload))"
                                    >
                                        Rincian
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm text-slate-500 py-10">Tidak ada transaksi pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="vx-table-foot">{{ $transactions->links() }}</div>
    </div>

    <template x-teleport="body">
        <div
            x-show="showOpenBills"
            x-cloak
            class="vx-modal-overlay"
            @click.self="closeOpenBills()"
        >
            <div
                class="vx-modal-panel is-wide"
                role="dialog"
                aria-modal="true"
                aria-labelledby="open-bills-title"
                @click.stop
            >
                <div class="vx-modal-head" style="background: linear-gradient(135deg, #92400e 0%, #b45309 100%);">
                    <div>
                        <h3 id="open-bills-title">Open bill belum lunas</h3>
                        <p>{{ $openBillCount }} tagihan · {{ format_rupiah($openBillTotal) }} · semua periode</p>
                    </div>
                    <button type="button" class="vx-modal-close" @click="closeOpenBills()" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="vx-modal-body">
                    @if ($openBills->isEmpty())
                        <div class="vx-open-bill-empty">Tidak ada open bill aktif.</div>
                    @else
                        <div class="vx-open-bill-list">
                            @foreach ($openBills as $bill)
                                @php
                                    $itemsCount = (int) $bill->details->sum('qty');
                                    $itemsPreview = $bill->details
                                        ->take(3)
                                        ->map(fn ($d) => $d->product?->nama_produk)
                                        ->filter()
                                        ->implode(', ');
                                    if ($bill->details->count() > 3) {
                                        $itemsPreview .= ', …';
                                    }
                                @endphp
                                <div class="vx-open-bill-item">
                                    <div class="min-w-0">
                                        <p class="vx-open-bill-item-id">
                                            #{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}
                                            @if ($bill->nama_pelanggan)
                                                <span class="font-medium text-slate-600">/ {{ $bill->nama_pelanggan }}</span>
                                            @endif
                                        </p>
                                        <p class="vx-open-bill-item-meta">
                                            {{ $bill->created_at->format('d M Y · H:i') }}
                                            · Kasir: {{ $bill->cashierDisplayName() }}
                                        </p>
                                        <p class="vx-open-bill-item-meta">
                                            {{ $itemsCount }} item
                                            @if ($bill->order_type)
                                                · {{ $bill->order_type === 'take' ? 'Take Away' : 'Dine In' }}
                                            @endif
                                        </p>
                                        @if ($itemsPreview !== '')
                                            <p class="vx-open-bill-item-meta">{{ $itemsPreview }}</p>
                                        @endif
                                        <span class="vx-badge vx-badge-warning mt-2">Open bill</span>
                                    </div>
                                    <div class="vx-open-bill-item-total">{{ format_rupiah($bill->total) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div
            x-show="showDetail"
            x-cloak
            class="vx-modal-overlay"
            @click.self="closeDetail()"
        >
            <div
                class="vx-modal-panel"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="trx ? 'trx-detail-title' : null"
                @click.stop
            >
                <div class="vx-modal-head">
                    <div>
                        <h3 id="trx-detail-title" x-text="trx ? 'Transaksi #' + trx.id : 'Detail transaksi'"></h3>
                        <p x-text="trx?.waktu ?? ''"></p>
                    </div>
                    <button type="button" class="vx-modal-close" @click="closeDetail()" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="vx-modal-body" x-show="trx">
                    <div class="vx-modal-meta">
                        <div>
                            <label>Kasir</label>
                            <span x-text="trx?.kasir"></span>
                        </div>
                        <div>
                            <label>Pembayaran</label>
                            <span x-text="trx?.pembayaran"></span>
                        </div>
                    </div>

                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Item pesanan</p>
                    <div class="vx-modal-items">
                        <template x-for="(item, index) in (trx?.items ?? [])" :key="index">
                            <div class="vx-modal-item">
                                <span><strong x-text="item.nama"></strong> × <span x-text="item.qty"></span></span>
                                <span class="shrink-0 font-medium text-slate-700" x-text="item.subtotal"></span>
                            </div>
                        </template>
                    </div>

                    <template x-if="(trx?.splits ?? []).length > 0">
                        <div class="mt-4 pt-4 border-t border-[var(--vx-border-soft)]">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Rincian pembayaran</p>
                            <div class="vx-modal-items">
                                <template x-for="(split, index) in trx.splits" :key="index">
                                    <div class="vx-modal-item">
                                        <span x-text="split.metode"></span>
                                        <span class="shrink-0 font-medium text-slate-700" x-text="split.jumlah"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="vx-modal-foot">
                        <div class="vx-modal-foot-row is-total">
                            <span>Total</span>
                            <span x-text="trx?.total"></span>
                        </div>
                        <div class="vx-modal-foot-row">
                            <span class="text-slate-500">Bayar</span>
                            <span class="font-medium" x-text="trx?.bayar"></span>
                        </div>
                        <div class="vx-modal-foot-row">
                            <span class="text-slate-500">Kembalian</span>
                            <span class="font-medium" x-text="trx?.kembalian"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </div>
@endsection
