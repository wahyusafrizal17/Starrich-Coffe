@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.reports.index') }}">Laporan</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Laba Rugi</span>
@endsection

@section('page_header')
    <div>
        <h1>Laporan Laba Rugi</h1>
        <p>Ringkasan pendapatan, beban operasional, dan laba bersih.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.index', request()->only(['from','to'])) }}" class="vx-btn vx-btn-ghost">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 9 7.5l3.75 3.75L21 4.5"/></svg>
            Laporan penjualan
        </a>
        <button type="button" class="vx-btn vx-btn-primary" onclick="window.print()">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
            Cetak
        </button>
    </div>
@endsection

@section('content')
    <div class="vx-card vx-card-pad-sm mb-5 print:hidden">
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

    <div class="grid gap-4 mb-5 md:grid-cols-3">
        <div class="vx-stat">
            <span class="vx-stat-icon vx-bg-success">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Pendapatan</p>
                <p class="vx-stat-value">{{ format_rupiah($revenue) }}</p>
            </div>
        </div>
        <div class="vx-stat">
            <span class="vx-stat-icon vx-bg-warning">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Total beban</p>
                <p class="vx-stat-value">{{ format_rupiah($operatingCost) }}</p>
            </div>
        </div>
        <div class="vx-stat">
            <span class="vx-stat-icon {{ $netIncome >= 0 ? 'vx-bg-primary' : 'vx-bg-danger' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 9 7.5l3.75 3.75L21 4.5M21 4.5h-4.5M21 4.5V9"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Laba bersih</p>
                <p class="vx-stat-value {{ $netIncome >= 0 ? 'text-slate-900' : 'text-red-600' }}">{{ format_rupiah($netIncome) }}</p>
            </div>
        </div>
    </div>

    <div class="vx-card vx-card-pad">
        <header class="mb-4 flex flex-col gap-1 border-b border-slate-200 pb-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Laporan Laba Rugi</h2>
                <p class="text-xs text-slate-500">Periode {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</p>
            </div>
            <p class="text-xs text-slate-500">{{ config('app.name', 'Starrich') }}</p>
        </header>

        <div class="overflow-x-auto">
            <table class="vx-table">
                <tbody>
                    <tr class="border-b-2 border-slate-200">
                        <th class="text-left text-xs uppercase tracking-wider text-slate-500" colspan="2">Pendapatan</th>
                    </tr>
                    <tr>
                        <td>Penjualan kasir</td>
                        <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($revenue) }}</td>
                    </tr>
                    <tr class="border-b border-slate-200">
                        <td class="font-semibold text-slate-900">Total pendapatan</td>
                        <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($revenue) }}</td>
                    </tr>

                    <tr class="border-b-2 border-slate-200">
                        <th class="pt-6 text-left text-xs uppercase tracking-wider text-slate-500" colspan="2">Beban Operasional</th>
                    </tr>
                    @foreach ($expenseLines as $key => $line)
                        <tr>
                            <td class="text-slate-700">{{ $line['label'] }}</td>
                            <td class="vx-text-end">{{ format_rupiah($line['total']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-b border-slate-200">
                        <td class="font-semibold text-slate-900">Subtotal beban operasional</td>
                        <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($totalExpenses) }}</td>
                    </tr>

                    <tr class="border-b-2 border-slate-200">
                        <th class="pt-6 text-left text-xs uppercase tracking-wider text-slate-500" colspan="2">Depresiasi Peralatan</th>
                    </tr>
                    @forelse ($depreciationDetails as $row)
                        <tr>
                            <td class="text-slate-700">
                                {{ $row['asset']->nama }}
                                <span class="ml-1 text-xs text-slate-500">(Rp {{ number_format($row['monthly'], 0, ',', '.') }} / bln)</span>
                            </td>
                            <td class="vx-text-end">{{ format_rupiah($row['period']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-slate-500" colspan="2">Belum ada aset terdaftar.</td>
                        </tr>
                    @endforelse
                    <tr class="border-b border-slate-200">
                        <td class="font-semibold text-slate-900">Subtotal depresiasi</td>
                        <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($totalDepreciation) }}</td>
                    </tr>

                    <tr class="border-b border-slate-200">
                        <td class="pt-4 font-semibold text-slate-900">Total beban</td>
                        <td class="pt-4 vx-text-end font-semibold text-slate-900">{{ format_rupiah($operatingCost) }}</td>
                    </tr>

                    <tr>
                        <td class="pt-4 text-base font-bold text-slate-900">Laba bersih</td>
                        <td class="pt-4 vx-text-end text-base font-bold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ format_rupiah($netIncome) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            .vx-sidebar, .vx-topbar, .vx-page-head .vx-btn, .vx-page-head a, .vx-btn { display: none !important; }
            .vx-app { background: #fff !important; }
            main { padding: 0 !important; overflow: visible !important; }
            body { overflow: visible !important; }
        }
    </style>
@endsection
