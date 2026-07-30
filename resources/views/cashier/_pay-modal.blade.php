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
            <h3 id="pc-pay-modal-title" class="pc-pay-modal-title" x-text="settlingBill ? 'Bayar Open Bill' : (editingOpenBillId ? 'Bayar (edit open bill)' : (isKaryawanPayment ? 'Pencatatan Karyawan' : 'Pembayaran'))"></h3>
            <div class="pc-pay-modal-tagihan-block">
                <span class="pc-pay-modal-tagihan-label" x-text="isKaryawanPayment ? 'Nilai pesanan' : 'Tagihan'"></span>
                <strong class="pc-pay-modal-tagihan-amount" x-text="formatRp(payModalTotal)"></strong>
            </div>
            <p class="pc-pay-modal-settle-hint" x-show="settlingBill" x-cloak>
                No. <span x-text="settlingBill ? '#' + String(settlingBill.id).padStart(5, '0') : ''"></span>
                    <span x-show="settlingBill?.nama_pelanggan" x-text="settlingBill?.nama_pelanggan ? ' / ' + settlingBill.nama_pelanggan : ''"></span>
            </p>
        </header>

        <div class="pc-pay-modal-name-wrap" x-show="isKaryawanPayment || (!settlingBill && !editingOpenBillId)" x-cloak>
            <label class="pc-pay-modal-section-label" for="pc-open-bill-name" x-text="isKaryawanPayment ? 'Nama karyawan' : 'Nama pelanggan'"></label>
            <input
                id="pc-open-bill-name"
                type="text"
                class="pc-pay-modal-name-input"
                x-model="openBillName"
                :placeholder="isKaryawanPayment ? 'Contoh: Budi (barista)' : 'Contoh: Budi / Meja 3'"
                maxlength="100"
                autocomplete="off"
            />
        </div>

        <p class="pc-pay-modal-section-label">Metode &amp; nominal</p>
        <p class="pc-pay-modal-karyawan-hint" x-show="isKaryawanPayment" x-cloak>
            Pesanan karyawan dicatat tanpa pembayaran dan tidak masuk omzet.
        </p>
        <template x-for="(row, idx) in paymentSplits" :key="idx">
            <div class="pc-split-row">
                <select
                    class="pc-pay-modal-select"
                    x-model="row.metode"
                    x-on:change="onPaymentMethodChange(row)"
                >
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="karyawan">Karyawan (gratis)</option>
                </select>
                <input
                    type="text"
                    class="pc-split-amount"
                    inputmode="numeric"
                    autocomplete="off"
                    :placeholder="row.metode === 'karyawan' ? 'Gratis' : '0'"
                    :value="row.metode === 'karyawan' ? '0' : row.jumlah"
                    :readonly="row.metode === 'karyawan'"
                    :disabled="row.metode === 'karyawan'"
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

        <button
            type="button"
            class="pc-split-add"
            x-show="!isKaryawanPayment"
            x-cloak
            x-on:click="addSplitRow()"
        >
            + Tambah pembagian
        </button>

        <div class="pc-pay-modal-summary">
            <div class="pc-pay-modal-summary-row">
                <span x-text="isKaryawanPayment ? 'Dibayar' : 'Terbayar'"></span>
                <span x-text="formatRp(isKaryawanPayment ? 0 : splitPaidTotal)"></span>
            </div>
            <div class="pc-pay-modal-summary-row" x-show="!isKaryawanPayment">
                <span>Kembalian</span>
                <span x-text="formatRp(splitKembalian)"></span>
            </div>
        </div>

        <div class="pc-pay-modal-actions">
            <button
                type="button"
                class="pc-pay-modal-open-bill"
                x-show="!settlingBill && !editingOpenBillId && !isKaryawanPayment"
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
                <span x-show="!paying" x-text="isKaryawanPayment ? 'Catat' : (settlingBill ? 'Lunas' : 'Bayar')"></span>
                <span x-show="paying" x-cloak>Memproses…</span>
            </button>
        </div>
    </div>
</div>
