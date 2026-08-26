@extends('layouts.admin')

@section('title', 'Diskon')

@section('content')
    @php
        use App\Models\Discount;

        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $listHiddenFields = [
            'filter_nama' => $columnFilters['filter_nama'],
            'filter_jenis' => $columnFilters['filter_jenis'],
            'filter_status' => $columnFilters['filter_status'],
            'page' => request('page'),
        ];
    @endphp

    <div
        x-data="{
            showCreate: @js($initialOpenCreate),
            showEdit: @js($initialOpenEdit),
            showDelete: @js($initialOpenDelete),
            deleteDiscount: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(discount) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/diskon')).replace(/\/$/, '') + '/' + discount.id;
                const setVal = (id, value) => {
                    const el = form.querySelector('#' + id);
                    if (el) el.value = value ?? '';
                };
                const setCheck = (id, on) => {
                    const el = form.querySelector('#' + id);
                    if (el) el.checked = !!on;
                };
                setVal('edit_nama', discount.nama);
                setVal('edit_jenis', discount.jenis);
                setVal('edit_tipe_nilai', discount.tipe_nilai);
                setVal('edit_jumlah', discount.jumlah);
                setVal('edit_product_id', discount.product_id ? String(discount.product_id) : '');
                setVal('edit_category_id', discount.category_id ? String(discount.category_id) : '');
                setVal('edit_min_belanja', discount.min_belanja ?? '');
                setVal('edit_starts_at', discount.starts_at || '');
                setVal('edit_ends_at', discount.ends_at || '');
                setVal('edit_jam_mulai', discount.jam_mulai || '');
                setVal('edit_jam_selesai', discount.jam_selesai || '');
                setVal('edit_catatan', discount.catatan || '');
                setCheck('edit_is_active', discount.is_active);

                form.querySelectorAll('input[type=checkbox]').forEach((cb) => {
                    if (cb.name !== 'hari_aktif[]') return;
                    cb.checked = (discount.hari_aktif || []).map(Number).includes(Number(cb.value));
                });

                const jenisSelect = form.querySelector('#edit_jenis');
                if (jenisSelect) {
                    jenisSelect.dispatchEvent(new Event('input', { bubbles: true }));
                }
                const tipeSelect = form.querySelector('#edit_tipe_nilai');
                if (tipeSelect) {
                    tipeSelect.dispatchEvent(new Event('input', { bubbles: true }));
                }

                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(discount) {
                this.deleteDiscount = discount;
                this.$refs.deleteForm.action = @js(url('admin/diskon')).replace(/\/$/, '') + '/' + discount.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteDiscount = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Diskon sesuai filter' : 'Total diskon' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredTotal : $catalogTotal, 0, ',', '.') }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ number_format($catalogTotal, 0, ',', '.') }} aturan diskon</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-success">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Diskon aktif</p>
                    <p class="vx-stat-value">{{ number_format($activeCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Promo & diskon</h2>
                    <p>Kelola diskon produk, kategori, minimal belanja, event, dan happy hour</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.discounts.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah diskon
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.discounts.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Promo</th>
                                <th>Jenis</th>
                                <th>Nilai</th>
                                <th>Syarat / periode</th>
                                <th>Status</th>
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
                                    <select name="filter_jenis" class="vx-table-col-filter" onchange="this.form.submit()">
                                        <option value="">Semua jenis</option>
                                        @foreach (Discount::JENIS_LABELS as $value => $label)
                                            <option value="{{ $value }}" @selected($columnFilters['filter_jenis'] === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th></th>
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
                            @forelse ($discounts as $discount)
                                @php
                                    $payload = [
                                        'id' => $discount->id,
                                        'nama' => $discount->nama,
                                        'jenis' => $discount->jenis,
                                        'tipe_nilai' => $discount->tipe_nilai,
                                        'jumlah' => (int) $discount->jumlah,
                                        'product_id' => $discount->product_id,
                                        'category_id' => $discount->category_id,
                                        'min_belanja' => $discount->min_belanja,
                                        'starts_at' => $discount->starts_at?->format('Y-m-d\TH:i'),
                                        'ends_at' => $discount->ends_at?->format('Y-m-d\TH:i'),
                                        'jam_mulai' => $discount->jam_mulai ? substr((string) $discount->jam_mulai, 0, 5) : null,
                                        'jam_selesai' => $discount->jam_selesai ? substr((string) $discount->jam_selesai, 0, 5) : null,
                                        'hari_aktif' => $discount->hari_aktif ?? [],
                                        'is_active' => $discount->is_active,
                                        'catatan' => $discount->catatan,
                                    ];
                                    $scopeBits = [];
                                    if ($discount->product) {
                                        $scopeBits[] = $discount->product->nama_produk;
                                    }
                                    if ($discount->category) {
                                        $scopeBits[] = 'Kategori '.$discount->category->nama_kategori;
                                    }
                                    if ($discount->min_belanja) {
                                        $scopeBits[] = 'Min. '.format_rupiah($discount->min_belanja);
                                    }
                                    if ($discount->starts_at || $discount->ends_at) {
                                        $from = $discount->starts_at?->format('d/m/Y H:i') ?? '…';
                                        $to = $discount->ends_at?->format('d/m/Y H:i') ?? '…';
                                        $scopeBits[] = $from.' – '.$to;
                                    }
                                    if ($discount->jam_mulai && $discount->jam_selesai) {
                                        $scopeBits[] = substr((string) $discount->jam_mulai, 0, 5).'–'.substr((string) $discount->jam_selesai, 0, 5);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <p class="text-sm font-semibold text-slate-900">{{ $discount->nama }}</p>
                                        @if ($discount->catatan)
                                            <p class="text-xs text-slate-500">{{ $discount->catatan }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="vx-badge vx-badge-primary">{{ $discount->jenisLabel() }}</span>
                                    </td>
                                    <td class="font-semibold text-rose-600">−{{ $discount->nilaiLabel() }}</td>
                                    <td class="text-xs text-slate-600">
                                        {{ $scopeBits !== [] ? implode(' · ', $scopeBits) : '—' }}
                                    </td>
                                    <td>
                                        @if ($discount->is_active)
                                            @if ($discount->isValidAt())
                                                <span class="vx-badge vx-badge-success">Berlaku</span>
                                            @else
                                                <span class="vx-badge vx-badge-slate">Aktif (di luar jadwal)</span>
                                            @endif
                                        @else
                                            <span class="vx-badge vx-badge-slate">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button type="button" class="vx-btn-icon" title="Edit" aria-label="Edit" @click="openEdit(@js($payload))">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button type="button" class="vx-btn-icon is-danger" title="Hapus" aria-label="Hapus" @click="openDelete(@js($payload))">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                        {{ $hasActiveFilters ? 'Tidak ada diskon sesuai filter.' : 'Belum ada diskon.' }}
                                        @if (! $hasActiveFilters)
                                            <button type="button" class="ml-1 font-semibold text-blue-700 hover:underline" @click="openCreate()">Tambah diskon</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $discounts->links() }}</div>
        </div>

        <template x-teleport="body">
            <div x-show="showCreate" x-cloak class="vx-modal-overlay" @keydown.escape.window="closeCreate()" @click.self="closeCreate()">
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="discount-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="discount-create-title">Tambah diskon</h3>
                            <p>Pilih jenis promo coffee shop yang sesuai</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.discounts.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.discounts._form-fields', [
                                'prefix' => 'create_',
                                'products' => $products,
                                'categories' => $categories,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan diskon</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="showEdit" x-cloak class="vx-modal-overlay" @keydown.escape.window="closeEdit()" @click.self="closeEdit()">
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="discount-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="discount-edit-title">Edit diskon</h3>
                            <p>Perbarui aturan promo</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form x-ref="editForm" method="POST" action="{{ $modalDiscount ? route('admin.discounts.update', $modalDiscount) : '' }}">
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.discounts._form-fields', [
                                'prefix' => 'edit_',
                                'discount' => $modalDiscount,
                                'products' => $products,
                                'categories' => $categories,
                                'isEdit' => true,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui diskon</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="showDelete" x-cloak class="vx-modal-overlay" @keydown.escape.window="closeDelete()" @click.self="closeDelete()">
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="discount-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="discount-delete-title">Hapus diskon</h3>
                            <p x-text="deleteDiscount ? deleteDiscount.nama : ''"></p>
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
                            <p class="text-sm text-slate-600">Hapus aturan diskon ini? Promo tidak lagi berlaku di kasir.</p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button>Hapus diskon</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
