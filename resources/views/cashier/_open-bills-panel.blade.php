<div class="pc-open-bills">
    <button
        type="button"
        class="pc-open-bills-toggle"
        x-on:click="openBillsPanelOpen = !openBillsPanelOpen"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
        </svg>
        <span>Open Bill</span>
        <span class="pc-open-bills-count" x-show="openBills.length > 0" x-text="openBills.length"></span>
        <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            class="pc-open-bills-chevron"
            :class="{ 'is-open': openBillsPanelOpen }"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>

    <div
        class="pc-open-bills-list"
        x-show="openBillsPanelOpen && openBills.length > 0"
        x-transition
        x-cloak
    >
        <template x-for="bill in openBills" :key="bill.id">
            <div class="pc-open-bill-row">
                <div class="pc-open-bill-info">
                    <p class="pc-open-bill-id" x-text="'#' + String(bill.id).padStart(5, '0')"></p>
                    <p class="pc-open-bill-meta">
                        <span x-text="formatRp(bill.total)"></span>
                        <span x-show="bill.order_type" x-text="' · ' + (bill.order_type === 'take' ? 'Take Away' : 'Dine In')"></span>
                        <span x-show="bill.items_count" x-text="' · ' + bill.items_count + ' item'"></span>
                    </p>
                    <p
                        class="pc-open-bill-preview"
                        x-show="bill.items_preview && bill.items_preview.length"
                        x-text="bill.items_preview.join(', ')"
                    ></p>
                </div>
                <button type="button" class="pc-open-bill-pay" x-on:click="openSettleModal(bill)">
                    Bayar
                </button>
            </div>
        </template>
    </div>

    <p class="pc-open-bills-empty" x-show="openBillsPanelOpen && openBills.length === 0" x-cloak>
        Tidak ada open bill aktif.
    </p>
</div>
