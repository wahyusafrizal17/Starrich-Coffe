@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('page_header')
    <div>
        <h1>Pengeluaran</h1>
        <p>Catat seluruh biaya operasional toko untuk laporan laba rugi.</p>
    </div>
    <a href="{{ route('admin.expenses.create') }}" class="vx-btn vx-btn-primary">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah pengeluaran
    </a>
@endsection

@section('content')
    <div class="vx-card vx-card-pad-sm mb-5">
        <form method="GET" class="grid gap-3 sm:grid-cols-4 sm:items-end">
            <div>
                <label class="vx-label" for="from">Dari</label>
                <input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="vx-input" />
            </div>
            <div>
                <label class="vx-label" for="to">Sampai</label>
                <input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="vx-input" />
            </div>
            <div>
                <label class="vx-label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="vx-select">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" @selected($kategori === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="vx-btn vx-btn-primary">Terapkan</button>
        </form>
    </div>

    <div class="vx-stat mb-5">
        <span class="vx-stat-icon vx-bg-warning">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
        </span>
        <div class="min-w-0">
            <p class="vx-stat-label">Total pengeluaran (periode)</p>
            <p class="vx-stat-value">{{ format_rupiah($sumTotal) }}</p>
        </div>
    </div>

    <div class="vx-table-wrap">
        <div class="overflow-x-auto">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Dicatat oleh</th>
                        <th class="vx-text-end">Jumlah</th>
                        <th class="vx-text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $e)
                        <tr>
                            <td class="text-slate-600">{{ $e->tanggal->format('d M Y') }}</td>
                            <td>
                                <span class="vx-badge vx-badge-primary">{{ $e->kategori_label }}</span>
                            </td>
                            <td>
                                <p class="text-sm font-semibold text-slate-900">{{ $e->nama }}</p>
                                @if ($e->catatan)
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($e->catatan, 80) }}</p>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $e->user?->name ?? '—' }}</td>
                            <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($e->jumlah) }}</td>
                            <td class="vx-text-end">
                                <div class="vx-table-actions justify-end">
                                    <a href="{{ route('admin.expenses.edit', $e) }}" class="vx-btn-icon" aria-label="Edit pengeluaran" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengeluaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="vx-btn-icon is-danger" aria-label="Hapus pengeluaran" title="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-sm text-slate-500 py-10">Belum ada pengeluaran pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="vx-table-foot">{{ $expenses->links() }}</div>
    </div>
@endsection
