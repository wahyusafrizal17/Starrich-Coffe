@extends('layouts.admin')

@section('title', 'Aset & Peralatan')

@section('page_header')
    <div>
        <h1>Aset & Peralatan</h1>
        <p>Daftar peralatan & perlengkapan toko.</p>
    </div>
    <a href="{{ route('admin.assets.create') }}" class="vx-btn vx-btn-primary">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah aset
    </a>
@endsection

@section('content')
    <div class="vx-stat mb-5">
        <span class="vx-stat-icon vx-bg-info">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5L12 12 3.75 7.5M12 12v9.75M20.25 7.5v9L12 21.75 3.75 16.5v-9L12 3l8.25 4.5Z"/></svg>
        </span>
        <div class="min-w-0">
            <p class="vx-stat-label">Total nilai perolehan</p>
            <p class="vx-stat-value">{{ format_rupiah($totalCost) }}</p>
        </div>
    </div>

    <div class="vx-table-wrap">
        <div class="overflow-x-auto">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Nama aset</th>
                        <th>Tanggal perolehan</th>
                        <th class="vx-text-end">Harga</th>
                        <th class="vx-text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>
                                <p class="text-sm font-semibold text-slate-900">{{ $asset->nama }}</p>
                                @if ($asset->catatan)
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($asset->catatan, 60) }}</p>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $asset->tanggal_perolehan->format('d M Y') }}</td>
                            <td class="vx-text-end">{{ format_rupiah($asset->harga_perolehan) }}</td>
                            <td class="vx-text-end">
                                <div class="vx-table-actions justify-end">
                                    <a href="{{ route('admin.assets.edit', $asset) }}" class="vx-btn-icon" aria-label="Edit aset" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="vx-btn-icon is-danger" aria-label="Hapus aset" title="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-sm text-slate-500 py-10">Belum ada aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="vx-table-foot">{{ $assets->links() }}</div>
    </div>
@endsection
