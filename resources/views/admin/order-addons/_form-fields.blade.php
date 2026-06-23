@props([
    'prefix' => '',
    'orderAddon' => null,
    'isEdit' => false,
])

<div class="space-y-4">
    @if (! $isEdit)
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}kode">Kode (opsional)</label>
            <input
                id="{{ $prefix }}kode"
                name="kode"
                type="text"
                class="vx-input w-full font-mono text-sm"
                value="{{ old('kode') }}"
                placeholder="mis. arabica — kosongkan untuk otomatis"
                pattern="[a-z0-9_]+"
            />
            <p class="vx-help">Huruf kecil, angka, dan underscore. Tidak bisa diubah setelah disimpan.</p>
            <x-input-error :messages="$errors->get('kode')" />
        </div>
    @else
        <div class="vx-field">
            <span class="vx-label">Kode</span>
            <p class="mt-1 font-mono text-sm font-semibold text-slate-800" x-text="editKode || '{{ $orderAddon?->kode ?? '' }}'"></p>
        </div>
    @endif

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}label">Nama tambahan</label>
        <input
            id="{{ $prefix }}label"
            name="label"
            type="text"
            class="vx-input w-full"
            value="{{ old('label', $orderAddon->label ?? '') }}"
            placeholder="mis. Biji Arabika"
            required
        />
        <x-input-error :messages="$errors->get('label')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}harga">Harga tambahan (per cup)</label>
            <input
                id="{{ $prefix }}harga"
                name="harga"
                type="number"
                min="0"
                step="1"
                class="vx-input w-full"
                value="{{ old('harga', $orderAddon->harga ?? 0) }}"
                required
            />
            <x-input-error :messages="$errors->get('harga')" />
        </div>

        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}urutan">Urutan tampil</label>
            <input
                id="{{ $prefix }}urutan"
                name="urutan"
                type="number"
                min="0"
                max="9999"
                class="vx-input w-full"
                value="{{ old('urutan', $orderAddon->urutan ?? 0) }}"
            />
            <p class="vx-help">Angka lebih kecil tampil lebih dulu.</p>
            <x-input-error :messages="$errors->get('urutan')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="inline-flex cursor-pointer items-center gap-2">
            <input
                id="{{ $prefix }}is_active"
                type="checkbox"
                name="is_active"
                value="1"
                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                @checked(old('is_active', $orderAddon->is_active ?? true))
            />
            <span class="text-sm font-medium text-slate-700">Aktif (tampil di kasir)</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" />
    </div>
</div>
