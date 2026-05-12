@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page_header')
    <div>
        <h1>Selamat datang, {{ auth()->user()->name }} 👋</h1>
        <p>Ringkasan singkat penjualan dan performa toko hari ini.</p>
    </div>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="vx-stat">
            <span class="vx-stat-icon vx-bg-success">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .896-3 2s1.343 2 3 2 3 .896 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Penjualan hari ini</p>
                <p class="vx-stat-value">{{ format_rupiah($todayTotal) }}</p>
            </div>
        </div>

        <div class="vx-stat">
            <span class="vx-stat-icon vx-bg-primary">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M3.75 6.75A1.5 1.5 0 0 1 5.25 5.25h13.5A1.5 1.5 0 0 1 20.25 6.75v10.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V6.75ZM7.5 11.25h.008v.008H7.5v-.008Z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Transaksi hari ini</p>
                <p class="vx-stat-value">{{ $todayCount }}</p>
            </div>
        </div>

        <div class="vx-stat">
            <span class="vx-stat-icon vx-bg-info">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 9 7.5l3.75 3.75L21 4.5M21 4.5h-4.5M21 4.5V9m0 10.5H3"/></svg>
            </span>
            <div class="min-w-0">
                <p class="vx-stat-label">Pendapatan bulan ini</p>
                <p class="vx-stat-value">{{ format_rupiah($monthlyTotal) }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="vx-card lg:col-span-2">
            <div class="vx-card-head">
                <div>
                    <h2>Produk terlaris</h2>
                    <p>Berdasarkan jumlah unit terjual sepanjang waktu.</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                    Lihat produk
                </a>
            </div>
            <ul class="divide-y divide-[var(--vx-border-soft)]">
                @forelse ($topProducts as $row)
                    <li class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="vx-thumb-placeholder" aria-hidden="true">
                                {{ \Illuminate\Support\Str::of($row->product->nama_produk ?? '—')->substr(0, 1)->upper() }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $row->product->nama_produk ?? '—' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $row->product->category->nama_kategori ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <span class="vx-badge vx-badge-primary">{{ $row->qty_sold }} terjual</span>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center text-sm text-slate-500">Belum ada data penjualan.</li>
                @endforelse
            </ul>
        </div>

        <div class="vx-card vx-card-pad space-y-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Akses cepat</h2>
                <p class="text-xs text-slate-500">Jalan pintas untuk kebutuhan harian.</p>
            </div>
            <a href="{{ route('cashier.index') }}" class="vx-btn vx-btn-primary w-full">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.5l1.05 4.2M6 16.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm12 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-12-3h12.36a1.5 1.5 0 0 0 1.47-1.2L21.75 6H4.8"/></svg>
                Buka kasir
            </a>
            <a href="{{ route('admin.products.create') }}" class="vx-btn vx-btn-soft w-full">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah produk
            </a>
            <a href="{{ route('admin.reports.index') }}" class="vx-btn vx-btn-ghost w-full">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 9 7.5l3.75 3.75L21 4.5M21 4.5h-4.5M21 4.5V9m0 10.5H3"/></svg>
                Lihat laporan
            </a>
        </div>
    </div>
@endsection
