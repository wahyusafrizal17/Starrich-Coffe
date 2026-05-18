<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #{{ $transaction->id }} — {{ config('app.name', 'Starrich') }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #f1f5f9; color: #0f172a; font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .wrap { display: flex; justify-content: center; padding: 24px; }
        .receipt {
            width: 320px;
            background: #fff;
            padding: 20px 18px;
            font-family: 'Menlo', 'Consolas', ui-monospace, monospace;
            font-size: 12px;
            color: #111827;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        }
        .center { text-align: center; }
        .brand-logo {
            display: block;
            margin: 0 auto 6px;
            height: 44px;
            width: auto;
            max-width: 80%;
            object-fit: contain;
        }
        .brand { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 16px; letter-spacing: 0.5px; }
        .muted { color: #6b7280; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .sep { border-top: 1px dashed #9ca3af; margin: 10px 0; }
        .items { margin: 8px 0; }
        .item { margin-bottom: 6px; }
        .item-name { font-weight: 600; }
        .item-line { display: flex; justify-content: space-between; }
        .totals .row { margin: 2px 0; }
        .totals .grand { font-weight: 700; font-size: 14px; padding-top: 4px; border-top: 1px dashed #9ca3af; margin-top: 6px; }
        .footer { margin-top: 12px; text-align: center; color: #4b5563; font-size: 11px; }
        .actions { margin-top: 14px; display: flex; gap: 8px; }
        .actions button, .actions a {
            flex: 1;
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 12px; border-radius: 10px; font: inherit; font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
            border: 1px solid transparent;
        }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-close { background: #fff; color: #1f2937; border-color: #e5e7eb; }
        .btn-close:hover { background: #f3f4f6; }

        @media print {
            html, body { background: #fff; }
            .wrap { padding: 0; }
            .receipt { box-shadow: none; width: 80mm; padding: 6mm 4mm; border-radius: 0; }
            .actions { display: none !important; }
            @page { margin: 4mm; size: 80mm auto; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="receipt" id="receipt">
            <div class="center">
                <img
                    src="{{ asset('images/logo/logo.png') }}"
                    alt="{{ config('app.name', 'Starrich') }}"
                    class="brand-logo"
                    decoding="async"
                />
            </div>
            <div class="sep"></div>
            <div class="row"><span class="muted">No. Transaksi</span><strong>#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
            <div class="row"><span class="muted">Tanggal</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="row"><span class="muted">Kasir</span><span>{{ $transaction->user?->name ?? '—' }}</span></div>
            <div class="sep"></div>

            <div class="items">
                @foreach ($transaction->details as $d)
                    <div class="item">
                        <div class="item-name">{{ $d->product->nama_produk ?? 'Produk' }}</div>
                        <div class="item-line">
                            <span>{{ $d->qty }} x {{ number_format($d->harga, 0, ',', '.') }}</span>
                            <span>{{ number_format($d->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="totals">
                <div class="row"><span>Subtotal</span><span>{{ number_format($transaction->total, 0, ',', '.') }}</span></div>
                <div class="row grand"><span>TOTAL</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
                @if ($transaction->isOpen())
                    <div class="row" style="margin-top:8px;font-weight:700;color:#b45309">
                        <span>Status</span><span>BELUM LUNAS (OPEN BILL)</span>
                    </div>
                @else
                    <div class="row"><span>Bayar</span><span>Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</span></div>
                    <div class="row"><span>Kembalian</span><span>Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span></div>
                    @if (is_array($transaction->payment_splits) && count($transaction->payment_splits) > 0)
                    <div class="sep"></div>
                    <div class="muted" style="font-size:11px; margin-bottom:4px;">Metode pembayaran</div>
                    @foreach ($transaction->payment_splits as $split)
                        <div class="row">
                            <span>{{ ucfirst($split['metode'] ?? '—') }}</span>
                            <span>Rp {{ number_format((int) ($split['jumlah'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    @endif
                @endif
            </div>

            <div class="sep"></div>
            <div class="footer">
                Terima kasih sudah ngopi di {{ config('app.name', 'Starrich') }}!<br>
                Sampai jumpa lagi.
            </div>

            <div class="actions">
                <button type="button" class="btn-close" onclick="window.close()">Tutup</button>
                <button type="button" class="btn-print" onclick="window.print()">Cetak</button>
            </div>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        if (params.get('print') === '1') {
            window.addEventListener('load', () => {
                const imgs = Array.from(document.images).filter((i) => ! i.complete);
                Promise.all(imgs.map((img) => new Promise((resolve) => {
                    img.addEventListener('load', resolve, { once: true });
                    img.addEventListener('error', resolve, { once: true });
                }))).then(() => setTimeout(() => window.print(), 300));
            });
        }
    </script>
</body>
</html>
