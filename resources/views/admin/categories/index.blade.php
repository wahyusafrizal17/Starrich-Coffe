@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = [
            'filter_nama' => $columnFilters['filter_nama'],
            'page' => request('page'),
        ];
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            deleteCategory: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(category) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/categories')).replace(/\/$/, '') + '/' + category.id;
                form.querySelector('#edit_nama_kategori').value = category.nama_kategori;
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(category) {
                this.deleteCategory = category;
                this.$refs.deleteForm.action = @js(url('admin/categories')).replace(/\/$/, '') + '/' + category.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteCategory = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-violet">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Kategori sesuai filter' : 'Total kategori' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredTotal : $catalogTotal, 0, ',', '.') }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ number_format($catalogTotal, 0, ',', '.') }} kategori</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5a1.125 1.125 0 0 0-1.125-1.125H3.375a1.125 1.125 0 0 0-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Total produk</p>
                    <p class="vx-stat-value">{{ number_format($productTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Daftar kategori</h2>
                    <p>Kelompokkan produk untuk pengalaman kasir yang rapi</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.categories.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah kategori
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.categories.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Nama kategori</th>
                                <th class="vx-text-end">Jumlah produk</th>
                                <th class="vx-text-end">Aksi</th>
                            </tr>
                            <tr class="vx-table-filter-row">
                                <th>
                                    <input
                                        type="search"
                                        name="filter_nama"
                                        value="{{ $columnFilters['filter_nama'] }}"
                                        placeholder="Cari kategori…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $cat)
                                @php
                                    $categoryPayload = [
                                        'id' => $cat->id,
                                        'nama_kategori' => $cat->nama_kategori,
                                        'products_count' => $cat->products_count,
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="vx-thumb-placeholder" aria-hidden="true">
                                                {{ \Illuminate\Support\Str::of($cat->nama_kategori)->substr(0, 1)->upper() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900">{{ $cat->nama_kategori }}</p>
                                                <p class="text-xs text-slate-500">ID #{{ $cat->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="vx-text-end">
                                        <span class="vx-badge vx-badge-primary">{{ $cat->products_count }}</span>
                                    </td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                aria-label="Edit kategori"
                                                title="Edit"
                                                @click="openEdit(@js($categoryPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                aria-label="Hapus kategori"
                                                title="Hapus"
                                                @click="openDelete(@js($categoryPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-sm text-slate-500 py-10">
                                        {{ $hasActiveFilters ? 'Tidak ada kategori sesuai filter.' : 'Belum ada kategori.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $categories->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="category-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="category-create-title">Tambah kategori</h3>
                            <p>Buat kelompok produk baru untuk kasir</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.categories._form-fields', ['prefix' => 'create_'])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan kategori</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="category-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="category-edit-title">Edit kategori</h3>
                            <p>Perbarui nama kelompok produk</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalCategory ? route('admin.categories.update', $modalCategory) : '' }}"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.categories._form-fields', [
                                'prefix' => 'edit_',
                                'category' => $modalCategory,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui kategori</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="category-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="category-delete-title">Hapus kategori</h3>
                            <p x-text="deleteCategory ? deleteCategory.nama_kategori : ''"></p>
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
                        <div class="vx-modal-body space-y-3">
                            <p class="text-sm text-slate-600">
                                Kategori yang masih memiliki produk tidak dapat dihapus.
                                Yakin ingin menghapus kategori ini?
                            </p>
                            <p
                                class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"
                                x-show="deleteCategory && deleteCategory.products_count > 0"
                                x-cloak
                            >
                                Kategori ini masih memiliki
                                <strong x-text="deleteCategory ? deleteCategory.products_count : 0"></strong>
                                produk. Pindahkan atau hapus produk terlebih dahulu.
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button x-bind:disabled="deleteCategory && deleteCategory.products_count > 0">
                                Hapus kategori
                            </x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
