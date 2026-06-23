@extends('layouts.admin')

@section('title', 'Aset & Peralatan')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = array_merge(
            $columnFilters,
            ['page' => request('page')],
        );
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            editDepresiasiLabel: @js($modalAsset ? format_rupiah($modalAsset->monthlyDepreciation()) : null),
            deleteAsset: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(asset) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/aset')).replace(/\/$/, '') + '/' + asset.id;
                form.querySelector('#edit_nama').value = asset.nama;
                form.querySelector('#edit_tanggal_perolehan').value = asset.tanggal_perolehan;
                form.querySelector('#edit_harga_perolehan').value = asset.harga_perolehan;
                form.querySelector('#edit_catatan').value = asset.catatan || '';
                this.editDepresiasiLabel = asset.depresiasi_label;
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(asset) {
                this.deleteAsset = asset;
                this.$refs.deleteForm.action = @js(url('admin/aset')).replace(/\/$/, '') + '/' + asset.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteAsset = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-info">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5L12 12 3.75 7.5M12 12v9.75M20.25 7.5v9L12 21.75 3.75 16.5v-9L12 3l8.25 4.5Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Nilai sesuai filter' : 'Total nilai perolehan' }}</p>
                    <p class="vx-stat-value">{{ format_rupiah($displayCost) }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ format_rupiah($totalCost) }} seluruh aset</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Aset sesuai filter' : 'Jumlah aset' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredCount : $catalogTotal, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="vx-stat sm:col-span-2 lg:col-span-1">
                <span class="vx-stat-icon vx-bg-warning">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Depresiasi / bulan</p>
                    <p class="vx-stat-value">{{ format_rupiah($monthlyDepreciationTotal) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Masuk laporan laba rugi</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Daftar aset</h2>
                    <p>Peralatan & perlengkapan toko untuk perhitungan depresiasi</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.assets.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <a href="{{ route('admin.reports.profit-loss') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.5 4.5L21.75 7.5M21.75 7.5H16.5M21.75 7.5V12.75"/></svg>
                        Laba rugi
                    </a>
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah aset
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.assets.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Nama aset</th>
                                <th>Tanggal perolehan</th>
                                <th class="vx-text-end">Harga</th>
                                <th class="vx-text-end">Depresiasi/bln</th>
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
                                    <input
                                        type="date"
                                        name="filter_tanggal"
                                        value="{{ $columnFilters['filter_tanggal'] }}"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th>
                                    <input
                                        type="text"
                                        name="filter_harga"
                                        value="{{ $columnFilters['filter_harga'] }}"
                                        placeholder="Nominal…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                @php
                                    $assetPayload = [
                                        'id' => $asset->id,
                                        'nama' => $asset->nama,
                                        'tanggal_perolehan' => $asset->tanggal_perolehan->format('Y-m-d'),
                                        'harga_perolehan' => $asset->harga_perolehan,
                                        'catatan' => $asset->catatan ?? '',
                                        'harga_label' => format_rupiah($asset->harga_perolehan),
                                        'depresiasi_label' => format_rupiah($asset->monthlyDepreciation()),
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="vx-thumb-placeholder" aria-hidden="true">
                                                {{ \Illuminate\Support\Str::of($asset->nama)->substr(0, 1)->upper() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900">{{ $asset->nama }}</p>
                                                <p class="text-xs text-slate-500">ID #{{ $asset->id }}</p>
                                                @if ($asset->catatan)
                                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($asset->catatan, 60) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-slate-600">{{ $asset->tanggal_perolehan->format('d M Y') }}</td>
                                    <td class="vx-text-end font-semibold text-slate-900">{{ format_rupiah($asset->harga_perolehan) }}</td>
                                    <td class="vx-text-end text-slate-600">{{ format_rupiah($asset->monthlyDepreciation()) }}</td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                aria-label="Edit aset"
                                                title="Edit"
                                                @click="openEdit(@js($assetPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                aria-label="Hapus aset"
                                                title="Hapus"
                                                @click="openDelete(@js($assetPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm text-slate-500 py-10">
                                        {{ $hasActiveFilters ? 'Tidak ada aset sesuai filter.' : 'Belum ada aset.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $assets->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="asset-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="asset-create-title">Tambah aset</h3>
                            <p>Catat peralatan untuk perhitungan beban depresiasi</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.assets.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.assets._form-fields', ['prefix' => 'create_'])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan aset</x-primary-button>
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
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="asset-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="asset-edit-title">Edit aset</h3>
                            <p>
                                <span x-show="editDepresiasiLabel" x-cloak>Depresiasi </span>
                                <span x-text="editDepresiasiLabel ? editDepresiasiLabel + ' / bulan' : 'Perbarui data peralatan'"></span>
                            </p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalAsset ? route('admin.assets.update', $modalAsset) : '' }}"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.assets._form-fields', [
                                'prefix' => 'edit_',
                                'asset' => $modalAsset,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui aset</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="asset-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="asset-delete-title">Hapus aset</h3>
                            <p x-text="deleteAsset ? deleteAsset.nama : ''"></p>
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
                                Aset yang dihapus tidak lagi dihitung dalam depresiasi laporan laba rugi.
                                Yakin ingin melanjutkan?
                            </p>
                            <p
                                class="rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 px-3 py-2 text-sm text-slate-700"
                                x-show="deleteAsset"
                                x-cloak
                            >
                                <span class="text-slate-500">Nilai perolehan:</span>
                                <strong x-text="deleteAsset ? deleteAsset.harga_label : ''"></strong>
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button>Hapus aset</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
