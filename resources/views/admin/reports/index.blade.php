@extends('layouts.admin')

@section('title', 'Laporan')

@section('page_header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Laporan penjualan</h1>
            <p class="text-sm text-slate-500">Filter berdasarkan tanggal</p>
        </div>
        <a
            href="{{ route('admin.reports.export', request()->query()) }}"
            class="inline-flex h-12 items-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-800 shadow-sm"
        >
            Export CSV
        </a>
    </div>
@endsection

@section('content')
    <form method="GET" class="mb-6 flex flex-col gap-3 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dari</label>
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="mt-1 block rounded-2xl border-slate-200 text-sm" />
        </div>
        <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sampai</label>
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="mt-1 block rounded-2xl border-slate-200 text-sm" />
        </div>
        <button type="submit" class="h-11 rounded-2xl bg-teal-700 px-5 text-sm font-semibold text-white">Terapkan</button>
    </form>

    <div class="mb-6 rounded-3xl border border-teal-100 bg-teal-50/80 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-teal-800">Total penjualan (periode)</p>
        <p class="mt-1 text-3xl font-bold text-teal-900">{{ format_rupiah($sumTotal) }}</p>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">ID</th>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Kasir</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($transactions as $t)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-4 font-mono text-xs text-slate-600">#{{ $t->id }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $t->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $t->user->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">{{ format_rupiah($t->total) }}</td>
                            <td class="px-5 py-4 text-right align-top">
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer list-none text-teal-700 hover:underline [&::-webkit-details-marker]:hidden">
                                        Rincian
                                    </summary>
                                    <div class="mt-2 min-w-[220px] space-y-1 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-left text-xs text-slate-600">
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
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $transactions->links() }}</div>
    </div>
@endsection
