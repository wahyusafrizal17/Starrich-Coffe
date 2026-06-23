@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = array_merge(
            $filterQuery,
            $columnFilters,
            ['page' => request('page')],
        );
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            deleteExpense: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(expense) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/pengeluaran')).replace(/\/$/, '') + '/' + expense.id;
                form.querySelector('#edit_tanggal').value = expense.tanggal;
                form.querySelector('#edit_kategori').value = expense.kategori;
                form.querySelector('#edit_nama').value = expense.nama;
                form.querySelector('#edit_jumlah').value = expense.jumlah;
                form.querySelector('#edit_catatan').value = expense.catatan || '';
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(expense) {
                this.deleteExpense = expense;
                this.$refs.deleteForm.action = @js(url('admin/pengeluaran')).replace(/\/$/, '') + '/' + expense.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteExpense = null;
            },
        }"
    >
        @include('partials.dashboard-filter-strip', [
            'period' => $period,
            'filterAction' => route('admin.expenses.index'),
        ])

        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-warning">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Total pengeluaran</p>
                    <p class="vx-stat-value">{{ format_rupiah($sumTotal) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $periodLabel }}</p>
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-violet">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Jumlah entri</p>
                    <p class="vx-stat-value">{{ number_format($entryCount, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-500">Pos biaya pada periode ini</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Daftar pengeluaran</h2>
                    <p>Biaya operasional untuk laporan laba rugi · {{ $periodLabel }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveColumnFilters)
                        <a href="{{ route('admin.expenses.index', $filterQuery) }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter kolom
                        </a>
                    @endif
                    <a href="{{ route('admin.reports.profit-loss', $filterQuery) }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.5 4.5L21.75 7.5M21.75 7.5H16.5M21.75 7.5V12.75"/></svg>
                        Laba rugi
                    </a>
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah pengeluaran
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.expenses.index') }}">
                @foreach ($filterQuery as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
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
                            <tr class="vx-table-filter-row">
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
                                    <select name="filter_kategori" class="vx-table-col-filter" onchange="this.form.submit()">
                                        <option value="">Semua kategori</option>
                                        @foreach ($categories as $key => $label)
                                            <option value="{{ $key }}" @selected($columnFilters['filter_kategori'] === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <input
                                        type="search"
                                        name="filter_nama"
                                        value="{{ $columnFilters['filter_nama'] }}"
                                        placeholder="Cari keterangan…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th>
                                    <input
                                        type="search"
                                        name="filter_dicatat"
                                        value="{{ $columnFilters['filter_dicatat'] }}"
                                        placeholder="Cari nama…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses as $e)
                                @php
                                    $expensePayload = [
                                        'id' => $e->id,
                                        'tanggal' => $e->tanggal->format('Y-m-d'),
                                        'kategori' => $e->kategori,
                                        'nama' => $e->nama,
                                        'jumlah' => $e->jumlah,
                                        'jumlah_label' => format_rupiah($e->jumlah),
                                        'catatan' => $e->catatan ?? '',
                                        'dicatat' => $e->user?->name ?? '—',
                                        'kategori_label' => $e->kategori_label,
                                    ];
                                @endphp
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
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                aria-label="Edit pengeluaran"
                                                title="Edit"
                                                @click="openEdit(@js($expensePayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                aria-label="Hapus pengeluaran"
                                                title="Hapus"
                                                @click="openDelete(@js($expensePayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-sm text-slate-500 py-10">
                                        {{ $hasActiveColumnFilters ? 'Tidak ada pengeluaran sesuai filter.' : 'Belum ada pengeluaran pada periode ini.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $expenses->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="expense-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="expense-create-title">Tambah pengeluaran</h3>
                            <p>Catat satu pos biaya operasional</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.expenses.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.expenses._form-fields', [
                                'prefix' => 'create_',
                                'expense' => null,
                                'categories' => $categories,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan pengeluaran</x-primary-button>
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
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="expense-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="expense-edit-title">Edit pengeluaran</h3>
                            <p>Perbarui pos biaya operasional</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalExpense ? route('admin.expenses.update', $modalExpense) : '' }}"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.expenses._form-fields', [
                                'prefix' => 'edit_',
                                'expense' => $modalExpense,
                                'categories' => $categories,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui pengeluaran</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="expense-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="expense-delete-title">Hapus pengeluaran</h3>
                            <p x-text="deleteExpense ? deleteExpense.nama : ''"></p>
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
                                Pengeluaran ini akan dihapus dari laporan periode terpilih.
                                Yakin ingin melanjutkan?
                            </p>
                            <p
                                class="mt-3 rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 px-3 py-2 text-sm text-slate-700"
                                x-show="deleteExpense"
                                x-cloak
                            >
                                <span class="text-slate-500">Jumlah:</span>
                                <strong x-text="deleteExpense ? deleteExpense.jumlah_label : ''"></strong>
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button>Hapus pengeluaran</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
