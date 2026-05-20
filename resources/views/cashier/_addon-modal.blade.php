{{-- Modal add-on (biji, susu, dll.) — parent: StarrichPos Alpine --}}
<div
    class="pc-addon-overlay"
    x-show="addonModalOpen"
    x-cloak
    x-on:click="closeAddonModal()"
    x-transition.opacity
>
    <div
        class="pc-addon-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pc-addon-title"
        x-on:click.stop
    >
        <h3 id="pc-addon-title" class="pc-addon-title">Tambahan</h3>
        <p class="pc-addon-product" x-show="addonModalProduct" x-text="addonModalProduct?.nama_produk"></p>
        <p class="pc-addon-hint">Opsional — centang yang dipakai pelanggan.</p>

        <div class="pc-addon-list">
            <template x-for="opt in addonsCatalog" :key="opt.code">
                <label class="pc-addon-row">
                    <input type="checkbox" class="pc-addon-check" x-model="addonModalSelected[opt.code]" />
                    <span class="pc-addon-row-text">
                        <span class="pc-addon-label" x-text="opt.label"></span>
                        <span class="pc-addon-price" x-text="'+ ' + formatRp(opt.harga)"></span>
                    </span>
                </label>
            </template>
        </div>

        <p class="pc-addon-extra" x-show="addonModalExtraPreview > 0" x-cloak>
            Tambahan per cup: <strong x-text="formatRp(addonModalExtraPreview)"></strong>
        </p>

        <div class="pc-addon-actions">
            <button type="button" class="pc-addon-cancel" x-on:click="closeAddonModal()">Batal</button>
            <button type="button" class="pc-addon-confirm" x-on:click="confirmAddonModal()">Tambah ke pesanan</button>
        </div>
    </div>
</div>
