{{-- Gaya kasir — dari referensi codingan --}}
<style>
  .pos-kasir {
    --primary: #1E40AF;
    --primary-strong: #152E82;
    --primary-soft: #EEF2FC;
    --primary-soft-2: #E1E9FA;
    --white: #FFFFFF;
    --surface: #F7F8FB;
    --surface-2: #F1F3F8;
    --ink: #111827;
    --ink-soft: #5B6472;
    --ink-faint: #98A1AF;
    --line: #E7EAF0;
    --success: #1E8A5B;
    --radius-lg: 20px;
    --radius-md: 13px;
    --radius-sm: 9px;
    --order-w: 400px;
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    color: var(--ink);
    background: var(--white);
    min-height: 100svh;
    position: relative;
  }

  .pos-kasir .sr-app {
    display: grid;
    grid-template-columns: 1fr var(--order-w);
    min-height: 100svh;
  }

  .pos-kasir .menu-side {
    padding: 15px 25px 0px;
    border-right: 1px solid var(--line);
    min-width: 0;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  .pos-kasir .brand-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-shrink: 0;
    gap: 16px;
  }

  .pos-kasir .brand {
    display: flex;
    align-items: center;
    gap: 13px;
    min-width: 0;
  }

  .pos-kasir .brand-mark {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    background: linear-gradient(155deg, var(--primary), var(--primary-strong));
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    font-weight: 600;
    color: var(--white);
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 6px 16px -4px rgba(30, 64, 175, 0.4);
    overflow: hidden;
  }

  .pos-kasir .brand-mark img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .pos-kasir .brand-text h1 {
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    font-weight: 600;
    font-size: 22px;
    letter-spacing: 0.01em;
    color: var(--ink);
    margin: 0;
    line-height: 1.2;
  }

  .pos-kasir .brand-text span {
    display: block;
    font-size: 11px;
    letter-spacing: 0.13em;
    text-transform: uppercase;
    color: var(--ink-faint);
    margin-top: 2px;
  }

  .pos-kasir .shift-info {
    text-align: right;
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    font-size: 12px;
    color: var(--ink-faint);
    line-height: 1.6;
    flex-shrink: 0;
  }

  .pos-kasir .shift-info b {
    color: var(--primary);
    font-weight: 500;
  }

  .pos-kasir .sr-edit-banner {
    flex-shrink: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 14px;
    margin-bottom: 16px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--primary-soft-2);
    background: var(--primary-soft);
    font-size: 12px;
    color: var(--ink-soft);
  }

  .pos-kasir .sr-edit-banner strong {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--primary);
    margin-bottom: 2px;
  }

  .pos-kasir .sr-edit-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .pos-kasir .sr-edit-actions button {
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 999px;
    cursor: pointer;
    border: 1px solid var(--line);
    background: var(--white);
    color: var(--ink-soft);
  }

  .pos-kasir .sr-edit-actions .is-primary {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
  }

  .pos-kasir .controls {
    display: flex;
    gap: 14px;
    margin-bottom: 26px;
    flex-wrap: wrap;
    flex-shrink: 0;
  }

  .pos-kasir .search-wrap {
    flex: 1;
    min-width: 220px;
    position: relative;
  }

  .pos-kasir .search-wrap svg {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ink-faint);
    pointer-events: none;
  }

  .pos-kasir .search-wrap input {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 13px 18px 13px 44px;
    color: var(--ink);
    font-family: inherit;
    font-size: 14px;
    outline: none;
    transition: border-color 0.18s ease, background 0.18s ease;
  }

  .pos-kasir .search-wrap input::placeholder { color: var(--ink-faint); }
  .pos-kasir .search-wrap input:focus {
    border-color: var(--primary);
    background: var(--white);
  }

  .pos-kasir .filter-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .pos-kasir .pill {
    background: var(--white);
    border: 1px solid var(--line);
    color: var(--ink-soft);
    padding: 11px 20px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.16s ease;
    font-family: inherit;
  }

  .pos-kasir .pill:hover { border-color: var(--primary); color: var(--primary); }
  .pos-kasir .pill.active {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
    font-weight: 600;
    box-shadow: 0 4px 12px -3px rgba(30, 64, 175, 0.35);
  }

  .pos-kasir .menu-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 12px;
    flex: 1 1 0;
    min-height: 0;
    overflow-y: auto;
    align-content: start;
    padding-right: 4px;
  }

  .pos-kasir .menu-grid::-webkit-scrollbar { width: 5px; }
  .pos-kasir .menu-grid::-webkit-scrollbar-thumb { background: #D8DCE5; border-radius: 999px; }

  .pos-kasir .item-card {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    background: var(--white);
    border: 1px solid #E8ECF2;
    border-radius: 20px;
    padding: 10px;
    cursor: pointer;
    transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
    position: relative;
    overflow: visible;
    text-align: left;
    font-family: inherit;
    box-shadow: 0 1px 2px rgba(17, 24, 39, 0.04);
    min-width: 0;
  }

  .pos-kasir .item-card:hover {
    border-color: #D5DBE8;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px -14px rgba(17, 24, 39, 0.18);
  }

  .pos-kasir .item-card:active {
    transform: translateY(0);
    box-shadow: 0 4px 12px -10px rgba(17, 24, 39, 0.14);
  }

  .pos-kasir .item-media {
    position: relative;
    flex-shrink: 0;
    width: 100%;
  }

  .pos-kasir .item-icon {
    width: 100%;
    aspect-ratio: 4 / 3;
    border-radius: 14px;
    background: #F8FAFC;
    border: 1px solid #EEF2F7;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    overflow: hidden;
  }

  .pos-kasir .item-icon.is-fallback {
    background: linear-gradient(180deg, #F8FAFC 0%, #EEF2F7 100%);
  }

  .pos-kasir .item-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .pos-kasir .item-emoji {
    font-size: 28px;
    line-height: 1;
  }

  .pos-kasir .item-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1 1 auto;
    min-width: 0;
    min-height: 0;
    padding: 0 2px 2px;
    flex-shrink: 0;
  }

  .pos-kasir .item-name {
    font-size: 13px;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.4;
    letter-spacing: -0.01em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
    min-height: 2.8em;
  }

  .pos-kasir .item-meta {
    display: flex;
    align-items: center;
    margin-top: auto;
    width: 100%;
  }

  .pos-kasir .item-price {
    font-size: 12px;
    color: #1E40AF;
    font-weight: 700;
    letter-spacing: -0.01em;
    white-space: nowrap;
  }

  .pos-kasir .qty-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    z-index: 2;
    background: var(--primary);
    color: var(--white);
    font-size: 10px;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--white);
    box-shadow: 0 4px 10px -4px rgba(30, 64, 175, 0.55);
    opacity: 0;
    transform: scale(0.6);
    transition: opacity 0.16s ease, transform 0.16s ease;
  }

  .pos-kasir .qty-badge.show { opacity: 1; transform: scale(1); }

  .pos-kasir .empty-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 64px 20px;
    color: var(--ink-faint);
    font-size: 14px;
    background: var(--white);
    border: 1px dashed #D8DCE5;
    border-radius: 20px;
  }

  .pos-kasir .order-side {
    background: #F8F9FC;
    display: flex;
    flex-direction: column;
    height: 100svh;
    position: sticky;
    top: 0;
    border-left: 1px solid var(--line);
    min-width: 0;
  }

  .pos-kasir .order-header {
    padding: 24px 24px 16px;
    flex-shrink: 0;
  }

  .pos-kasir .order-title-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .pos-kasir .order-title-wrap {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
  }

  .pos-kasir .order-title {
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    letter-spacing: -0.02em;
  }

  .pos-kasir .order-count {
    font-size: 12px;
    font-weight: 500;
    color: var(--ink-faint);
  }

  .pos-kasir .mode-toggle {
    display: flex;
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 3px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(17, 24, 39, 0.04);
  }

  .pos-kasir .mode-toggle button {
    background: transparent;
    border: none;
    color: var(--ink-soft);
    font-family: inherit;
    font-size: 11.5px;
    font-weight: 600;
    padding: 7px 12px;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.16s ease, color 0.16s ease, box-shadow 0.16s ease;
    white-space: nowrap;
  }

  .pos-kasir .mode-toggle button.active {
    background: var(--primary);
    color: var(--white);
    box-shadow: 0 2px 8px -2px rgba(30, 64, 175, 0.45);
  }

  .pos-kasir .order-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    margin: 0 24px;
    padding: 2px;
  }

  .pos-kasir .order-body::-webkit-scrollbar { width: 5px; }
  .pos-kasir .order-body::-webkit-scrollbar-thumb { background: #D8DCE5; border-radius: 999px; }

  .pos-kasir .order-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-bottom: 8px;
  }

  .pos-kasir .cart-item {
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 14px 14px 12px;
    box-shadow: 0 1px 2px rgba(17, 24, 39, 0.03);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .pos-kasir .cart-item:hover {
    border-color: #D5DBE8;
    box-shadow: 0 4px 14px -8px rgba(17, 24, 39, 0.12);
  }

  .pos-kasir .cart-item-main {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
  }

  .pos-kasir .cart-item-info {
    flex: 1;
    min-width: 0;
  }

  .pos-kasir .cart-item-name {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.35;
  }

  .pos-kasir .cart-item-sub {
    display: block;
    font-size: 12px;
    color: var(--ink-faint);
    margin-top: 3px;
  }

  .pos-kasir .cart-item-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
  }

  .pos-kasir .cart-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .pos-kasir .cart-item-price {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    white-space: nowrap;
    flex-shrink: 0;
    padding-top: 1px;
  }

  .pos-kasir .cart-item-actions {
    display: flex;
    justify-content: flex-end;
  }

  .pos-kasir .order-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 220px;
    padding: 40px 24px;
    background: var(--white);
    border: 1px dashed #D8DCE5;
    border-radius: 16px;
  }

  .pos-kasir .order-empty-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: var(--surface);
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ink-faint);
    margin-bottom: 14px;
  }

  .pos-kasir .order-empty p {
    color: var(--ink-soft);
    font-size: 14px;
    font-weight: 600;
    margin: 0;
  }

  .pos-kasir .order-empty span {
    color: var(--ink-faint);
    font-size: 12px;
    display: block;
    margin-top: 4px;
  }

  .pos-kasir .stepper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 4px 8px;
  }

  .pos-kasir .stepper button {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    background: var(--white);
    color: var(--primary);
    font-size: 15px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(17, 24, 39, 0.06);
    transition: background 0.12s ease, transform 0.1s ease;
  }

  .pos-kasir .stepper button:hover { background: var(--primary-soft); }
  .pos-kasir .stepper button:active { transform: scale(0.94); }

  .pos-kasir .stepper span {
    font-size: 13px;
    font-weight: 600;
    min-width: 18px;
    text-align: center;
    color: var(--ink);
  }

  .pos-kasir .order-footer {
    padding: 16px 24px 24px;
    flex-shrink: 0;
    background: linear-gradient(180deg, rgba(248, 249, 252, 0) 0%, #F8F9FC 24%);
  }

  .pos-kasir .order-checkout {
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 18px 18px 16px;
    box-shadow: 0 8px 24px -16px rgba(17, 24, 39, 0.18);
  }

  .pos-kasir .summary-block {
    margin-bottom: 4px;
  }

  .pos-kasir .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: var(--ink-soft);
    margin-bottom: 8px;
  }

  .pos-kasir .summary-row.total {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--line);
    margin-bottom: 0;
  }

  .pos-kasir .summary-row.total .label {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
  }

  .pos-kasir .summary-row.total .value {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary);
    letter-spacing: -0.02em;
  }

  .pos-kasir .cashier-field {
    margin: 16px 0 14px;
    padding-top: 16px;
    border-top: 1px solid var(--line);
  }

  .pos-kasir .cashier-field label {
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-faint);
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
  }

  .pos-kasir .cashier-field input {
    width: 100%;
    background: var(--surface);
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 11px 14px;
    color: var(--ink);
    font-family: inherit;
    font-size: 13.5px;
    outline: none;
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
  }

  .pos-kasir .cashier-field input::placeholder { color: var(--ink-faint); }

  .pos-kasir .cashier-field input:focus {
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
  }

  .pos-kasir .pay-btn {
    width: 100%;
    background: linear-gradient(180deg, var(--primary) 0%, var(--primary-strong) 100%);
    border: none;
    border-radius: 12px;
    padding: 15px 16px;
    color: var(--white);
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: filter 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 8px 20px -8px rgba(30, 64, 175, 0.55);
  }

  .pos-kasir .pay-btn:hover:not(:disabled) {
    filter: brightness(1.05);
    box-shadow: 0 10px 24px -8px rgba(30, 64, 175, 0.6);
  }

  .pos-kasir .pay-btn:active:not(:disabled) { transform: scale(0.99); }

  .pos-kasir .pay-btn:disabled {
    background: #ECEEF3;
    color: #A8B0BD;
    cursor: not-allowed;
    box-shadow: none;
  }

  .pos-kasir .cashier-field input:read-only {
    background: var(--surface);
    color: var(--ink-soft);
    cursor: default;
  }

  /* Bottom nav floating dock */
  .pos-kasir .bottom-nav {
    position: fixed;
    left: 0;
    width: calc(100% - var(--order-w));
    bottom: 15px;
    display: flex;
    justify-content: center;
    z-index: 40;
    pointer-events: none;
  }

  .pos-kasir .nav-dock {
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 34px;
    background: rgba(255, 255, 255, 0.86);
    -webkit-backdrop-filter: blur(14px);
    backdrop-filter: blur(14px);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 10px 26px;
    box-shadow: 0 18px 40px -16px rgba(17, 24, 39, 0.22);
  }

  .pos-kasir .nav-item,
  .pos-kasir .nav-item-form button {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    color: var(--ink-faint);
    cursor: pointer;
    font-family: inherit;
    font-size: 10.5px;
    font-weight: 600;
    position: relative;
    padding: 4px 2px;
    transition: color 0.15s ease;
    text-decoration: none;
  }

  .pos-kasir .nav-item svg,
  .pos-kasir .nav-item-form button svg {
    width: 21px;
    height: 21px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
  }

  .pos-kasir .nav-item:hover,
  .pos-kasir .nav-item-form button:hover { color: var(--primary); }
  .pos-kasir .nav-item.active,
  .pos-kasir .nav-item-form button.active { color: var(--primary); }

  .pos-kasir .nav-badge {
    position: absolute;
    top: -4px;
    right: -10px;
    background: var(--primary);
    color: var(--white);
    font-size: 9.5px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
  }

  .pos-kasir .nav-item-form { display: contents; }

  .pos-kasir .nav-item.center,
  .pos-kasir button.nav-item.center {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(155deg, var(--primary), var(--primary-strong));
    color: var(--white);
    margin-top: -5px;
    box-shadow: 0 10px 22px -6px rgba(30, 64, 175, 0.55);
    justify-content: center;
    border: 3px solid var(--white);
    flex-direction: row;
  }

  .pos-kasir .nav-item.center svg { width: 22px; height: 22px; }
  .pos-kasir .nav-item.center span { display: none; }

  .pos-kasir .nav-fab-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: #fff;
    font-size: 9.5px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    border: 2px solid var(--white);
  }

  /* Payment / addon modals */
  .pos-kasir .pc-pay-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(17, 24, 39, 0.45);
    backdrop-filter: blur(4px);
  }

  .pos-kasir .pc-pay-modal {
    width: 100%;
    max-width: 380px;
    max-height: min(90dvh, 560px);
    overflow-y: auto;
    background: var(--white);
    border-radius: var(--radius-md);
    border: 1px solid var(--line);
    box-shadow: 0 18px 40px -16px rgba(17, 24, 39, 0.22);
    padding: 18px;
  }

  .pos-kasir .pc-pay-modal-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 12px;
    margin-bottom: 14px;
    border-bottom: 1px solid var(--line);
  }

  .pos-kasir .pc-pay-modal-title {
    margin: 0;
    font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--ink);
  }

  .pos-kasir .pc-pay-modal-tagihan-label { font-size: 11px; color: var(--ink-faint); text-transform: uppercase; letter-spacing: 0.08em; }
  .pos-kasir .pc-pay-modal-tagihan-amount { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; font-size: 22px; color: var(--primary); font-weight: 600; }
  .pos-kasir .pc-pay-modal-section-label { font-size: 11px; font-weight: 600; color: var(--ink-faint); text-transform: uppercase; letter-spacing: 0.06em; margin: 12px 0 8px; }
  .pos-kasir .pc-pay-modal-karyawan-hint {
    margin: -2px 0 10px;
    padding: 10px 12px;
    border-radius: 10px;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    color: #64748B;
    font-size: 12px;
    line-height: 1.45;
  }
  .pos-kasir .pc-split-amount:disabled {
    background: var(--surface);
    color: var(--ink-faint);
    cursor: default;
  }
  .pos-kasir .pc-pay-modal-name-input,
  .pos-kasir .pc-pay-modal-select,
  .pos-kasir .pc-split-amount {
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 10px 12px;
    font-family: inherit;
    font-size: 14px;
    background: var(--white);
    color: var(--ink);
    box-sizing: border-box;
  }

  .pos-kasir .pc-pay-modal-name-input {
    width: 100%;
  }

  .pos-kasir .pc-split-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
    align-items: center;
  }

  .pos-kasir .pc-split-row .pc-pay-modal-select {
    flex: 0 0 42%;
    min-width: 0;
    width: auto;
  }

  .pos-kasir .pc-split-row .pc-split-amount {
    flex: 1;
    min-width: 0;
    width: auto;
  }

  .pos-kasir .pc-split-remove {
    flex-shrink: 0;
    width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--line);
    background: var(--white); color: var(--ink-soft); cursor: pointer; font-size: 18px; line-height: 1;
  }

  .pos-kasir .pc-split-add {
    width: 100%; margin: 8px 0 12px; padding: 10px; border-radius: var(--radius-sm);
    border: 1px dashed var(--line); background: var(--surface); color: var(--primary);
    font-weight: 600; font-size: 13px; cursor: pointer; font-family: inherit;
  }

  .pos-kasir .pc-pay-modal-summary { border-top: 1px solid var(--line); padding-top: 12px; margin-top: 8px; }
  .pos-kasir .pc-pay-modal-summary-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--ink-soft); margin-bottom: 6px; font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; }
  .pos-kasir .pc-pay-modal-actions { display: flex; gap: 8px; margin-top: 14px; }
  .pos-kasir .pc-pay-modal-open-bill,
  .pos-kasir .pc-pay-modal-confirm {
    flex: 1;
    padding: 9px 12px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    line-height: 1.2;
    min-height: 0;
    cursor: pointer;
    font-family: inherit;
    border: none;
  }

  .pos-kasir .pc-pay-modal-open-bill { background: var(--surface); color: var(--ink-soft); border: 1px solid var(--line); }
  .pos-kasir .pc-pay-modal-confirm { background: var(--primary); color: var(--white); }

  .pos-kasir .pc-modal-overlay {
    position: fixed; inset: 0; z-index: 55; display: flex; align-items: center; justify-content: center;
    padding: 20px; background: rgba(17, 24, 39, 0.45); backdrop-filter: blur(4px);
  }

  .pos-kasir .pc-modal-panel {
    width: 100%; max-width: 400px; background: var(--white); border-radius: var(--radius-md);
    border: 1px solid var(--line); box-shadow: 0 18px 40px -16px rgba(17, 24, 39, 0.22); overflow: hidden;
  }

  .pos-kasir .pc-modal-head {
    padding: 16px 18px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; gap: 12px;
  }

  .pos-kasir .pc-modal-head h3 { margin: 0; font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; font-size: 17px; font-weight: 600; }
  .pos-kasir .pc-modal-body { padding: 16px 18px; }
  .pos-kasir .pc-modal-close { background: none; border: none; cursor: pointer; color: var(--ink-faint); width: 32px; height: 32px; }

  .pos-kasir .pc-varian-overlay,
  .pos-kasir .pc-addon-overlay {
    position: fixed; inset: 0; z-index: 56; display: flex; align-items: center; justify-content: center;
    padding: 20px; background: rgba(17, 24, 39, 0.45); backdrop-filter: blur(4px);
  }

  .pos-kasir .pc-varian-modal,
  .pos-kasir .pc-addon-modal {
    width: 100%; max-width: 360px; background: var(--white); border-radius: var(--radius-md);
    border: 1px solid var(--line); box-shadow: 0 18px 40px -16px rgba(17, 24, 39, 0.22);
    padding: 20px 18px;
  }

  .pos-kasir .pc-varian-title,
  .pos-kasir .pc-addon-title {
    margin: 0 0 6px; font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; font-size: 17px; font-weight: 600;
  }

  .pos-kasir .pc-varian-product,
  .pos-kasir .pc-addon-product { margin: 0 0 14px; font-size: 13px; color: var(--ink-soft); }
  .pos-kasir .pc-addon-hint { margin: 0 0 12px; font-size: 12px; color: var(--ink-faint); }

  .pos-kasir .pc-varian-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }

  .pos-kasir .pc-varian-pick {
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid var(--line);
    background: var(--surface);
    font-weight: 600;
    font-size: 13px;
    line-height: 1.2;
    cursor: pointer;
    font-family: inherit;
  }

  .pos-kasir .pc-varian-ice:hover { border-color: #38bdf8; color: #0284c7; }
  .pos-kasir .pc-varian-hot:hover { border-color: #f97316; color: #ea580c; }

  .pos-kasir .pc-varian-cancel {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--line);
    border-radius: 10px;
    background: var(--white);
    color: var(--ink-soft);
    font-size: 13px;
    font-weight: 600;
    line-height: 1.2;
    cursor: pointer;
    font-family: inherit;
  }

  .pos-kasir .pc-addon-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; max-height: 240px; overflow-y: auto; }

  .pos-kasir .pc-addon-row {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--line);
    border-radius: var(--radius-sm); cursor: pointer;
  }

  .pos-kasir .pc-addon-row-text { flex: 1; display: flex; justify-content: space-between; gap: 8px; font-size: 13px; }
  .pos-kasir .pc-addon-price { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; color: var(--ink-soft); font-size: 12px; }
  .pos-kasir .pc-addon-extra { font-size: 12px; color: var(--ink-faint); margin: 0 0 12px; }

  .pos-kasir .pc-addon-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-top: 4px;
  }

  .pos-kasir .pc-addon-cancel,
  .pos-kasir .pc-addon-confirm {
    flex: 1;
    width: auto;
    min-height: 0;
    padding: 9px 12px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.2;
    cursor: pointer;
    font-family: inherit;
  }

  .pos-kasir .pc-addon-cancel {
    border: 1px solid var(--line);
    background: var(--white);
    color: var(--ink-soft);
  }

  .pos-kasir .pc-addon-confirm {
    border: none;
    background: var(--primary);
    color: var(--white);
  }

  @media (min-width: 981px) and (max-width: 1366px) {
    .pos-kasir .menu-grid {
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 14px;
    }

    .pos-kasir .item-card {
      padding: 12px;
      gap: 10px;
      border-radius: 18px;
    }

    .pos-kasir .item-icon {
      aspect-ratio: 1 / 1;
      border-radius: 12px;
    }

    .pos-kasir .item-emoji {
      font-size: 32px;
    }

    .pos-kasir .item-name {
      font-size: 14px;
      font-weight: 700;
      line-height: 1.35;
      min-height: 2.7em;
      color: #0F172A;
    }

    .pos-kasir .item-meta {
      flex-direction: row;
      align-items: center;
    }
  }

  @media (max-width: 980px) {
    .pos-kasir {
      height: 100svh;
      max-height: 100svh;
      overflow: hidden;
    }

    .pos-kasir .sr-app {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 0;
      height: 100%;
      overflow: hidden;
    }

    .pos-kasir .menu-side {
      flex: 1 1 56%;
      min-height: 0;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      padding: 14px 16px 10px;
      border-right: none;
    }

    .pos-kasir .controls {
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
      margin-bottom: 12px;
    }

    .pos-kasir .search-wrap {
      min-width: 0;
      width: 100%;
    }

    .pos-kasir .search-wrap input {
      padding: 12px 16px 12px 42px;
      font-size: 15px;
    }

    .pos-kasir .filter-pills {
      display: flex;
      flex-wrap: nowrap;
      gap: 8px;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      padding-bottom: 2px;
    }

    .pos-kasir .filter-pills::-webkit-scrollbar {
      display: none;
    }

    .pos-kasir .pill {
      flex-shrink: 0;
      padding: 9px 16px;
      font-size: 12px;
    }

    .pos-kasir .menu-grid {
      flex: 1;
      min-height: 0;
      max-height: none;
      overflow-y: auto;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      padding-bottom: 8px;
      -webkit-overflow-scrolling: touch;
    }

    .pos-kasir .item-card {
      padding: 10px;
      border-radius: 16px;
      gap: 8px;
    }

    .pos-kasir .item-icon {
      aspect-ratio: 1 / 1;
      border-radius: 12px;
    }

    .pos-kasir .item-emoji {
      font-size: 30px;
    }

    .pos-kasir .item-name {
      font-size: 14px;
      font-weight: 700;
      min-height: auto;
      -webkit-line-clamp: 2;
    }

    .pos-kasir .item-price {
      font-size: 13px;
    }

    .pos-kasir .order-side {
      flex: 0 1 44%;
      min-height: 180px;
      max-height: 44%;
      height: auto;
      position: relative;
      top: auto;
      border-left: none;
      border-top: 1px solid var(--line);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      padding-bottom: 78px;
      box-shadow: 0 -8px 24px -16px rgba(17, 24, 39, 0.12);
    }

    .pos-kasir .order-header {
      padding: 12px 16px 8px;
    }

    .pos-kasir .order-title {
      font-size: 16px;
    }

    .pos-kasir .order-count {
      font-size: 11px;
    }

    .pos-kasir .mode-toggle button {
      font-size: 11px;
      padding: 6px 10px;
    }

    .pos-kasir .order-body {
      flex: 1;
      min-height: 0;
      margin: 0 12px;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }

    .pos-kasir .order-list {
      gap: 8px;
    }

    .pos-kasir .cart-item {
      padding: 10px 12px;
      border-radius: 12px;
    }

    .pos-kasir .cart-item-name {
      font-size: 13px;
    }

    .pos-kasir .order-empty {
      min-height: 100px;
      padding: 20px 16px;
      border-radius: 14px;
    }

    .pos-kasir .order-empty-icon {
      width: 48px;
      height: 48px;
      margin-bottom: 10px;
    }

    .pos-kasir .order-footer {
      padding: 10px 12px 12px;
      background: transparent;
    }

    .pos-kasir .order-checkout {
      padding: 12px 14px;
      border-radius: 14px;
    }

    .pos-kasir .summary-row {
      font-size: 12px;
      margin-bottom: 6px;
    }

    .pos-kasir .summary-row.total .value {
      font-size: 16px;
    }

    .pos-kasir .cashier-field {
      margin: 12px 0 10px;
      padding-top: 12px;
    }

    .pos-kasir .cashier-field input {
      padding: 10px 12px;
      font-size: 13px;
    }

    .pos-kasir .pay-btn {
      padding: 12px 14px;
      font-size: 13px;
      border-radius: 10px;
    }

    .pos-kasir .bottom-nav {
      width: 100%;
      left: 0;
      bottom: 10px;
      padding: 0 10px;
      box-sizing: border-box;
    }

    .pos-kasir .nav-dock {
      gap: 10px;
      padding: 8px 14px;
      width: 100%;
      max-width: 100%;
      justify-content: space-between;
      border-radius: 20px;
    }

    .pos-kasir .nav-item,
    .pos-kasir .nav-item-form button {
      font-size: 9px;
      padding: 2px 0;
      min-width: 0;
    }

    .pos-kasir .nav-item svg,
    .pos-kasir .nav-item-form button svg {
      width: 19px;
      height: 19px;
    }

    .pos-kasir .nav-item.center,
    .pos-kasir button.nav-item.center {
      width: 44px;
      height: 44px;
      margin-top: -4px;
    }
  }

  @media (max-width: 380px) {
    .pos-kasir .menu-side {
      flex-basis: 54%;
    }

    .pos-kasir .order-side {
      flex-basis: 46%;
      max-height: 46%;
    }

    .pos-kasir .nav-dock {
      gap: 6px;
      padding: 8px 10px;
    }

    .pos-kasir .nav-item span,
    .pos-kasir .nav-item-form button span {
      display: none;
    }

    .pos-kasir .nav-item.center span,
    .pos-kasir button.nav-item.center span {
      display: none;
    }
  }

  .pos-kasir.ch-page .ch-content {
    padding-bottom: 110px;
  }

  .pos-kasir.ch-page .bottom-nav {
    width: 100%;
  }
</style>
