@props([
    'prefix' => '',
    'expense' => null,
    'categories' => [],
])

<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}tanggal">Tanggal</label>
            <input
                id="{{ $prefix }}tanggal"
                name="tanggal"
                type="date"
                class="vx-input w-full"
                value="{{ old('tanggal', optional($expense?->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required
            />
            <x-input-error :messages="$errors->get('tanggal')" />
        </div>
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}kategori">Kategori</label>
            <select id="{{ $prefix }}kategori" name="kategori" class="vx-select w-full" required>
                <option value="">— Pilih kategori —</option>
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('kategori', $expense?->kategori) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kategori')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}nama">Nama / Keterangan singkat</label>
        <input
            id="{{ $prefix }}nama"
            name="nama"
            type="text"
            class="vx-input w-full"
            value="{{ old('nama', $expense?->nama) }}"
            placeholder="mis. Sewa tempat bulan Mei"
            required
        />
        <x-input-error :messages="$errors->get('nama')" />
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}jumlah">Jumlah (Rp)</label>
        <input
            id="{{ $prefix }}jumlah"
            name="jumlah"
            type="number"
            min="0"
            class="vx-input w-full"
            value="{{ old('jumlah', $expense?->jumlah) }}"
            placeholder="0"
            required
        />
        <x-input-error :messages="$errors->get('jumlah')" />
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}catatan">Catatan</label>
        <textarea
            id="{{ $prefix }}catatan"
            name="catatan"
            rows="3"
            class="vx-input w-full"
            placeholder="Opsional"
        >{{ old('catatan', $expense?->catatan) }}</textarea>
        <x-input-error :messages="$errors->get('catatan')" />
    </div>

    <div class="rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 p-3 text-xs text-slate-600">
        <p class="font-semibold text-slate-900">Kategori umum</p>
        <p class="mt-1">Sewa tempat, maintenance mesin, utilitas, gaji karyawan, bahan baku, dan lainnya.</p>
    </div>
</div>
