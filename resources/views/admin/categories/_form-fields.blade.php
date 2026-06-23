@props([
    'prefix' => '',
    'category' => null,
])

<div class="vx-field">
    <label class="vx-label" for="{{ $prefix }}nama_kategori">Nama kategori</label>
    <input
        id="{{ $prefix }}nama_kategori"
        name="nama_kategori"
        type="text"
        class="vx-input w-full"
        value="{{ old('nama_kategori', $category?->nama_kategori) }}"
        placeholder="mis. Kopi"
        required
    />
    <x-input-error :messages="$errors->get('nama_kategori')" />
</div>
