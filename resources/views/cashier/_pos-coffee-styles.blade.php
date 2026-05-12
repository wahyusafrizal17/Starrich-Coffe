{{-- Gaya POS identik biru merek (logo), class prefix pc-* --}}
<style>
    .pos-coffee {
        --cream: #eff6ff;
        --warm-white: #f8fafc;
        --espresso: #0a1628;
        --brown-dark: #1e3a5f;
        --brown-mid: #334155;
        --brown-light: #64748b;
        --caramel: #3b82f6;
        --caramel-light: #dbeafe;
        --sage: #475569;
        --sage-light: #e2e8f0;
        --border: rgba(15, 23, 42, 0.1);
        --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.06);
        --shadow-md: 0 8px 32px rgba(15, 23, 42, 0.1);
        --radius: 16px;
        --radius-sm: 10px;
        font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
        background: var(--cream);
        color: var(--espresso);
        -webkit-font-smoothing: antialiased;
    }

    .pc-header {
        background: var(--espresso);
        color: var(--caramel-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 clamp(14px, 3vw, 28px);
        height: 60px;
        flex-shrink: 0;
        gap: 12px;
    }
    .pc-header .brand {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        line-height: 0;
    }
    .pc-header-logo {
        display: block;
        height: clamp(30px, 4.5vw, 42px);
        width: auto;
        max-width: min(200px, 42vw);
        object-fit: contain;
        object-position: left center;
    }
    .pc-header-meta {
        display: flex;
        align-items: center;
        gap: clamp(10px, 2vw, 18px);
        font-size: 12px;
        font-weight: 300;
        color: rgba(191, 219, 254, 0.7);
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .pc-header-meta strong { color: var(--caramel-light); font-weight: 500; }
    .pc-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pc-header-actions a,
    .pc-header-actions button {
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        color: var(--caramel-light);
        text-decoration: none;
        padding: 6px 10px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(191, 219, 254, 0.28);
        background: transparent;
        cursor: pointer;
        transition: background 0.15s ease;
        white-space: nowrap;
    }
    .pc-header-actions a:hover,
    .pc-header-actions button:hover { background: rgba(255,255,255,0.08); }

    .pc-header-actions .pc-logout-btn {
        padding: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 36px;
    }
    .pc-header-logout-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }
    .pc-header-logout-icon path {
        stroke: currentColor;
    }

    .pc-main {
        display: grid;
        grid-template-columns: 1fr minmax(280px, 360px);
        flex: 1;
        overflow: hidden;
        min-height: 0;
    }

    .pc-left {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 20px 20px 20px 24px;
        gap: 14px;
        min-width: 0;
    }

    .pc-categories {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        overflow-x: auto;
        scrollbar-width: none;
        padding-bottom: 4px;
    }
    .pc-categories::-webkit-scrollbar { display: none; }

    .pc-cat-btn {
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 50px;
        border: 1.5px solid var(--border);
        background: var(--warm-white);
        color: var(--brown-mid);
        cursor: pointer;
        transition: all 0.18s ease;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .pc-cat-btn:hover { border-color: var(--brown-light); color: var(--brown-dark); }
    .pc-cat-btn.active {
        background: var(--espresso);
        color: var(--caramel-light);
        border-color: var(--espresso);
    }

    .pc-search-wrap { position: relative; flex-shrink: 0; }
    .pc-search-wrap svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--brown-light);
        pointer-events: none;
    }
    .pc-search-input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--warm-white);
        font-family: inherit;
        font-size: 14px;
        color: var(--espresso);
        outline: none;
        transition: border-color 0.18s;
    }
    .pc-search-input::placeholder { color: var(--brown-light); }
    .pc-search-input:focus { border-color: var(--caramel); }

    .pc-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        align-items: start;
        align-content: start;
        overflow-y: auto;
        padding-right: 6px;
        flex: 1;
        min-height: 0;
    }
    .pc-menu-grid::-webkit-scrollbar { width: 4px; }
    .pc-menu-grid::-webkit-scrollbar-track { background: transparent; }
    .pc-menu-grid::-webkit-scrollbar-thumb { background: var(--caramel-light); border-radius: 4px; }

    .pc-menu-card {
        background: var(--warm-white);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        padding: 10px 10px 9px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 3px;
        position: relative;
        overflow: hidden;
        text-align: left;
        height: fit-content;
        align-self: start;
        width: 100%;
        min-width: 0;
    }
    .pc-menu-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--caramel);
        transform: scaleX(0);
        transition: transform 0.2s ease;
    }
    .pc-menu-card:hover:not(:disabled) { border-color: var(--caramel); box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .pc-menu-card:hover:not(:disabled)::before { transform: scaleX(1); }
    .pc-menu-card:disabled,
    .pc-menu-card.pc-out-of-stock { opacity: 0.45; cursor: not-allowed; transform: none; }

    .pc-card-thumb {
        height: 72px;
        width: 100%;
        border-radius: var(--radius-sm);
        background: var(--cream);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 2px;
        flex-shrink: 0;
    }
    .pc-card-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pc-card-emoji { font-size: 28px; }
    .pc-card-name { font-size: 13px; font-weight: 500; color: var(--espresso); line-height: 1.3; }
    .pc-card-desc { font-size: 11px; color: var(--brown-light); line-height: 1.35; }
    .pc-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 3px;
        gap: 6px;
    }
    .pc-card-price { font-size: 13px; font-weight: 500; color: var(--brown-dark); }
    .pc-card-tag {
        font-size: 9px;
        font-weight: 500;
        padding: 3px 7px;
        border-radius: 20px;
        background: var(--sage-light);
        color: var(--sage);
        flex-shrink: 0;
    }

    .pc-right {
        background: var(--warm-white);
        border-left: 1.5px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
    }

    .pc-order-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px 12px;
        border-bottom: 1.5px solid var(--border);
        flex-shrink: 0;
    }
    .pc-order-header h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 17px;
        font-weight: 500;
        color: var(--espresso);
        margin: 0;
        line-height: 1.2;
    }
    .pc-order-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        flex-shrink: 0;
        justify-content: flex-end;
    }

    .pc-order-type-btn {
        font-family: inherit;
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        background: transparent;
        color: var(--brown-mid);
        cursor: pointer;
        transition: all 0.18s;
    }
    .pc-order-type-btn.active {
        background: var(--espresso);
        color: var(--caramel-light);
        border-color: var(--espresso);
    }

    .pc-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 10px 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 0;
    }
    .pc-cart-items::-webkit-scrollbar { width: 3px; }
    .pc-cart-items::-webkit-scrollbar-thumb { background: var(--caramel-light); border-radius: 4px; }

    .pc-cart-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--brown-light);
        font-size: 13px;
        text-align: center;
        padding: 24px 12px;
    }
    .pc-cart-empty .pc-empty-icon { font-size: 40px; opacity: 0.4; }

    .pc-cart-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        background: var(--cream);
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
    }
    .pc-cart-thumb {
        width: 40px; height: 40px;
        border-radius: 8px;
        background: var(--warm-white);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 18px;
    }
    .pc-cart-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pc-cart-info { flex: 1; min-width: 0; }
    .pc-cart-name { font-size: 12px; font-weight: 500; color: var(--espresso); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pc-cart-price { font-size: 11px; color: var(--brown-mid); }
    .pc-qty-ctrl { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
    .pc-qty-btn {
        width: 26px; height: 26px;
        border-radius: 50%;
        border: 1.5px solid var(--border);
        background: var(--warm-white);
        cursor: pointer;
        font-size: 15px;
        display: flex; align-items: center; justify-content: center;
        color: var(--brown-dark);
        transition: all 0.15s;
        line-height: 1;
    }
    .pc-qty-btn:hover { background: var(--espresso); color: var(--caramel-light); border-color: var(--espresso); }
    .pc-qty-num { font-size: 13px; font-weight: 500; min-width: 18px; text-align: center; color: var(--espresso); }

    .pc-remove-link {
        font-size: 10px;
        font-weight: 500;
        color: #2563eb;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0 0 0 4px;
        text-decoration: underline;
    }

    .pc-order-summary {
        border-top: 1.5px solid var(--border);
        padding: 12px 18px 16px;
        flex-shrink: 0;
    }
    .pc-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--brown-mid);
        padding: 3px 0;
    }
    .pc-summary-row.pc-total {
        font-size: 15px;
        font-weight: 500;
        color: var(--espresso);
        padding-top: 0;
        margin-top: 0;
    }

    .pc-pay-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
    }
    .pc-pay-modal {
        width: 100%;
        max-width: 380px;
        max-height: min(90dvh, 560px);
        overflow-y: auto;
        background: var(--warm-white);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-md);
        padding: 18px 18px 16px;
    }
    .pc-pay-modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1.5px solid var(--border);
    }
    .pc-pay-modal-head .pc-pay-modal-title {
        margin: 0;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 18px;
        font-weight: 500;
        color: var(--espresso);
        line-height: 1.25;
    }
    .pc-pay-modal-tagihan-block {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .pc-pay-modal-tagihan-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--brown-mid);
        letter-spacing: 0.02em;
    }
    .pc-pay-modal-tagihan-amount {
        font-size: 15px;
        font-weight: 600;
        color: var(--espresso);
        font-variant-numeric: tabular-nums;
    }
    .pc-pay-modal-section-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--brown-mid);
        margin: 0 0 8px;
    }
    .pc-split-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .pc-pay-modal-select {
        flex: 0 0 auto;
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--cream);
        color: var(--espresso);
    }
    .pc-split-amount {
        flex: 1;
        min-width: 0;
        font-family: inherit;
        font-size: 14px;
        font-weight: 500;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--cream);
        color: var(--espresso);
        text-align: right;
    }
    .pc-split-amount:focus,
    .pc-pay-modal-select:focus {
        outline: none;
        border-color: var(--caramel);
    }
    .pc-split-remove {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--cream);
        color: var(--brown-mid);
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        transition: background 0.15s;
    }
    .pc-split-remove:hover { background: var(--sage-light); color: var(--espresso); }
    .pc-split-add {
        width: 100%;
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        padding: 8px;
        margin-bottom: 12px;
        border-radius: var(--radius-sm);
        border: 1.5px dashed var(--brown-light);
        background: transparent;
        color: var(--brown-mid);
        cursor: pointer;
    }
    .pc-split-add:hover { border-color: var(--caramel); color: var(--espresso); }
    .pc-pay-modal-summary {
        background: var(--sage-light);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        margin-bottom: 14px;
    }
    .pc-pay-modal-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--brown-dark);
        padding: 2px 0;
    }
    .pc-pay-modal-summary-row span:last-child {
        font-weight: 600;
        color: var(--espresso);
    }
    .pc-pay-modal-actions {
        display: flex;
        gap: 10px;
    }
    .pc-pay-modal-cancel {
        flex: 1;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        padding: 11px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--cream);
        color: var(--brown-mid);
        cursor: pointer;
    }
    .pc-pay-modal-confirm {
        flex: 1;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        padding: 11px;
        border-radius: var(--radius-sm);
        border: none;
        background: var(--caramel);
        color: white;
        cursor: pointer;
        transition: background 0.15s;
    }
    .pc-pay-modal-confirm:hover:not(:disabled) { background: #1d4ed8; }
    .pc-pay-modal-confirm:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .pc-checkout-btn {
        width: 100%;
        font-family: inherit;
        font-size: 14px;
        font-weight: 500;
        padding: 13px;
        border-radius: var(--radius);
        background: var(--caramel);
        color: white;
        border: none;
        cursor: pointer;
        letter-spacing: 0.2px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .pc-checkout-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }
    .pc-checkout-icon path {
        stroke: currentColor;
    }
    .pc-checkout-btn:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-1px); box-shadow: var(--shadow-md); }
    .pc-checkout-btn:active:not(:disabled) { transform: translateY(0); }
    .pc-checkout-btn:disabled {
        background: var(--caramel-light);
        color: var(--brown-light);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 1023px) {
        .pc-main { grid-template-columns: 1fr; }
        .pc-right.pc-right-sidebar { display: none; }
    }
</style>
