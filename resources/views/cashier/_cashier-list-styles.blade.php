<style>
    .ch-page { display: flex; flex-direction: column; height: 100dvh; }
    .ch-content {
        flex: 1;
        min-height: 0;
        overflow: auto;
        padding: 20px;
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
    }
    .ch-card {
        background: #fff;
        border: 1px solid var(--caramel-light);
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .ch-card + .ch-card { margin-top: 14px; }
    .ch-card-pad-sm { padding: 14px 16px; }
    .ch-grid-filter { display: grid; grid-template-columns: 1fr 1.2fr auto; gap: 10px; align-items: end; }
    .ch-grid-filter--history { grid-template-columns: 1fr 1fr 1.2fr 1fr auto; }
    .ch-grid-filter--open-bills { grid-template-columns: 1fr auto; }
    .ch-label { display: block; font-size: 11px; font-weight: 600; color: var(--brown-mid); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
    .ch-input, .ch-select {
        width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid var(--caramel-light);
        background: #fff; font-size: 13px; color: var(--brown-dark); transition: border-color 0.15s ease;
    }
    .ch-input:focus, .ch-select:focus { outline: none; border-color: var(--caramel); box-shadow: 0 0 0 3px rgba(59,130,246,0.18); }
    .ch-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer;
        text-decoration: none; border: 1px solid transparent; transition: background 0.15s ease, color 0.15s ease;
        font-family: inherit;
    }
    .ch-btn-primary { background: var(--caramel); color: #fff; }
    .ch-btn-primary:hover { background: #1d4ed8; }
    .ch-btn-primary:disabled { opacity: 0.65; cursor: not-allowed; }
    .ch-btn-ghost { background: #fff; color: var(--brown-mid); border-color: var(--caramel-light); }
    .ch-btn-ghost:hover { background: var(--cream); }

    .ch-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
    .ch-stat { display: flex; align-items: center; gap: 12px; padding: 14px 16px; }
    .ch-stat-icon {
        width: 40px; height: 40px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
        background: var(--caramel-light); color: var(--caramel);
    }
    .ch-stat-icon--amber { background: #fef3c7; color: #b45309; }
    .ch-stat-icon svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 1.8; }
    .ch-stat-label { font-size: 11px; font-weight: 600; color: var(--brown-light); text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .ch-stat-value { font-size: 18px; font-weight: 700; color: var(--brown-dark); margin: 2px 0 0; }

    .ch-list { display: flex; flex-direction: column; gap: 10px; }
    .ch-row {
        display: grid; grid-template-columns: 1.6fr 1.6fr 1fr 1.2fr auto; gap: 12px;
        padding: 14px 16px; align-items: center; border-radius: 12px;
        background: #fff; border: 1px solid var(--caramel-light);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .ch-row:hover { border-color: var(--caramel); box-shadow: 0 4px 12px rgba(37,99,235,0.08); }
    .ch-row .ch-id { font-family: 'Menlo', ui-monospace, monospace; font-size: 12px; color: var(--brown-light); }
    .ch-row .ch-trx-title { font-weight: 600; color: var(--brown-dark); font-size: 14px; }
    .ch-row .ch-trx-meta { font-size: 12px; color: var(--brown-light); margin-top: 2px; }
    .ch-row .ch-total { font-weight: 700; color: var(--brown-dark); text-align: right; }
    .ch-row .ch-method { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: var(--caramel-light); color: var(--caramel); }
    .ch-row .ch-method.cash { background: #dcfce7; color: #15803d; }
    .ch-row .ch-method.transfer { background: #ede9fe; color: #6d28d9; }
    .ch-row .ch-method.qris { background: #fef3c7; color: #b45309; }
    .ch-row .ch-method.split { background: #cffafe; color: #0e7490; }
    .ch-row .ch-method.open_bill { background: #fef3c7; color: #b45309; }
    .ch-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .ch-icon-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--caramel-light);
        background: #fff; color: var(--brown-mid); cursor: pointer; text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .ch-icon-btn:hover { background: var(--caramel); color: #fff; border-color: var(--caramel); }
    .ch-icon-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; }

    .ch-empty { padding: 60px 20px; text-align: center; color: var(--brown-light); font-size: 14px; }
    .ch-pagination { margin-top: 16px; }

    @media (max-width: 768px) {
        .ch-grid-filter,
        .ch-grid-filter--history,
        .ch-grid-filter--open-bills { grid-template-columns: 1fr 1fr; }
        .ch-grid-filter > button,
        .ch-grid-filter--history > button,
        .ch-grid-filter--open-bills > button { grid-column: 1 / -1; }
        .ch-row { grid-template-columns: 1fr 1fr; }
        .ch-row .ch-total, .ch-row .ch-actions { grid-column: 1 / -1; justify-content: flex-end; text-align: right; }
    }
</style>
