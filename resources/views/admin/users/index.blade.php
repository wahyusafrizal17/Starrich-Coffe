@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
    @php
        $initialOpenCreate = ($openModal ?? null) === 'create' || request('modal') === 'create';
        $initialOpenEdit = ($openModal ?? null) === 'edit';
        $initialOpenDelete = ($openModal ?? null) === 'delete';
        $currentUserId = auth()->id();
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
            deleteUser: null,
            openCreate() {
                this.showCreate = true;
            },
            closeCreate() {
                this.showCreate = false;
            },
            openEdit(user) {
                const form = this.$refs.editForm;
                form.action = @js(url('admin/users')).replace(/\/$/, '') + '/' + user.id;
                form.querySelector('#edit_name').value = user.name;
                form.querySelector('#edit_email').value = user.email;
                form.querySelector('#edit_role').value = user.role;
                form.querySelector('#edit_password').value = '';
                form.querySelector('#edit_password_confirmation').value = '';
                this.showEdit = true;
            },
            closeEdit() {
                this.showEdit = false;
            },
            openDelete(user) {
                this.deleteUser = user;
                this.$refs.deleteForm.action = @js(url('admin/users')).replace(/\/$/, '') + '/' + user.id;
                this.showDelete = true;
            },
            closeDelete() {
                this.showDelete = false;
                this.deleteUser = null;
            },
        }"
    >
        <div class="grid gap-4 mb-5 sm:grid-cols-2">
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">{{ $hasActiveFilters ? 'Pengguna sesuai filter' : 'Total pengguna' }}</p>
                    <p class="vx-stat-value">{{ number_format($hasActiveFilters ? $filteredCount : $catalogTotal, 0, ',', '.') }}</p>
                    @if ($hasActiveFilters)
                        <p class="mt-1 text-xs text-slate-500">dari {{ number_format($catalogTotal, 0, ',', '.') }} akun terdaftar</p>
                    @endif
                </div>
            </div>
            <div class="vx-stat">
                <span class="vx-stat-icon vx-bg-violet">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="vx-stat-label">Per role</p>
                    <p class="vx-stat-value">{{ number_format($adminCount, 0, ',', '.') }} admin</p>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($kasirCount, 0, ',', '.') }} kasir</p>
                </div>
            </div>
        </div>

        <div class="vx-table-wrap">
            <div class="vx-card-head border-b border-[var(--vx-border-soft)] px-5 py-4">
                <div>
                    <h2>Daftar pengguna</h2>
                    <p>Admin dan kasir yang dapat mengakses aplikasi</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ route('admin.users.index') }}" class="vx-btn vx-btn-ghost vx-btn-sm">
                            Reset filter
                        </a>
                    @endif
                    <button type="button" class="vx-btn vx-btn-primary vx-btn-sm" @click="openCreate()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah pengguna
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="overflow-x-auto">
                    <table class="vx-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="vx-text-end">Aksi</th>
                            </tr>
                            <tr class="vx-table-filter-row">
                                <th colspan="2">
                                    <input
                                        type="search"
                                        name="filter_nama"
                                        value="{{ $columnFilters['filter_nama'] }}"
                                        placeholder="Cari nama atau email…"
                                        class="vx-table-col-filter"
                                        onchange="this.form.submit()"
                                    >
                                </th>
                                <th>
                                    <select name="filter_role" class="vx-table-col-filter" onchange="this.form.submit()">
                                        <option value="">Semua role</option>
                                        <option value="admin" @selected($columnFilters['filter_role'] === 'admin')>Admin</option>
                                        <option value="kasir" @selected($columnFilters['filter_role'] === 'kasir')>Kasir</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                @php
                                    $userPayload = [
                                        'id' => $u->id,
                                        'name' => $u->name,
                                        'email' => $u->email,
                                        'role' => $u->role,
                                        'role_label' => $u->role === 'admin' ? 'Admin' : 'Kasir',
                                        'is_self' => $u->id === $currentUserId,
                                        'can_delete' => $u->id !== $currentUserId && $catalogTotal > 1,
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="vx-thumb-placeholder" aria-hidden="true">
                                                {{ \Illuminate\Support\Str::of($u->name)->trim()->substr(0, 1)->upper() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900">{{ $u->name }}</p>
                                                <p class="text-xs text-slate-500">ID #{{ $u->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-slate-600">{{ $u->email }}</td>
                                    <td>
                                        @if ($u->role === 'admin')
                                            <span class="vx-badge vx-badge-violet">Admin</span>
                                        @else
                                            <span class="vx-badge vx-badge-slate">Kasir</span>
                                        @endif
                                    </td>
                                    <td class="vx-text-end">
                                        <div class="vx-table-actions justify-end">
                                            <button
                                                type="button"
                                                class="vx-btn-icon"
                                                aria-label="Edit pengguna"
                                                title="Edit"
                                                @click="openEdit(@js($userPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="vx-btn-icon is-danger"
                                                aria-label="Hapus pengguna"
                                                title="Hapus"
                                                @click="openDelete(@js($userPayload))"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-sm text-slate-500 py-10">
                                        {{ $hasActiveFilters ? 'Tidak ada pengguna sesuai filter.' : 'Belum ada pengguna.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="vx-table-foot">{{ $users->links() }}</div>
        </div>

        <template x-teleport="body">
            <div
                x-show="showCreate"
                x-cloak
                class="vx-modal-overlay"
                @keydown.escape.window="closeCreate()"
                @click.self="closeCreate()"
            >
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="user-create-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="user-create-title">Tambah pengguna</h3>
                            <p>Buat akun untuk admin atau kasir</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeCreate()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.users._form-fields', ['prefix' => 'create_'])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeCreate()">Batal</button>
                            <x-primary-button>Simpan pengguna</x-primary-button>
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
                <div class="vx-modal-panel is-wide" role="dialog" aria-modal="true" aria-labelledby="user-edit-title" @click.stop>
                    <div class="vx-modal-head">
                        <div>
                            <h3 id="user-edit-title">Edit pengguna</h3>
                            <p>Perbarui data akun dan hak akses</p>
                        </div>
                        <button type="button" class="vx-modal-close" @click="closeEdit()" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form
                        x-ref="editForm"
                        method="POST"
                        action="{{ $modalUser ? route('admin.users.update', $modalUser) : '' }}"
                    >
                        @csrf
                        @method('PUT')
                        @foreach ($listHiddenFields as $name => $value)
                            @if ($value !== null && $value !== '')
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="vx-modal-body">
                            @include('admin.users._form-fields', [
                                'prefix' => 'edit_',
                                'user' => $modalUser,
                                'isEdit' => true,
                            ])
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeEdit()">Batal</button>
                            <x-primary-button>Perbarui pengguna</x-primary-button>
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
                <div class="vx-modal-panel" role="dialog" aria-modal="true" aria-labelledby="user-delete-title" @click.stop>
                    <div class="vx-modal-head" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);">
                        <div>
                            <h3 id="user-delete-title">Hapus pengguna</h3>
                            <p x-text="deleteUser ? deleteUser.name : ''"></p>
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
                                Akun yang dihapus tidak dapat login lagi. Yakin ingin melanjutkan?
                            </p>
                            <p
                                class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"
                                x-show="deleteUser && deleteUser.is_self"
                                x-cloak
                            >
                                Tidak dapat menghapus akun yang sedang Anda gunakan.
                            </p>
                            <p
                                class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"
                                x-show="deleteUser && !deleteUser.can_delete && !deleteUser.is_self"
                                x-cloak
                            >
                                Minimal satu pengguna harus tetap ada di sistem.
                            </p>
                            <p
                                class="rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 px-3 py-2 text-sm text-slate-700"
                                x-show="deleteUser && deleteUser.can_delete"
                                x-cloak
                            >
                                <span class="text-slate-500">Email:</span>
                                <strong x-text="deleteUser ? deleteUser.email : ''"></strong>
                                ·
                                <span class="text-slate-500">Role:</span>
                                <strong x-text="deleteUser ? deleteUser.role_label : ''"></strong>
                            </p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-[var(--vx-border-soft)] px-5 py-4 sm:flex-row sm:justify-end">
                            <button type="button" class="vx-btn vx-btn-ghost" @click="closeDelete()">Batal</button>
                            <x-danger-button x-bind:disabled="deleteUser && !deleteUser.can_delete">
                                Hapus pengguna
                            </x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
