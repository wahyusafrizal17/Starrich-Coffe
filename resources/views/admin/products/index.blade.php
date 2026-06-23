@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = [
            'filter_nama' => $columnFilters['filter_nama'],
            'filter_kategori' => $columnFilters['filter_kategori'],
            'page' => request('page'),
        ];
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            editImageUrl: @js($modalProduct?->imageUrl()),
            deleteProduct: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(product) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/products')).replace(/\/$/, '') + '/' + product.id;
                form.querySelector('#edit_nama_produk').value = product.nama_produk;
                form.querySelector('#edit_kategori_id').value = String(product.kategori_id);
                form.querySelector('#edit_harga').value = product.harga;
                form.querySelector('#edit_gambar').value = '';
                this.editImageUrl = product.gambar_url || null;
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(product) {
                this.deleteProduct = product;
                this.$refs.deleteForm.action = @js(url('admin/products')).replace(/\/$/, '') + '/' + product.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteProduct = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5a1.125 1.125 0 0 0-1.125-1.125H3.375a1.125 1.125 0 0 0-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Produk sesuai filter' : 'Total produk' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredTotal : $catalogTotal, 0, ',', '.') }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ number_format($catalogTotal, 0, ',', '.') }} produk di katalog</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-violet">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Kategori</p>
                    <p class="vx-stat-value">{{ number_format($categoryCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Katalog produk</h2>
                    <p>Kelola menu yang tampil di kasir</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.products.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah produk
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.products.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="vx-text-end">Harga</th>
                                <th class="vx-text-end">Aksi</th>
                            </tr>
                            <tr class="vx-table-filter-row">
                                <th>
                                    <input
                                        type="search"
                                        name="filter_nama"
                                        value="{{ $columnFilters['filter_nama'] }}"
                                        placeholder="Cari nama…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th>
                                    <select name="filter_kategori" class="vx-table-col-filter" onchange="this.form.submit()">
                                        <option value="">Semua kategori</option>
                                        @foreach ($categories as $c)
                                            <option value="{{ $c->id }}" @selected($columnFilters['filter_kategori'] == (string) $c->id)>
                                                {{ $c->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $p)
                                @php
                                    $productPayload = [
                                        'id' => $p->id,
                                        'nama_produk' => $p->nama_produk,
                                        'harga' => $p->harga,
                                        'kategori_id' => $p->kategori_id,
                                        'gambar_url' => $p->imageUrl(),
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if ($p->gambar)
                                                <img src="{{ $p->imageUrl() }}" alt="" class="vx-thumb" />
                                            @else
                                                <span class="vx-thumb-placeholder" aria-hidden="true">{{ \Illuminate\Support\Str::of($p->nama_produk)->substr(0, 1)->upper() }}</span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $p->nama_produk }}</p>
                                                <p class="text-xs text-slate-500">ID #{{ $p->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="vx-badge vx-badge-primary">{{ $p->category->nama_kategori ?? '—' }}</span>
                                    </td>
                                    <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($p->harga) }}</td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                aria-label="Edit produk"
                                                title="Edit"
                                                @click="openEdit(@js($productPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                aria-label="Hapus produk"
                                                title="Hapus"
                                                @click="openDelete(@js($productPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-sm text-slate-500 py-10">
                                        {{ $hasActiveFilters ? 'Tidak ada produk sesuai filter.' : 'Belum ada produk.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $products->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="product-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="product-create-title">Tambah produk</h3>
                            <p>Menu baru akan langsung tampil di kasir</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.products._form-fields', ['prefix' => 'create_'])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan produk</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div
                x-show="showEdit"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeEdit()"
                @click.self="closeEdit()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="product-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="product-edit-title">Edit produk</h3>
                            <p>Perbarui informasi menu di katalog</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalProduct ? route('admin.products.update', $modalProduct) : '' }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.products._form-fields', [
                                'prefix' => 'edit_',
                                'product' => $modalProduct,
                                'showCurrentImage' => true,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui produk</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div
                x-show="showDelete"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeDelete()"
                @click.self="closeDelete()"
            >
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="product-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="product-delete-title">Hapus produk</h3>
                            <p x-text="deleteProduct ? deleteProduct.nama_produk : ''"></p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeDelete()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form x-ref="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            <p class="text-sm text-slate-600">
                                Produk yang sudah pernah terjual tidak dapat dihapus agar laporan tetap akurat.
                                Yakin ingin menghapus produk ini?
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button>Hapus produk</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
