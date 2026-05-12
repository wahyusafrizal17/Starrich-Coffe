@props(['asset' => null])

<div class="vx-card vx-card-pad space-y-5">
    <div>
        <h2 class="text-sm font-semibold text-slate-900">Detail aset</h2>
        <p class="text-xs text-slate-500">Catat peralatan yang dimiliki toko.</p>
    </div>

    <div class="vx-field">
        <x-input-label for="nama" value="Nama aset" />
        <x-text-input id="nama" name="nama" type="text" :value="old('nama', $asset?->nama)" placeholder="mis. Mesin Espresso La Marzocco" required />
        <x-input-error :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div class="vx-field">
            <x-input-label for="tanggal_perolehan" value="Tanggal perolehan" />
            <x-text-input id="tanggal_perolehan" name="tanggal_perolehan" type="date" :value="old('tanggal_perolehan', optional($asset?->tanggal_perolehan)->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('tanggal_perolehan')" />
        </div>
        <div class="vx-field">
            <x-input-label for="harga_perolehan" value="Harga (Rp)" />
            <x-text-input id="harga_perolehan" name="harga_perolehan" type="number" min="0" :value="old('harga_perolehan', $asset?->harga_perolehan)" required />
            <x-input-error :messages="$errors->get('harga_perolehan')" />
        </div>
    </div>

    <div class="vx-field">
        <x-input-label for="catatan" value="Catatan" />
        <textarea id="catatan" name="catatan" rows="3" class="vx-input" placeholder="Opsional">{{ old('catatan', $asset?->catatan) }}</textarea>
        <x-input-error :messages="$errors->get('catatan')" />
    </div>
</div>

<div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.assets.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
    <x-primary-button>{{ $asset ? 'Perbarui aset' : 'Simpan aset' }}</x-primary-button>
</div>
