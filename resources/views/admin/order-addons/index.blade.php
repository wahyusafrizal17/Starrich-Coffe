@extends('layouts.admin')

@section('title', 'Tambahan pesanan')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = [
            'filter_nama' => $columnFilters['filter_nama'],
            'filter_status' => $columnFilters['filter_status'],
            'page' => request('page'),
        ];
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            editKode: @js($modalAddon?->kode),
            deleteAddon: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(addon) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/order-addons')).replace(/\/$/, '') + '/' + addon.id;
                form.querySelector('#edit_label').value = addon.label;
                form.querySelector('#edit_harga').value = addon.harga;
                form.querySelector('#edit_urutan').value = addon.urutan;
                form.querySelector('#edit_is_active').checked = addon.is_active;
                this.editKode = addon.kode;
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(addon) {
                this.deleteAddon = addon;
                this.$refs.deleteForm.action = @js(url('admin/order-addons')).replace(/\/$/, '') + '/' + addon.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteAddon = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15M6 9.75h.008v.008H6V9.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 6h.008v.008H6.375v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Opsi sesuai filter' : 'Total opsi' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredTotal : $catalogTotal, 0, ',', '.') }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ number_format($catalogTotal, 0, ',', '.') }} opsi tambahan</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-success">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Opsi aktif</p>
                    <p class="vx-stat-value">{{ number_format($activeCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Tambahan pesanan</h2>
                    <p>Opsi tambahan di kasir untuk menu minuman/kopi</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.order-addons.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah opsi
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.order-addons.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th class="vx-text-end">Harga</th>
                                <th>Status</th>
                                <th class="vx-text-end">Aksi</th>
                            </tr>
                            <tr class="vx-table-filter-row">
                                <th></th>
                                <th colspan="2">
                                    <input
                                        type="search"
                                        name="filter_nama"
                                        value="{{ $columnFilters['filter_nama'] }}"
                                        placeholder="Cari nama atau kode…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th></th>
                                <th>
                                    <select name="filter_status" class="vx-table-col-filter" onchange="this.form.submit()">
                                        <option value="">Semua status</option>
                                        <option value="active" @selected($columnFilters['filter_status'] === 'active')>Aktif</option>
                                        <option value="inactive" @selected($columnFilters['filter_status'] === 'inactive')>Nonaktif</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($addons as $addon)
                                @php
                                    $addonPayload = [
                                        'id' => $addon->id,
                                        'kode' => $addon->kode,
                                        'label' => $addon->label,
                                        'harga' => $addon->harga,
                                        'urutan' => $addon->urutan,
                                        'is_active' => $addon->is_active,
                                        'can_delete' => ! in_array($addon->kode, $usedKodes, true),
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-sm text-slate-600">{{ $addon->urutan }}</td>
                                    <td>
                                        <span class="font-mono text-xs font-semibold text-slate-700">{{ $addon->kode }}</span>
                                    </td>
                                    <td>
                                        <p class="font-semibold text-slate-900">{{ $addon->label }}</p>
                                        <p class="text-xs text-slate-500">ID #{{ $addon->id }}</p>
                                    </td>
                                    <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($addon->harga) }}</td>
                                    <td>
                                        @if ($addon->is_active)
                                            <span class="vx-badge vx-badge-success">Aktif</span>
                                        @else
                                            <span class="vx-badge vx-badge-slate">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                title="Edit"
                                                aria-label="Edit"
                                                @click="openEdit(@js($addonPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                title="Hapus"
                                                aria-label="Hapus"
                                                @click="openDelete(@js($addonPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                        {{ $hasActiveFilters ? 'Tidak ada opsi sesuai filter.' : 'Belum ada tambahan.' }}
                                        @if (! $hasActiveFilters)
                                            <button type="button" class="ml-1 font-semibold text-blue-700 hover:underline" @click="openCreate()">Tambah opsi</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $addons->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="addon-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="addon-create-title">Tambah opsi</h3>
                            <p>Opsi tambahan untuk menu minuman/kopi di kasir</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.order-addons.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.order-addons._form-fields', ['prefix' => 'create_'])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
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
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="addon-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="addon-edit-title">Edit opsi</h3>
                            <p>Perbarui tambahan pesanan di kasir</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalAddon ? route('admin.order-addons.update', $modalAddon) : '' }}"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.order-addons._form-fields', [
                                'prefix' => 'edit_',
                                'orderAddon' => $modalAddon,
                                'isEdit' => true,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="addon-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="addon-delete-title">Hapus opsi</h3>
                            <p x-text="deleteAddon ? deleteAddon.label : ''"></p>
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
                                Opsi yang sudah dipakai di transaksi tidak dapat dihapus. Nonaktifkan saja jika tidak ingin menampilkannya di kasir.
                            </p>
                            <p
                                class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"
                                x-show="deleteAddon && !deleteAddon.can_delete"
                                x-cloak
                            >
                                Opsi <strong x-text="deleteAddon ? deleteAddon.kode : ''"></strong> sudah pernah dipakai di transaksi.
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button x-bind:disabled="deleteAddon && !deleteAddon.can_delete">
                                Hapus opsi
                            </x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
