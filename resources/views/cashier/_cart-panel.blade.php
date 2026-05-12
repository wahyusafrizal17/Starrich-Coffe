<div class="flex min-h-0 flex-1 flex-col">
    <div class="pc-order-header">
        <h2>Pesanan</h2>
        <div class="pc-order-meta">
            <button
                type="button"
                class="pc-order-type-btn"
                :class="{ active: orderType === 'dine' }"
                x-on:click="orderType = 'dine'"
            >
                Dine In
            </button>
            <button
                type="button"
                class="pc-order-type-btn"
                :class="{ active: orderType === 'take' }"
                x-on:click="orderType = 'take'"
            >
                Take Away
            </button>
        </div>
    </div>

    <div class="pc-cart-items">
        <template x-for="item in cart" :key="item.product_id">
            <div class="pc-cart-item">
                <div class="pc-cart-thumb">
                    <img x-show="item.gambar" :src="item.gambar" alt="" loading="lazy" />
                    <span x-show="!item.gambar" class="opacity-70" x-text="emojiIcon(item)" x-cloak></span>
                </div>
                <div class="pc-cart-info">
                    <div class="pc-cart-name" x-text="item.nama_produk"></div>
                    <div class="pc-cart-price" x-text="formatRp(item.harga)"></div>
                </div>
                <div class="pc-qty-ctrl">
                    <button type="button" class="pc-qty-btn" x-on:click="dec(item)" aria-label="Kurangi">−</button>
                    <span class="pc-qty-num" x-text="item.qty"></span>
                    <button type="button" class="pc-qty-btn" x-on:click="inc(item)" aria-label="Tambah">+</button>
                </div>
                <button type="button" class="pc-remove-link" x-on:click="removeItem(item)">hapus</button>
            </div>
        </template>
        <div class="pc-cart-empty" x-show="cart.length === 0" x-cloak>
            <div class="pc-empty-icon">☕</div>
            <span>Belum ada pesanan</span>
            <span style="font-size: 11px; opacity: 0.7">Pilih menu untuk menambahkan</span>
        </div>
    </div>

    <div class="pc-order-summary">
        <div class="pc-summary-row pc-total">
            <span>Total</span>
            <span x-text="formatRp(cartTotal)"></span>
        </div>

        <button
            type="button"
            x-on:click="openPaymentModal()"
            :disabled="paying || cart.length === 0"
            class="pc-checkout-btn"
            style="margin-top: 12px"
        >
            <svg class="pc-checkout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-18 0v10.125a1.125 1.125 0 001.125 1.125h15.75a1.125 1.125 0 001.125-1.125V9m-17.25 4.125h15m-15 3h15" />
            </svg>
            <span x-show="!paying">Proses pembayaran</span>
            <span x-show="paying" x-cloak>Memproses…</span>
        </button>
    </div>

    {{-- Modal pembayaran (metode + split bill) --}}
    <div
        class="pc-pay-modal-overlay"
        x-show="payModalOpen"
        x-cloak
        x-on:click="closePaymentModal()"
        x-transition.opacity
    >
        <div
            class="pc-pay-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pc-pay-modal-title"
            x-on:click.stop
        >
            <header class="pc-pay-modal-head">
                <h3 id="pc-pay-modal-title" class="pc-pay-modal-title">Pembayaran</h3>
                <div class="pc-pay-modal-tagihan-block">
                    <span class="pc-pay-modal-tagihan-label">Tagihan</span>
                    <strong class="pc-pay-modal-tagihan-amount" x-text="formatRp(cartTotal)"></strong>
                </div>
            </header>

            <p class="pc-pay-modal-section-label">Metode & nominal</p>
            <template x-for="(row, idx) in paymentSplits" :key="idx">
                <div class="pc-split-row">
                    <select class="pc-pay-modal-select" x-model="row.metode">
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                    <input
                        type="text"
                        class="pc-split-amount"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="0"
                        :value="row.jumlah"
                        x-on:input="onSplitNominalInput(row, $event)"
                    />
                    <button
                        type="button"
                        class="pc-split-remove"
                        x-show="paymentSplits.length > 1"
                        x-on:click="removeSplitRow(idx)"
                        aria-label="Hapus baris"
                    >
                        ×
                    </button>
                </div>
            </template>

            <button type="button" class="pc-split-add" x-on:click="addSplitRow()">
                + Tambah pembagian
            </button>

            <div class="pc-pay-modal-summary">
                <div class="pc-pay-modal-summary-row">
                    <span>Terbayar</span>
                    <span x-text="formatRp(splitPaidTotal)"></span>
                </div>
                <div class="pc-pay-modal-summary-row">
                    <span>Kembalian</span>
                    <span x-text="formatRp(splitKembalian)"></span>
                </div>
            </div>

            <div class="pc-pay-modal-actions">
                <button type="button" class="pc-pay-modal-cancel" x-on:click="closePaymentModal()">
                    Batal
                </button>
                <button
                    type="button"
                    class="pc-pay-modal-confirm"
                    x-on:click="submitCheckout()"
                    :disabled="paying"
                >
                    <span x-show="!paying">Bayar</span>
                    <span x-show="paying" x-cloak>Memproses…</span>
                </button>
            </div>
        </div>
    </div>
</div>
