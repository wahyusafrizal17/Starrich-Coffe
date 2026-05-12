@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page_header')
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
    <p class="mt-1 text-sm text-slate-500">Ringkasan penjualan dan performa toko</p>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Penjualan hari ini</p>
            <p class="mt-2 text-2xl font-bold text-teal-700">{{ format_rupiah($todayTotal) }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Transaksi hari ini</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $todayCount }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pendapatan bulan ini</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ format_rupiah($monthlyTotal) }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Produk terlaris</h2>
            <p class="text-sm text-slate-500">Berdasarkan jumlah unit terjual</p>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse ($topProducts as $row)
                <li class="flex items-center justify-between gap-4 px-5 py-4">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-slate-900">
                            {{ $row->product->nama_produk ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $row->product->category->nama_kategori ?? '' }}
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">
                        {{ $row->qty_sold }} terjual
                    </span>
                </li>
            @empty
                <li class="px-5 py-12 text-center text-sm text-slate-500">Belum ada data penjualan.</li>
            @endforelse
        </ul>
    </div>
@endsection
