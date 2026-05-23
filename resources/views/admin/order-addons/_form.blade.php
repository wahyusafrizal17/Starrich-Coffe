@php
    $isEdit = isset($orderAddon);
@endphp

@if (! $isEdit)
    <div class="vx-field">
        <x-input-label for="kode" value="Kode (opsional)" />
        <x-text-input
            id="kode"
            name="kode"
            type="text"
            :value="old('kode')"
            placeholder="mis. arabica — kosongkan untuk otomatis"
            pattern="[a-z0-9_]+"
        />
        <p class="mt-1 text-xs text-slate-500">Huruf kecil, angka, dan underscore. Tidak bisa diubah setelah disimpan.</p>
        <x-input-error :messages="$errors->get('kode')" />
    </div>
@else
    <div class="vx-field">
        <x-input-label value="Kode" />
        <p class="mt-1 font-mono text-sm font-semibold text-slate-800">{{ $orderAddon->kode }}</p>
    </div>
@endif

<div class="vx-field">
    <x-input-label for="label" value="Nama tambahan" />
    <x-text-input
        id="label"
        name="label"
        type="text"
        :value="old('label', $orderAddon->label ?? '')"
        placeholder="mis. Biji Arabika"
        required
    />
    <x-input-error :messages="$errors->get('label')" />
</div>

<div class="vx-field">
    <x-input-label for="harga" value="Harga tambahan (per cup)" />
    <x-text-input
        id="harga"
        name="harga"
        type="number"
        min="0"
        step="1"
        :value="old('harga', $orderAddon->harga ?? 0)"
        required
    />
    <x-input-error :messages="$errors->get('harga')" />
</div>

<div class="vx-field">
    <x-input-label for="urutan" value="Urutan tampil" />
    <x-text-input
        id="urutan"
        name="urutan"
        type="number"
        min="0"
        max="9999"
        :value="old('urutan', $orderAddon->urutan ?? 0)"
    />
    <p class="mt-1 text-xs text-slate-500">Angka lebih kecil tampil lebih dulu di kasir.</p>
    <x-input-error :messages="$errors->get('urutan')" />
</div>

<div class="vx-field">
    <label class="inline-flex cursor-pointer items-center gap-2">
        <input
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
