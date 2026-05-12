@props(['expense' => null, 'categories'])

<div class="grid gap-5 lg:grid-cols-3">
    <div class="vx-card vx-card-pad space-y-5 lg:col-span-2">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Detail pengeluaran</h2>
            <p class="text-xs text-slate-500">Catat biaya operasional seperti sewa, maintenance, gaji, dll.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="vx-field">
                <x-input-label for="tanggal" value="Tanggal" />
                <x-text-input id="tanggal" name="tanggal" type="date" :value="old('tanggal', optional($expense?->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('tanggal')" />
            </div>
            <div class="vx-field">
                <x-input-label for="kategori" value="Kategori" />
                <select id="kategori" name="kategori" class="vx-select" required>
                    <option value="">— Pilih kategori —</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" @selected(old('kategori', $expense?->kategori) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kategori')" />
            </div>
        </div>

        <div class="vx-field">
            <x-input-label for="nama" value="Nama / Keterangan singkat" />
            <x-text-input id="nama" name="nama" type="text" :value="old('nama', $expense?->nama)" placeholder="mis. Sewa tempat bulan Mei" required />
            <x-input-error :messages="$errors->get('nama')" />
        </div>

        <div class="vx-field">
            <x-input-label for="jumlah" value="Jumlah (Rp)" />
            <x-text-input id="jumlah" name="jumlah" type="number" min="0" :value="old('jumlah', $expense?->jumlah)" placeholder="0" required />
            <x-input-error :messages="$errors->get('jumlah')" />
        </div>

        <div class="vx-field">
            <x-input-label for="catatan" value="Catatan" />
            <textarea id="catatan" name="catatan" rows="3" class="vx-input" placeholder="Opsional">{{ old('catatan', $expense?->catatan) }}</textarea>
            <x-input-error :messages="$errors->get('catatan')" />
        </div>
    </div>

    <div class="vx-card vx-card-pad space-y-3">
        <h2 class="text-sm font-semibold text-slate-900">Tips kategori</h2>
        <ul class="space-y-2 text-xs text-slate-600">
            <li><strong class="text-slate-900">Sewa Tempat</strong> — biaya kontrak ruko/lapak bulanan.</li>
            <li><strong class="text-slate-900">Maintenance Mesin</strong> — servis mesin kopi, grinder, kulkas, dll.</li>
            <li><strong class="text-slate-900">Utilitas</strong> — listrik, air, internet.</li>
            <li><strong class="text-slate-900">Gaji</strong> — upah karyawan periode terkait.</li>
            <li><strong class="text-slate-900">Bahan Baku</strong> — biji kopi, susu, kemasan, gula, dll.</li>
        </ul>
    </div>
</div>

<div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.expenses.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
    <x-primary-button>{{ $expense ? 'Perbarui pengeluaran' : 'Simpan pengeluaran' }}</x-primary-button>
</div>
