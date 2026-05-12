@extends('layouts.admin')

@section('title', 'Produk')

@section('page_header')
    <div>
        <h1>Produk</h1>
        <p>Kelola katalog menu kasir.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="vx-btn vx-btn-primary">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah produk
    </a>
@endsection

@section('content')
    <div class="vx-card vx-card-pad-sm mb-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="vx-label" for="q">Cari produk</label>
                <input
                    id="q"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    class="vx-input"
                    placeholder="Nama produk…"
                />
            </div>
            <div class="sm:w-56">
                <label class="vx-label" for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="vx-select">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('kategori_id') == $c->id)>{{ $c->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="vx-btn vx-btn-soft">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                Terapkan
            </button>
        </form>
    </div>

    <div class="vx-table-wrap">
        <div class="overflow-x-auto">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th class="vx-text-end">Harga</th>
                        <th class="vx-text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if ($p->gambar)
                                        <img src="{{ $p->imageUrl() }}" alt="" class="vx-thumb" />
                                    @else
                                        <span class="vx-thumb-placeholder" aria-hidden="true">📷</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $p->nama_produk }}</p>
                                        <p class="text-xs text-slate-500">ID #{{ $p->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="vx-badge vx-badge-slate">{{ $p->category->nama_kategori ?? '—' }}</span>
                            </td>
                            <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($p->harga) }}</td>
                            <td class="vx-text-end">
                                <div class="vx-table-actions justify-end">
                                    <a href="{{ route('admin.products.edit', $p) }}" class="vx-btn-icon" aria-label="Edit produk" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="vx-btn-icon is-danger" aria-label="Hapus produk" title="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-sm text-slate-500 py-10">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="vx-table-foot">{{ $products->links() }}</div>
    </div>
@endsection
