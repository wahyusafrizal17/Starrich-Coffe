{{-- Modal pilih Ice/Hot — parent: StarrichPos Alpine --}}
<div
    class="pc-varian-overlay"
    x-show="varianModalOpen"
    x-cloak
    x-on:click="closeVarianModal()"
    x-transition.opacity
>
    <div
        class="pc-varian-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pc-varian-title"
        x-on:click.stop
    >
        <h3 id="pc-varian-title" class="pc-varian-title">Pilih suhu</h3>
        <p class="pc-varian-product" x-show="varianModalProduct" x-text="varianModalProduct?.nama_produk"></p>
        <div class="pc-varian-btns">
            <button type="button" class="pc-varian-pick pc-varian-ice" x-on:click="pickVarianSuhu('ice')">Ice</button>
            <button type="button" class="pc-varian-pick pc-varian-hot" x-on:click="pickVarianSuhu('hot')">Hot</button>
        </div>
        <button type="button" class="pc-varian-cancel" x-on:click="closeVarianModal()">Batal</button>
    </div>
</div>
