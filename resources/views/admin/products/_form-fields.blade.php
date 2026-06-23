@props([
    'prefix' => '',
    'product' => null,
    'showCurrentImage' => false,
])

<div class="space-y-4">
    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}nama_produk">Nama produk</label>
        <input
            id="{{ $prefix }}nama_produk"
            name="nama_produk"
            type="text"
            class="vx-input w-full"
            value="{{ old('nama_produk', $product?->nama_produk) }}"
            placeholder="mis. Espresso"
            required
        />
        <x-input-error :messages="$errors->get('nama_produk')" />
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}kategori_id">Kategori</label>
        <select id="{{ $prefix }}kategori_id" name="kategori_id" class="vx-select w-full" required>
            <option value="">— Pilih kategori —</option>
            @foreach ($categories as $c)
                <option
                    value="{{ $c->id }}"
                    @selected((string) old('kategori_id', $product?->kategori_id) === (string) $c->id)
                >
                    {{ $c->nama_kategori }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kategori_id')" />
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}harga">Harga (Rp)</label>
        <input
            id="{{ $prefix }}harga"
            name="harga"
            type="number"
            min="0"
            class="vx-input w-full"
            value="{{ old('harga', $product?->harga) }}"
            placeholder="0"
            required
        />
        <x-input-error :messages="$errors->get('harga')" />
    </div>

    @if ($showCurrentImage)
        <div
            class="flex items-center gap-3 rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 p-3"
            x-show="editImageUrl"
            x-cloak
        >
            <img :src="editImageUrl" alt="" class="h-14 w-14 rounded-lg object-cover" />
            <p class="text-xs text-slate-500">Gambar saat ini. Unggah file baru untuk menggantinya.</p>
        </div>
    @endif

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}gambar">{{ $showCurrentImage ? 'Unggah gambar baru' : 'Gambar produk' }}</label>
        <input id="{{ $prefix }}gambar" name="gambar" type="file" accept="image/*" class="vx-input w-full" />
        <p class="vx-help">{{ $showCurrentImage ? 'Opsional.' : 'Opsional. Format JPG/PNG, ukuran ideal 1:1.' }}</p>
        <x-input-error :messages="$errors->get('gambar')" />
    </div>
</div>
