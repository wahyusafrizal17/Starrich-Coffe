@extends('layouts.admin')

@section('title', 'Produk')

@section('page_header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Produk</h1>
            <p class="text-sm text-slate-500">Kelola katalog dan stok</p>
        </div>
        <a
            href="{{ route('admin.products.create') }}"
            class="inline-flex h-12 items-center rounded-2xl bg-teal-700 px-5 text-sm font-semibold text-white shadow-md shadow-teal-700/25"
        >
            Tambah produk
        </a>
    </div>
@endsection

@section('content')
    <form method="GET" class="mb-6 flex flex-col gap-3 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        <div class="flex-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                class="mt-1 block w-full rounded-2xl border-slate-200 text-sm"
                placeholder="Nama produk…"
            />
        </div>
        <div class="sm:w-56">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</label>
            <select name="kategori_id" class="mt-1 block w-full rounded-2xl border-slate-200 text-sm">
                <option value="">Semua</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('kategori_id') == $c->id)>{{ $c->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="h-11 rounded-2xl bg-slate-900 px-5 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Gambar</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-right">Harga</th>
                        <th class="px-5 py-3 text-right">Stok</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($products as $p)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-3">
                                @if ($p->gambar)
                                    <img src="{{ $p->imageUrl() }}" alt="" class="h-12 w-12 rounded-xl object-cover" />
                                @else
                                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-lg">📷</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $p->nama_produk }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $p->category->nama_kategori }}</td>
                            <td class="px-5 py-4 text-right font-medium">{{ format_rupiah($p->harga) }}</td>
                            <td class="px-5 py-4 text-right">{{ $p->stok }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.products.edit', $p) }}" class="text-teal-700 hover:underline">Edit</a>
                                <form
                                    action="{{ route('admin.products.destroy', $p) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Hapus produk ini?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $products->links() }}</div>
    </div>
@endsection
