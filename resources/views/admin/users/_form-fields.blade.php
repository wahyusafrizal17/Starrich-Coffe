@props([
    'prefix' => '',
    'user' => null,
    'isEdit' => false,
])

<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}name">Nama lengkap</label>
            <input
                id="{{ $prefix }}name"
                name="name"
                type="text"
                class="vx-input w-full"
                value="{{ old('name', $user?->name) }}"
                required
            />
            <x-input-error :messages="$errors->get('name')" />
        </div>
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}email">Email</label>
            <input
                id="{{ $prefix }}email"
                name="email"
                type="email"
                class="vx-input w-full"
                value="{{ old('email', $user?->email) }}"
                required
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}role">Role</label>
        <select id="{{ $prefix }}role" name="role" class="vx-select w-full" required>
            <option value="kasir" @selected(old('role', $user?->role ?? 'kasir') === 'kasir')>Kasir</option>
            <option value="admin" @selected(old('role', $user?->role) === 'admin')>Admin</option>
        </select>
        <x-input-error :messages="$errors->get('role')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}password">
                {{ $isEdit ? 'Password baru (opsional)' : 'Password' }}
            </label>
            <input
                id="{{ $prefix }}password"
                name="password"
                type="password"
                class="vx-input w-full"
                @if (! $isEdit) required @endif
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}password_confirmation">Konfirmasi password</label>
            <input
                id="{{ $prefix }}password_confirmation"
                name="password_confirmation"
                type="password"
                class="vx-input w-full"
                @if (! $isEdit) required @endif
            />
        </div>
    </div>

    <div class="rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 p-3 text-xs text-slate-600">
        <p class="font-semibold text-slate-900">Hak akses</p>
        <ul class="mt-2 space-y-1">
            <li><strong class="text-slate-900">Admin</strong> — dashboard, laporan, pengaturan, dan seluruh menu manajemen.</li>
            <li><strong class="text-slate-900">Kasir</strong> — halaman POS, riwayat transaksi, dan open bill.</li>
        </ul>
        @if ($isEdit && $user)
            <p class="mt-2 border-t border-[var(--vx-border-soft)] pt-2">
                ID #{{ $user->id }} · bergabung {{ $user->created_at?->format('d M Y') ?? '—' }}
            </p>
        @endif
    </div>
</div>
