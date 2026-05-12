{{-- Gaya admin (Vuexy-inspired, prefix vx-*) --}}
<style>
    :root {
        --vx-primary: #2563eb;
        --vx-primary-hover: #1d4ed8;
        --vx-primary-soft: #eff6ff;
        --vx-primary-tint: #dbeafe;
        --vx-primary-text: #1e40af;
        --vx-bg: #f7f8fc;
        --vx-surface: #ffffff;
        --vx-text: #1e293b;
        --vx-text-soft: #64748b;
        --vx-text-mute: #94a3b8;
        --vx-border: #e2e8f0;
        --vx-border-soft: #eef1f6;
        --vx-radius-sm: 10px;
        --vx-radius: 14px;
        --vx-radius-lg: 18px;
        --vx-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 1px rgba(15, 23, 42, 0.03);
        --vx-shadow: 0 4px 18px -8px rgba(15, 23, 42, 0.12), 0 2px 6px -2px rgba(15, 23, 42, 0.04);
        --vx-shadow-lg: 0 24px 56px -28px rgba(15, 23, 42, 0.2), 0 6px 18px -8px rgba(15, 23, 42, 0.08);
        --vx-success: #16a34a;
        --vx-success-soft: #dcfce7;
        --vx-warning: #d97706;
        --vx-warning-soft: #fef3c7;
        --vx-danger: #dc2626;
        --vx-danger-soft: #fee2e2;
        --vx-info: #0284c7;
        --vx-info-soft: #e0f2fe;
        --vx-violet: #7c3aed;
        --vx-violet-soft: #ede9fe;
    }

    .vx-app {
        background: var(--vx-bg);
        color: var(--vx-text);
        font-feature-settings: "cv11", "ss03";
    }

    /* ---------- Sidebar ---------- */
    .vx-sidebar {
        width: 260px;
        background: var(--vx-surface);
        border-right: 1px solid var(--vx-border-soft);
    }
    .vx-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 18px 18px 16px;
    }
    .vx-sidebar-brand img {
        height: 36px;
        width: auto;
        max-width: 170px;
        object-fit: contain;
    }
    .vx-sidebar-section {
        padding: 16px 16px 8px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--vx-text-mute);
    }
    .vx-sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 4px 12px 16px;
    }
    .vx-sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: var(--vx-text-soft);
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .vx-sidebar-link svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.8;
        flex-shrink: 0;
    }
    .vx-sidebar-link:hover {
        background: var(--vx-primary-soft);
        color: var(--vx-primary-text);
    }
    .vx-sidebar-link.is-active {
        background: linear-gradient(118deg, var(--vx-primary) 0%, #3b82f6 100%);
        color: #fff;
        box-shadow: 0 6px 14px -6px rgba(37, 99, 235, 0.55);
    }
    .vx-sidebar-link.is-active svg { stroke: #fff; }

    /* ---------- Topbar ---------- */
    .vx-topbar {
        position: sticky;
        top: 0;
        z-index: 30;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--vx-border-soft);
    }
    .vx-topbar-inner {
        height: 64px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 clamp(14px, 3vw, 28px);
    }
    .vx-topbar-burger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid var(--vx-border);
        background: #fff;
        color: var(--vx-text-soft);
        cursor: pointer;
    }
    .vx-topbar-burger:hover { color: var(--vx-primary); border-color: var(--vx-primary-tint); }
    .vx-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--vx-text);
        margin: 0;
    }
    .vx-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--vx-text-mute);
    }
    .vx-breadcrumbs a {
        color: var(--vx-text-soft);
        text-decoration: none;
    }
    .vx-breadcrumbs a:hover { color: var(--vx-primary); }
    .vx-breadcrumbs .vx-sep { opacity: 0.55; }
    .vx-breadcrumbs .vx-current { color: var(--vx-text); font-weight: 500; }
    .vx-topbar-user {
        position: relative;
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .vx-topbar-user-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 4px 6px 4px 10px;
        border-radius: 999px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .vx-topbar-user-btn:hover {
        background: var(--vx-primary-soft);
        border-color: var(--vx-primary-tint);
    }
    .vx-topbar-user .vx-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--vx-primary);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
    }
    .vx-topbar-user .vx-meta {
        line-height: 1.1;
        text-align: right;
    }
    .vx-topbar-user .vx-meta strong { font-size: 13px; color: var(--vx-text); display: block; font-weight: 600; }
    .vx-topbar-user .vx-meta small { font-size: 11px; color: var(--vx-text-mute); }

    .vx-user-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 220px;
        background: var(--vx-surface);
        border: 1px solid var(--vx-border-soft);
        border-radius: var(--vx-radius);
        box-shadow: var(--vx-shadow-lg);
        padding: 6px;
        z-index: 40;
    }
    .vx-user-menu-head {
        padding: 10px 12px 8px;
        border-bottom: 1px solid var(--vx-border-soft);
        margin-bottom: 6px;
    }
    .vx-user-menu-head strong { display: block; font-size: 13px; font-weight: 600; color: var(--vx-text); }
    .vx-user-menu-head small { display: block; font-size: 11px; color: var(--vx-text-mute); margin-top: 2px; }
    .vx-user-menu-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 10px;
        border: 0;
        background: transparent;
        font-size: 13px;
        font-weight: 500;
        color: var(--vx-text-soft);
        text-align: left;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .vx-user-menu-item svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.8;
        flex-shrink: 0;
    }
    .vx-user-menu-item:hover {
        background: var(--vx-primary-soft);
        color: var(--vx-primary-text);
    }
    .vx-user-menu-item.is-danger { color: #b91c1c; }
    .vx-user-menu-item.is-danger:hover { background: #fef2f2; color: #991b1b; }
    .vx-user-menu-sep {
        height: 1px;
        background: var(--vx-border-soft);
        margin: 6px 4px;
    }

    /* ---------- Page header ---------- */
    .vx-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .vx-page-head h1 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 600;
        color: var(--vx-text);
    }
    .vx-page-head p {
        margin: 0;
        color: var(--vx-text-soft);
        font-size: 13px;
    }

    /* ---------- Cards ---------- */
    .vx-card {
        background: var(--vx-surface);
        border: 1px solid var(--vx-border-soft);
        border-radius: var(--vx-radius);
        box-shadow: var(--vx-shadow-sm);
    }
    .vx-card-pad { padding: 22px; }
    .vx-card-pad-sm { padding: 16px 18px; }
    .vx-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--vx-border-soft);
    }
    .vx-card-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--vx-text);
    }
    .vx-card-head p { margin: 0; font-size: 12px; color: var(--vx-text-mute); }

    /* ---------- Stat card ---------- */
    .vx-stat {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 20px;
        background: var(--vx-surface);
        border: 1px solid var(--vx-border-soft);
        border-radius: var(--vx-radius);
        box-shadow: var(--vx-shadow-sm);
    }
    .vx-stat-icon {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .vx-stat-icon svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 1.8; }
    .vx-stat-icon.vx-bg-primary { background: var(--vx-primary-soft); color: var(--vx-primary); }
    .vx-stat-icon.vx-bg-success { background: var(--vx-success-soft); color: var(--vx-success); }
    .vx-stat-icon.vx-bg-info { background: var(--vx-info-soft); color: var(--vx-info); }
    .vx-stat-icon.vx-bg-warning { background: var(--vx-warning-soft); color: var(--vx-warning); }
    .vx-stat-icon.vx-bg-violet { background: var(--vx-violet-soft); color: var(--vx-violet); }
    .vx-stat-icon.vx-bg-danger { background: var(--vx-danger-soft); color: var(--vx-danger); }
    .vx-stat-label {
        font-size: 12px;
        color: var(--vx-text-soft);
        font-weight: 500;
    }
    .vx-stat-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--vx-text);
        margin-top: 4px;
        letter-spacing: -0.02em;
    }

    /* ---------- Buttons ---------- */
    .vx-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.18s ease, transform 0.12s ease;
        border: 1px solid transparent;
        line-height: 1.2;
        text-decoration: none;
    }
    .vx-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2); }
    .vx-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; }

    .vx-btn-primary {
        background: var(--vx-primary);
        color: #fff;
        box-shadow: 0 6px 14px -6px rgba(37, 99, 235, 0.55);
    }
    .vx-btn-primary:hover { background: var(--vx-primary-hover); transform: translateY(-1px); }

    .vx-btn-ghost {
        background: #fff;
        border-color: var(--vx-border);
        color: var(--vx-text-soft);
    }
    .vx-btn-ghost:hover { background: var(--vx-primary-soft); color: var(--vx-primary-text); border-color: var(--vx-primary-tint); }

    .vx-btn-soft {
        background: var(--vx-primary-soft);
        color: var(--vx-primary-text);
        border-color: var(--vx-primary-tint);
    }
    .vx-btn-soft:hover { background: var(--vx-primary); color: #fff; border-color: var(--vx-primary); }

    .vx-btn-sm { padding: 7px 12px; font-size: 12px; }
    .vx-btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        background: transparent;
        border: 1px solid transparent;
        color: var(--vx-text-soft);
    }
    .vx-btn-icon:hover { background: var(--vx-primary-soft); color: var(--vx-primary); }
    .vx-btn-icon.is-danger:hover { background: var(--vx-danger-soft); color: var(--vx-danger); }

    /* ---------- Form ---------- */
    .vx-field { display: block; }
    .vx-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--vx-text-soft);
        margin-bottom: 6px;
    }
    .vx-input,
    .vx-select {
        width: 100%;
        font-family: inherit;
        font-size: 14px;
        color: var(--vx-text);
        background: var(--vx-surface);
        border: 1px solid var(--vx-border);
        border-radius: 10px;
        padding: 11px 13px;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .vx-input::placeholder { color: var(--vx-text-mute); }
    .vx-input:hover,
    .vx-select:hover { border-color: #cbd5e1; }
    .vx-input:focus,
    .vx-select:focus {
        border-color: var(--vx-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
    }
    .vx-input[type="file"] {
        padding: 8px 10px;
    }
    .vx-input[type="file"]::file-selector-button {
        margin-right: 10px;
        border: none;
        background: var(--vx-primary-soft);
        color: var(--vx-primary-text);
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }
    .vx-input[type="file"]::file-selector-button:hover {
        background: var(--vx-primary);
        color: #fff;
    }
    .vx-help {
        margin-top: 6px;
        font-size: 12px;
        color: var(--vx-text-mute);
    }
    .vx-error {
        margin-top: 6px;
        font-size: 12px;
        color: var(--vx-danger);
    }
    .vx-error ul { list-style: none; padding: 0; margin: 0; }

    /* ---------- Table ---------- */
    .vx-table-wrap {
        background: var(--vx-surface);
        border: 1px solid var(--vx-border-soft);
        border-radius: var(--vx-radius);
        box-shadow: var(--vx-shadow-sm);
        overflow: hidden;
    }
    .vx-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }
    .vx-table thead th {
        background: #f8fafc;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--vx-text-mute);
        padding: 14px 20px;
        border-bottom: 1px solid var(--vx-border-soft);
        white-space: nowrap;
    }
    .vx-table thead th.vx-text-end { text-align: right; }
    .vx-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--vx-border-soft);
        color: var(--vx-text);
        vertical-align: middle;
    }
    .vx-table tbody td.vx-text-end { text-align: right; }
    .vx-table tbody tr:last-child td { border-bottom: 0; }
    .vx-table tbody tr:hover td { background: #f8fafc; }
    .vx-table-foot {
        padding: 14px 20px;
        border-top: 1px solid var(--vx-border-soft);
        background: #fbfcfe;
    }
    .vx-table-actions { display: inline-flex; align-items: center; gap: 4px; }

    /* ---------- Badge / pill ---------- */
    .vx-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .vx-badge-primary { background: var(--vx-primary-soft); color: var(--vx-primary-text); }
    .vx-badge-success { background: var(--vx-success-soft); color: var(--vx-success); }
    .vx-badge-warning { background: var(--vx-warning-soft); color: var(--vx-warning); }
    .vx-badge-danger { background: var(--vx-danger-soft); color: var(--vx-danger); }
    .vx-badge-violet { background: var(--vx-violet-soft); color: var(--vx-violet); }
    .vx-badge-slate { background: #f1f5f9; color: var(--vx-text-soft); }

    /* ---------- Avatar/Thumb ---------- */
    .vx-thumb {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
        background: var(--vx-primary-soft);
    }
    .vx-thumb-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--vx-primary-soft);
        color: var(--vx-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    /* ---------- Drawer / responsive ---------- */
    @media (max-width: 1023px) {
        .vx-sidebar {
            position: fixed;
            inset-y: 0;
            top: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            z-index: 50;
            transition: transform 0.2s ease;
            box-shadow: var(--vx-shadow-lg);
        }
        .vx-sidebar.is-open { transform: translateX(0); }
    }
</style>
