@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.reports.index', $filterQuery) }}">Laporan</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Laba Rugi</span>
@endsection

@section('content')
    <div class="print:hidden">
        @include('partials.dashboard-filter-strip', [
            'period' => $period,
            'filterAction' => route('admin.reports.profit-loss'),
        ])
    </div>

    <div class="vx-hero-strip vx-hero-strip--pl mb-5 print:hidden">
        <div>
            <p class="vx-hero-eyebrow">{{ $periodTitle }}</p>
            <p class="vx-hero-value @if($netIncome < 0) is-negative @endif">{{ format_rupiah($netIncome) }}</p>
            <p class="vx-hero-sub">Laba bersih · {{ $periodLabel }}</p>
        </div>
        <div class="vx-hero-stats is-cols-6">
            <div class="vx-hero-stat">
                <label>Penjualan</label>
                <strong class="is-green">{{ format_rupiah($salesRevenue) }}</strong>
            </div>
            <div class="vx-hero-stat">
                <label>Pemasukan / modal</label>
                <strong class="is-blue">{{ format_rupiah($totalInflows) }}</strong>
            </div>
            <div class="vx-hero-stat">
                <label>Beban operasional</label>
                <strong class="is-amber">{{ format_rupiah($totalExpenses) }}</strong>
            </div>
            <div class="vx-hero-stat">
                <label>Depresiasi</label>
                <strong>{{ format_rupiah($totalDepreciation) }}</strong>
            </div>
            <div class="vx-hero-stat">
                <label>Total beban</label>
                <strong class="is-amber">{{ format_rupiah($operatingCost) }}</strong>
            </div>
            <div class="vx-hero-stat">
                <label>Pembagian 30%</label>
                <strong class="@if($profitShare30 < 0) is-negative @else is-green @endif">{{ format_rupiah($profitShare30) }}</strong>
            </div>
        </div>
    </div>

    <div class="vx-table-wrap">
        <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4 print:hidden">
            <div>
                <h2>Rincian laba rugi</h2>
                <p>Periode {{ $periodLabel }} · {{ config('app.name', 'Starrich') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" onclick="window.print()">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
                    Cetak
                </button>
            </div>
        </div>

        <div class="hidden print:block px-5 pt-5 pb-2">
            <h2 class="text-lg font-bold text-slate-900">Laporan Laba Rugi</h2>
            <p class="text-sm text-slate-500">Periode {{ $periodLabel }} · {{ config('app.name', 'Starrich') }}</p>
        </div>

        <div class="vx-pl-wrap overflow-x-auto">
            <table class="vx-pl-table">
                <tbody>
                    <tr class="vx-pl-section">
                        <th colspan="2">Pendapatan</th>
                    </tr>
                    <tr class="vx-pl-row">
                        <td>Penjualan kasir</td>
                        <td>{{ format_rupiah($salesRevenue) }}</td>
                    </tr>
                    @foreach ($inflowLines as $line)
                        @if ($line['total'] > 0)
                            <tr class="vx-pl-row">
                                <td>{{ $line['label'] }}</td>
                                <td>{{ format_rupiah($line['total']) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @if ($totalInflows > 0)
                        <tr class="vx-pl-subtotal">
                            <td>Subtotal pemasukan / modal</td>
                            <td>{{ format_rupiah($totalInflows) }}</td>
                        </tr>
                    @endif
                    <tr class="vx-pl-subtotal">
                        <td>Total pendapatan</td>
                        <td>{{ format_rupiah($totalRevenue) }}</td>
                    </tr>

                    <tr class="vx-pl-section is-spaced">
                        <th colspan="2">Beban operasional</th>
                    </tr>
                    @foreach ($expenseLines as $line)
                        <tr class="vx-pl-row">
                            <td>{{ $line['label'] }}</td>
                            <td>{{ format_rupiah($line['total']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="vx-pl-subtotal">
                        <td>Subtotal beban operasional</td>
                        <td>{{ format_rupiah($totalExpenses) }}</td>
                    </tr>

                    <tr class="vx-pl-section is-spaced">
                        <th colspan="2">Depresiasi peralatan</th>
                    </tr>
                    @forelse ($depreciationDetails as $row)
                        <tr class="vx-pl-row">
                            <td>
                                {{ $row['asset']->nama }}
                                <span class="vx-pl-note">Rp {{ number_format($row['monthly'], 0, ',', '.') }} / bln</span>
                            </td>
                            <td>{{ format_rupiah($row['period']) }}</td>
                        </tr>
                    @empty
                        <tr class="vx-pl-row">
                            <td colspan="2" class="text-slate-500">Belum ada aset terdaftar.</td>
                        </tr>
                    @endforelse
                    <tr class="vx-pl-subtotal">
                        <td>Subtotal depresiasi</td>
                        <td>{{ format_rupiah($totalDepreciation) }}</td>
                    </tr>

                    <tr class="vx-pl-total">
                        <td>Total beban</td>
                        <td>{{ format_rupiah($operatingCost) }}</td>
                    </tr>

                    <tr class="vx-pl-net @if($netIncome < 0) is-negative @endif">
                        <td>Laba bersih</td>
                        <td class="{{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ format_rupiah($netIncome) }}
                        </td>
                    </tr>

                    <tr class="vx-pl-share @if($profitShare30 < 0) is-negative @endif">
                        <td>Pembagian 30%</td>
                        <td class="{{ $profitShare30 >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ format_rupiah($profitShare30) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
