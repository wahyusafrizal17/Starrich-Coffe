{{-- Modal pembayaran (metode + split bill) — butuh parent Alpine StarrichPos --}}
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
            <h3 id="pc-pay-modal-title" class="pc-pay-modal-title" x-text="settlingBill ? 'Bayar Open Bill' : 'Pembayaran'"></h3>
            <div class="pc-pay-modal-tagihan-block">
                <span class="pc-pay-modal-tagihan-label">Tagihan</span>
                <strong class="pc-pay-modal-tagihan-amount" x-text="formatRp(payModalTotal)"></strong>
            </div>
            <p class="pc-pay-modal-settle-hint" x-show="settlingBill" x-cloak>
                No. <span x-text="settlingBill ? '#' + String(settlingBill.id).padStart(5, '0') : ''"></span>
                <span x-show="settlingBill?.nama_pelanggan" x-text="settlingBill?.nama_pelanggan ? ' · ' + settlingBill.nama_pelanggan : ''"></span>
            </p>
        </header>

        <div class="pc-pay-modal-name-wrap" x-show="!settlingBill" x-cloak>
            <label class="pc-pay-modal-section-label" for="pc-open-bill-name">Nama pelanggan</label>
            <input
                id="pc-open-bill-name"
                type="text"
                class="pc-pay-modal-name-input"
                x-model="openBillName"
                placeholder="Contoh: Budi / Meja 3"
                maxlength="100"
                autocomplete="off"
            />
        </div>

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
            <button
                type="button"
                class="pc-pay-modal-open-bill"
                x-show="!settlingBill"
                x-on:click="submitOpenBill()"
                :disabled="paying"
            >
                <span x-show="!paying">Open Bill</span>
                <span x-show="paying" x-cloak>Menyimpan…</span>
            </button>
            <button
                type="button"
                class="pc-pay-modal-confirm"
                x-on:click="submitCheckout()"
                :disabled="paying"
            >
                <span x-show="!paying" x-text="settlingBill ? 'Lunas' : 'Bayar'"></span>
                <span x-show="paying" x-cloak>Memproses…</span>
            </button>
        </div>
    </div>
</div>
