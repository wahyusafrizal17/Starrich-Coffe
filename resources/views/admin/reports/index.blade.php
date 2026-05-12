@extends('layouts.admin')

@section('title', 'Laporan')

@section('page_header')
    <div>
        <h1>Laporan penjualan</h1>
        <p>Pantau transaksi berdasarkan rentang tanggal.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.profit-loss', request()->only(['from','to'])) }}" class="vx-btn vx-btn-ghost">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.5 4.5L21.75 7.5M21.75 7.5H16.5M21.75 7.5V12.75"/></svg>
            Laba rugi
        </a>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="vx-btn vx-btn-primary">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
            Export CSV
        </a>
    </div>
@endsection

@section('content')
    <div class="vx-card vx-card-pad-sm mb-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label class="vx-label" for="from">Dari</label>
                <input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="vx-input" />
            </div>
            <div>
                <label class="vx-label" for="to">Sampai</label>
                <input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="vx-input" />
            </div>
            <button type="submit" class="vx-btn vx-btn-primary">Terapkan</button>
        </form>
    </div>

    <div class="vx-stat mb-5">
        <span class="vx-stat-icon vx-bg-success">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.8 2.1c.72.2 1.45-.34 1.45-1.09v-1.01M3.75 4.5v.75A60.07 60.07 0 0 1 18 7.5m0 0v.75a60.07 60.07 0 0 0 2.25.18M18 12.75v.75a60.07 60.07 0 0 1-15.8 2.1c-.72.2-1.45-.34-1.45-1.09V13.5"/></svg>
        </span>
        <div class="min-w-0">
            <p class="vx-stat-label">Total penjualan (periode)</p>
            <p class="vx-stat-value">{{ format_rupiah($sumTotal) }}</p>
        </div>
    </div>

    <div class="vx-table-wrap">
        <div class="overflow-x-auto">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th class="vx-text-end">Total</th>
                        <th class="vx-text-end">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td class="font-mono text-xs text-slate-500">#{{ $t->id }}</td>
                            <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                            <td class="text-slate-600">{{ $t->user->name ?? '—' }}</td>
                            <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($t->total) }}</td>
                            <td class="vx-text-end">
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer list-none text-sm font-semibold text-blue-700 hover:underline [&::-webkit-details-marker]:hidden">
                                        Rincian
                                    </summary>
                                    <div class="mt-2 min-w-[240px] space-y-1 rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-left text-xs text-slate-600">
                                        @foreach ($t->details as $d)
                                            <div class="flex justify-between gap-4">
                                                <span>{{ $d->product->nama_produk ?? '—' }} × {{ $d->qty }}</span>
                                                <span class="shrink-0">{{ format_rupiah($d->subtotal) }}</span>
                                            </div>
                                        @endforeach
                                        <div class="mt-2 flex justify-between border-t border-slate-200 pt-2 font-semibold text-slate-900">
                                            <span>Bayar / Kembali</span>
                                            <span class="shrink-0 text-right">{{ format_rupiah($t->bayar) }} / {{ format_rupiah($t->kembalian) }}</span>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-sm text-slate-500 py-10">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="vx-table-foot">{{ $transactions->links() }}</div>
    </div>
@endsection
