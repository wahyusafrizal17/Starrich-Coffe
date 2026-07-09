<div class="order-side">
    <div class="order-header">
        <div class="order-title-row">
            <div class="order-title-wrap">
                <h2 class="order-title">Pesanan</h2>
                <span class="order-count" x-show="cart.length > 0" x-text="cart.length + ' item'" x-cloak></span>
            </div>
            <div class="mode-toggle" role="group" aria-label="Jenis pesanan">
                <button
                    type="button"
                    :class="{ active: orderType === 'dine' }"
                    x-on:click="orderType = 'dine'"
                >
                    Dine In
                </button>
                <button
                    type="button"
                    :class="{ active: orderType === 'take' }"
                    x-on:click="orderType = 'take'"
                >
                    Take Away
                </button>
            </div>
        </div>
    </div>

    <div class="order-body">
        <div class="order-list" x-show="cart.length > 0" x-cloak>
            <template x-for="item in cart" :key="String(item.product_id) + '-' + (item.suhu || '') + '-' + addonsKey(item)">
                <div class="cart-item">
                    <div class="cart-item-main">
                        <div class="cart-item-info">
                            <span class="cart-item-name" x-text="item.nama_produk"></span>
                            <span class="cart-item-sub" x-text="formatRp(item.harga) + ' × ' + item.qty"></span>
                            <div class="cart-item-tags" x-show="item.suhu === 'ice' || item.suhu === 'hot' || formatAddonsLine(item)" x-cloak>
                                <span
                                    class="cart-tag"
                                    x-show="item.suhu === 'ice' || item.suhu === 'hot'"
                                    x-text="item.suhu === 'ice' ? 'Ice' : 'Hot'"
                                ></span>
                                <span class="cart-tag" x-show="formatAddonsLine(item)" x-text="formatAddonsLine(item)"></span>
                            </div>
                        </div>
                        <span class="cart-item-price" x-text="formatRp(item.harga * item.qty)"></span>
                    </div>
                    <div class="cart-item-actions">
                        <div class="stepper">
                            <button type="button" aria-label="Kurangi" x-on:click="dec(item)">−</button>
                            <span x-text="item.qty"></span>
                            <button type="button" aria-label="Tambah" x-on:click="inc(item)">+</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="order-empty" x-show="cart.length === 0" x-cloak>
            <div class="order-empty-icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 8h14l-1.2 10.2A2 2 0 0 1 14.8 20H7.2a2 2 0 0 1-2-1.8L4 8Z"/>
                    <path d="M18 8a3 3 0 0 0 0-6"/>
                </svg>
            </div>
            <p>Belum ada pesanan</p>
            <span>Pilih menu untuk menambahkan</span>
        </div>
    </div>

    <div class="order-footer">
        <div class="order-checkout">
            <div class="summary-block">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span x-text="formatRp(cartTotal)"></span>
                </div>
                <div class="summary-row">
                    <span>Pajak (10%)</span>
                    <span>Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span class="label">Total</span>
                    <span class="value" x-text="formatRp(cartTotal)"></span>
                </div>
            </div>

            <div class="cashier-field">
                <label for="sr-cashier-name">Kasir yang melayani</label>
                <input
                    id="sr-cashier-name"
                    type="text"
                    x-model="cashierName"
                    :readonly="!isAdmin"
                    :placeholder="isAdmin ? 'Ketik nama kasir...' : ''"
                    maxlength="100"
                    autocomplete="off"
                />
            </div>

            <button
                type="button"
                class="pay-btn"
                x-on:click="openPaymentModal()"
                :disabled="paying || cart.length === 0 || !cashierEcho() || cashierEcho() === '—'"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                </svg>
                <span x-show="!paying">Proses Pembayaran</span>
                <span x-show="paying" x-cloak>Memproses…</span>
            </button>
        </div>
    </div>
</div>
