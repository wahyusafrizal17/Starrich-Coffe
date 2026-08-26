@props([
    'prefix' => '',
    'discount' => null,
    'products' => collect(),
    'categories' => collect(),
    'isEdit' => false,
])

@php
    use App\Models\Discount;

    $jenis = old('jenis', $discount?->jenis ?? Discount::JENIS_PRODUCT);
    $tipeNilai = old('tipe_nilai', $discount?->tipe_nilai ?? Discount::TIPE_AMOUNT);
    $selectedProductId = (string) old('product_id', $discount?->product_id ?? '');
    $selectedCategoryId = (string) old('category_id', $discount?->category_id ?? '');
    $hariAktif = old('hari_aktif', $discount?->hari_aktif ?? []);
    if (! is_array($hariAktif)) {
        $hariAktif = [];
    }
    $hariLabels = [
        1 => 'Sen',
        2 => 'Sel',
        3 => 'Rab',
        4 => 'Kam',
        5 => 'Jum',
        6 => 'Sab',
        0 => 'Min',
    ];
    $startsAt = old('starts_at', $discount?->starts_at?->format('Y-m-d\TH:i'));
    $endsAt = old('ends_at', $discount?->ends_at?->format('Y-m-d\TH:i'));
    $jamMulai = old('jam_mulai', $discount?->jam_mulai ? substr((string) $discount->jam_mulai, 0, 5) : '');
    $jamSelesai = old('jam_selesai', $discount?->jam_selesai ? substr((string) $discount->jam_selesai, 0, 5) : '');
@endphp

<div
    class="space-y-4"
    x-data="{
        jenis: @js($jenis),
        tipeNilai: @js($tipeNilai),
    }"
>
    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}nama">Nama promo</label>
        <input
            id="{{ $prefix }}nama"
            name="nama"
            type="text"
            class="vx-input w-full"
            value="{{ old('nama', $discount?->nama) }}"
            placeholder="mis. Promo pagi, Diskon weekend"
            required
            maxlength="120"
        />
        <x-input-error :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}jenis">Jenis diskon</label>
            <select
                id="{{ $prefix }}jenis"
                name="jenis"
                class="vx-select w-full"
                x-model="jenis"
                @if ($isEdit) disabled @endif
                required
            >
                @foreach (Discount::JENIS_LABELS as $value => $label)
                    <option value="{{ $value }}" @selected($jenis === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @if ($isEdit)
                <input type="hidden" name="jenis" :value="jenis">
                <p class="vx-help">Jenis tidak bisa diganti. Hapus lalu buat baru jika salah jenis.</p>
            @endif
            <x-input-error :messages="$errors->get('jenis')" />
        </div>

        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}tipe_nilai">Tipe potongan</label>
            <select
                id="{{ $prefix }}tipe_nilai"
                name="tipe_nilai"
                class="vx-select w-full"
                x-model="tipeNilai"
                required
            >
                <option value="amount">Nominal (Rp)</option>
                <option value="percent">Persentase (%)</option>
            </select>
            <x-input-error :messages="$errors->get('tipe_nilai')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}jumlah">
            <span x-text="tipeNilai === 'percent' ? 'Persentase diskon' : 'Nominal diskon (Rp)'"></span>
        </label>
        <input
            id="{{ $prefix }}jumlah"
            name="jumlah"
            type="number"
            min="1"
            :max="tipeNilai === 'percent' ? 100 : undefined"
            class="vx-input w-full"
            value="{{ old('jumlah', $discount?->jumlah) }}"
            required
        />
        <p class="vx-help" x-show="tipeNilai === 'percent'" x-cloak>1–100. Dihitung dari harga item atau subtotal keranjang.</p>
        <p class="vx-help" x-show="tipeNilai === 'amount'" x-cloak>Potongan tetap dalam rupiah.</p>
        <x-input-error :messages="$errors->get('jumlah')" />
    </div>

    <div class="vx-field" x-show="jenis === 'product' || jenis === 'event' || jenis === 'happy_hour'" x-cloak>
        <label class="vx-label" for="{{ $prefix }}product_id">
            <span x-text="jenis === 'product' ? 'Produk' : 'Produk target (opsional)'"></span>
        </label>
        <select
            id="{{ $prefix }}product_id"
            name="product_id"
            class="vx-select w-full"
            :required="jenis === 'product'"
            :disabled="jenis !== 'product' && jenis !== 'event' && jenis !== 'happy_hour'"
        >
            <option value="">— {{ $jenis === 'product' ? 'Pilih produk' : 'Semua / tidak spesifik' }} —</option>
            @foreach ($products as $product)
                @php
                    $alreadyHasDiscount = $product->relationLoaded('discount')
                        ? $product->discount !== null
                        : false;
                    $isCurrent = (string) $product->id === $selectedProductId;
                @endphp
                <option
                    value="{{ $product->id }}"
                    @selected($isCurrent)
                    x-bind:disabled="jenis === 'product' && {{ ($alreadyHasDiscount && ! $isCurrent) ? 'true' : 'false' }}"
                >
                    {{ $product->nama_produk }} — {{ format_rupiah($product->harga) }}
                    @if ($alreadyHasDiscount && ! $isCurrent)
                        (sudah ada diskon produk)
                    @endif
                </option>
            @endforeach
        </select>
        <p class="vx-help" x-show="jenis === 'event' || jenis === 'happy_hour'" x-cloak>
            Jika diisi, potongan per item produk itu. Kosong + tanpa kategori = potongan keranjang.
        </p>
        <x-input-error :messages="$errors->get('product_id')" />
    </div>

    <div class="vx-field" x-show="jenis === 'category' || jenis === 'event' || jenis === 'happy_hour'" x-cloak>
        <label class="vx-label" for="{{ $prefix }}category_id">
            <span x-text="jenis === 'category' ? 'Kategori' : 'Kategori (opsional)'"></span>
        </label>
        <select
            id="{{ $prefix }}category_id"
            name="category_id"
            class="vx-select w-full"
            :required="jenis === 'category'"
            :disabled="jenis !== 'category' && jenis !== 'event' && jenis !== 'happy_hour'"
        >
            <option value="">— {{ $jenis === 'category' ? 'Pilih kategori' : 'Semua / tidak spesifik' }} —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $category->id === $selectedCategoryId)>
                    {{ $category->nama_kategori }}
                </option>
            @endforeach
        </select>
        <p class="vx-help" x-show="jenis === 'event' || jenis === 'happy_hour'" x-cloak>
            Jangan isi bersamaan dengan produk.
        </p>
        <x-input-error :messages="$errors->get('category_id')" />
    </div>

    <div class="vx-field" x-show="jenis === 'min_purchase' || jenis === 'event'" x-cloak>
        <label class="vx-label" for="{{ $prefix }}min_belanja">
            <span x-text="jenis === 'min_purchase' ? 'Minimal belanja (Rp)' : 'Minimal belanja (opsional)'"></span>
        </label>
        <input
            id="{{ $prefix }}min_belanja"
            name="min_belanja"
            type="number"
            min="0"
            class="vx-input w-full"
            value="{{ old('min_belanja', $discount?->min_belanja) }}"
            :required="jenis === 'min_purchase'"
            :disabled="jenis !== 'min_purchase' && jenis !== 'event'"
            placeholder="mis. 50000"
        />
        <x-input-error :messages="$errors->get('min_belanja')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2" x-show="jenis === 'event' || jenis === 'product' || jenis === 'category' || jenis === 'min_purchase' || jenis === 'happy_hour'" x-cloak>
        <div class="vx-field" x-show="jenis === 'event' || jenis === 'happy_hour' || jenis === 'product' || jenis === 'category' || jenis === 'min_purchase'">
            <label class="vx-label" for="{{ $prefix }}starts_at">
                <span x-text="jenis === 'event' ? 'Mulai event' : 'Berlaku dari (opsional)'"></span>
            </label>
            <input
                id="{{ $prefix }}starts_at"
                name="starts_at"
                type="datetime-local"
                class="vx-input w-full"
                value="{{ $startsAt }}"
                :required="jenis === 'event'"
            />
            <x-input-error :messages="$errors->get('starts_at')" />
        </div>
        <div class="vx-field">
            <label class="vx-label" for="{{ $prefix }}ends_at">
                <span x-text="jenis === 'event' ? 'Selesai event' : 'Berlaku sampai (opsional)'"></span>
            </label>
            <input
                id="{{ $prefix }}ends_at"
                name="ends_at"
                type="datetime-local"
                class="vx-input w-full"
                value="{{ $endsAt }}"
                :required="jenis === 'event'"
            />
            <x-input-error :messages="$errors->get('ends_at')" />
        </div>
    </div>

    <div class="space-y-4" x-show="jenis === 'happy_hour'" x-cloak>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="vx-field">
                <label class="vx-label" for="{{ $prefix }}jam_mulai">Jam mulai</label>
                <input
                    id="{{ $prefix }}jam_mulai"
                    name="jam_mulai"
                    type="time"
                    class="vx-input w-full"
                    value="{{ $jamMulai }}"
                    :required="jenis === 'happy_hour'"
                    :disabled="jenis !== 'happy_hour'"
                />
                <x-input-error :messages="$errors->get('jam_mulai')" />
            </div>
            <div class="vx-field">
                <label class="vx-label" for="{{ $prefix }}jam_selesai">Jam selesai</label>
                <input
                    id="{{ $prefix }}jam_selesai"
                    name="jam_selesai"
                    type="time"
                    class="vx-input w-full"
                    value="{{ $jamSelesai }}"
                    :required="jenis === 'happy_hour'"
                    :disabled="jenis !== 'happy_hour'"
                />
                <x-input-error :messages="$errors->get('jam_selesai')" />
            </div>
        </div>
        <div class="vx-field">
            <span class="vx-label">Hari aktif (opsional)</span>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($hariLabels as $day => $label)
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700">
                        <input
                            type="checkbox"
                            name="hari_aktif[]"
                            value="{{ $day }}"
                            class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            @checked(in_array($day, array_map('intval', $hariAktif), true))
                            :disabled="jenis !== 'happy_hour'"
                        />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <p class="vx-help">Kosongkan = setiap hari dalam rentang jam.</p>
            <x-input-error :messages="$errors->get('hari_aktif')" />
        </div>
    </div>

    <div class="vx-field">
        <label class="vx-label" for="{{ $prefix }}catatan">Catatan (opsional)</label>
        <input
            id="{{ $prefix }}catatan"
            name="catatan"
            type="text"
            class="vx-input w-full"
            value="{{ old('catatan', $discount?->catatan) }}"
            placeholder="Catatan internal"
            maxlength="255"
        />
        <x-input-error :messages="$errors->get('catatan')" />
    </div>

    <div class="vx-field">
        <label class="inline-flex cursor-pointer items-center gap-2">
            <input
                id="{{ $prefix }}is_active"
                type="checkbox"
                name="is_active"
                value="1"
                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                @checked(old('is_active', $discount?->is_active ?? true))
            />
            <span class="text-sm font-medium text-slate-700">Aktif (berlaku di kasir)</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" />
    </div>
</div>
