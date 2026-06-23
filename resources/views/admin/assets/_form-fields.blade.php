@props([
    'prefix' => '',
    'asset' => null,
])

<div class="space-y-4">
    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}nama">Nama aset</label>
        <input
            id="{{ $prefix }}nama"
            name="nama"
            type="text"
            class="vx-input w-full"
            value="{{ old('nama', $asset?->nama) }}"
            placeholder="mis. Mesin Espresso La Marzocco"
            required
        />
        <x-input-error :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}tanggal_perolehan">Tanggal perolehan</label>
            <input
                id="{{ $prefix }}tanggal_perolehan"
                name="tanggal_perolehan"
                type="date"
                class="vx-input w-full"
                value="{{ old('tanggal_perolehan', optional($asset?->tanggal_perolehan)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required
            />
            <x-input-error :messages="$errors->get('tanggal_perolehan')" />
        </div>
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}harga_perolehan">Harga (Rp)</label>
            <input
                id="{{ $prefix }}harga_perolehan"
                name="harga_perolehan"
                type="number"
                min="0"
                class="vx-input w-full"
                value="{{ old('harga_perolehan', $asset?->harga_perolehan) }}"
                required
            />
            <x-input-error :messages="$errors->get('harga_perolehan')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}catatan">Catatan</label>
        <textarea
            id="{{ $prefix }}catatan"
            name="catatan"
            rows="3"
            class="vx-input w-full"
            placeholder="Opsional"
        >{{ old('catatan', $asset?->catatan) }}</textarea>
        <x-input-error :messages="$errors->get('catatan')" />
    </div>

    <div class="rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 p-3 text-xs text-slate-600">
        <p class="font-semibold text-slate-900">Depresiasi garis lurus</p>
        <p class="mt-1">Masa manfaat default 60 bulan (5 tahun). Beban depresiasi bulanan otomatis masuk laporan laba rugi.</p>
    </div>
</div>
