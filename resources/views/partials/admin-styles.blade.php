{{-- Admin shell — Modifikasi Ori layout, navy + white (Starrich) --}}
<style>
    :root {
        --vx-navy: #0c1929;
        --vx-navy-light: #132337;
        --vx-primary: #1e40af;
        --vx-primary-hover: #1e3a8a;
        --vx-primary-bright: #2563eb;
        --vx-primary-soft: #eff6ff;
        --vx-primary-tint: #dbeafe;
        --vx-primary-text: #1e40af;
        --vx-bg: #f4f5f7;
        --vx-surface: #ffffff;
        --vx-text: #0f172a;
        --vx-text-soft: #64748b;
        --vx-text-mute: #94a3b8;
        --vx-border: #e2e8f0;
        --vx-border-soft: #eef1f6;
        --vx-radius-sm: 10px;
        --vx-radius: 16px;
        --vx-radius-lg: 20px;
        --vx-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
        --vx-shadow: 0 8px 24px -12px rgba(15, 23, 42, 0.14);
        --vx-shadow-lg: 0 24px 48px -24px rgba(15, 23, 42, 0.28);
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
        --vx-sidebar-w: 270px;
    }

    .vx-app {
        background: var(--vx-bg);
        color: var(--vx-text);
        font-feature-settings: "cv11", "ss03";
    }

    /* ---------- Sidebar shell ---------- */
    .vx-sidebar-shell {
        width: var(--vx-sidebar-w);
        flex-shrink: 0;
        overflow: hidden;
        transition: width 0.3s ease;
    }
    .vx-sidebar-shell.is-collapsed {
        width: 0;
    }

    .vx-sidebar {
        width: var(--vx-sidebar-w);
        height: 100%;
        background: var(--vx-navy);
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        display: flex;
        flex-direction: column;
    }

    .vx-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        flex-shrink: 0;
    }
    .vx-sidebar-brand img {
        height: 36px;
        width: 36px;
        object-fit: contain;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px;
    }
    .vx-sidebar-brand-title {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: #fff;
        line-height: 1.2;
    }
    .vx-sidebar-brand-sub {
        margin: 2px 0 0;
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.45);
    }

    .vx-sidebar-section {
        padding: 18px 20px 8px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.38);
    }
    .vx-sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 4px 14px 16px;
    }
    .vx-sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.65);
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
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
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }
    .vx-sidebar-link.is-active {
        background: var(--vx-primary);
        color: #fff;
        box-shadow: 0 8px 20px -8px rgba(37, 99, 235, 0.65);
    }

    /* ---------- Topbar ---------- */
    .vx-topbar {
        position: sticky;
        top: 0;
        z-index: 30;
        background: #fff;
        border-bottom: 1px solid var(--vx-border);
        flex-shrink: 0;
    }
    .vx-topbar-inner {
        height: 64px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 0 20px;
    }
    .vx-topbar-burger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid var(--vx-border);
        background: #fff;
        color: var(--vx-text-soft);
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .vx-topbar-burger:hover {
        background: var(--vx-primary-soft);
        color: var(--vx-primary);
        border-color: var(--vx-primary-tint);
    }
    .vx-topbar-context {
        font-size: 13px;
        font-weight: 500;
        color: var(--vx-text-mute);
    }
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
        padding: 6px 8px 6px 12px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .vx-topbar-user-btn:hover {
        background: #f8fafc;
        border-color: var(--vx-border);
    }
    .vx-topbar-user .vx-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--vx-primary);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
    }
    .vx-topbar-user .vx-meta { line-height: 1.15; text-align: right; }
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
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .vx-page-head h1 {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--vx-text);
    }
    .vx-page-head p {
        margin: 0;
        color: var(--vx-text-soft);
        font-size: 13px;
    }

    /* ---------- Dashboard filter strip ---------- */
    .vx-filter-strip {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .vx-filter-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--vx-text-soft);
    }
    .vx-filter-select,
    .vx-filter-date {
        appearance: none;
        background: var(--vx-surface);
        border: 1px solid var(--vx-border);
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--vx-text);
        font-family: inherit;
        cursor: pointer;
        box-shadow: var(--vx-shadow-sm);
    }
    .vx-filter-select {
        padding-right: 30px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
    }
    .vx-filter-date-year {
        width: 88px;
    }
    .vx-filter-apply {
        background: var(--vx-primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s, opacity 0.15s;
    }
    .vx-filter-apply:hover {
        background: var(--vx-primary-hover);
    }

    /* ---------- Hero strip (dashboard) ---------- */
    .vx-hero-strip {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 24px 28px;
        border-radius: var(--vx-radius-lg);
        background:
            radial-gradient(ellipse 80% 120% at 100% 0%, rgba(37, 99, 235, 0.28) 0%, transparent 55%),
            linear-gradient(135deg, var(--vx-navy) 0%, #132f52 55%, #0c1929 100%);
        color: #fff;
        box-shadow: var(--vx-shadow);
        margin-bottom: 20px;
    }
    @media (min-width: 768px) {
        .vx-hero-strip {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .vx-hero-strip .vx-hero-stats {
            flex: 1 1 0;
            min-width: 0;
        }
        .vx-hero-strip--pl {
            gap: 40px;
        }
        .vx-hero-strip--pl > div:first-child {
            flex-shrink: 0;
        }
        .vx-hero-strip--pl .vx-hero-stats {
            margin-left: 8px;
            padding-left: 40px;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
        }
    }
    .vx-hero-eyebrow {
        margin: 0 0 6px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.55);
    }
    .vx-hero-value {
        margin: 0;
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.1;
    }
    .vx-hero-sub {
        margin: 8px 0 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.6);
    }
    .vx-hero-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 20px;
    }
    @media (min-width: 640px) {
        .vx-hero-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .vx-hero-stats.is-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }
    .vx-hero-stats.is-cols-5 .vx-hero-stat strong {
        font-size: clamp(14px, 1.35vw, 18px);
    }
    .vx-hero-stat label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.45);
        margin-bottom: 4px;
    }
    .vx-hero-stat strong {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
    }
    .vx-hero-stat strong.is-green { color: #86efac; }
    .vx-hero-stat strong.is-blue { color: #93c5fd; }
    .vx-hero-stat strong.is-amber { color: #fcd34d; }
    .vx-hero-stat strong.is-negative { color: #fca5a5; }

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
        font-size: 11px;
        color: var(--vx-text-mute);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .vx-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--vx-text);
        margin-top: 4px;
        letter-spacing: -0.02em;
    }
    button.vx-stat.is-clickable {
        width: 100%;
        text-align: left;
        font: inherit;
        color: inherit;
        cursor: pointer;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    button.vx-stat.is-clickable:hover {
        border-color: #fcd34d;
        box-shadow: var(--vx-shadow);
    }

    .vx-modal-panel.is-wide { max-width: 640px; }
    .vx-open-bill-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .vx-open-bill-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px 16px;
        padding: 14px 16px;
        border: 1px solid var(--vx-border-soft);
        border-radius: 12px;
        background: #fafbfc;
    }
    .vx-open-bill-item-id {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 700;
        color: var(--vx-text);
    }
    .vx-open-bill-item-meta {
        margin: 0;
        font-size: 12px;
        color: var(--vx-text-soft);
        line-height: 1.5;
    }
    .vx-open-bill-item-total {
        align-self: center;
        font-size: 15px;
        font-weight: 700;
        color: #b45309;
        white-space: nowrap;
    }
    .vx-open-bill-empty {
        padding: 28px 16px;
        text-align: center;
        font-size: 13px;
        color: var(--vx-text-mute);
        border: 1px dashed var(--vx-border);
        border-radius: 12px;
    }

    /* ---------- Buttons ---------- */
    .vx-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.18s ease, transform 0.12s ease;
        border: 1px solid transparent;
        line-height: 1.2;
        text-decoration: none;
    }
    .vx-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2); }
    .vx-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; }

    .vx-btn-primary {
        background: var(--vx-primary);
        color: #fff;
        box-shadow: 0 8px 20px -8px rgba(30, 64, 175, 0.55);
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
    .vx-btn-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--vx-navy);
        background: #fff;
        border: 1.5px solid var(--vx-navy);
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .vx-btn-detail:hover {
        background: var(--vx-navy);
        color: #fff;
        box-shadow: 0 4px 12px rgba(12, 25, 41, 0.18);
    }

    /* ---------- Modal ---------- */
    .vx-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(12, 25, 41, 0.45);
        backdrop-filter: blur(4px);
    }
    .vx-modal-panel {
        width: 100%;
        max-width: 480px;
        max-height: min(88vh, 640px);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: var(--vx-surface);
        border: 1px solid var(--vx-border-soft);
        border-radius: var(--vx-radius);
        box-shadow: var(--vx-shadow-lg);
    }
    .vx-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        background: linear-gradient(135deg, var(--vx-navy) 0%, #132f52 100%);
        color: #fff;
    }
    .vx-modal-head h3 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .vx-modal-head p {
        margin: 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.65);
    }
    .vx-modal-close {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        cursor: pointer;
        transition: background 0.15s;
    }
    .vx-modal-close:hover { background: rgba(255, 255, 255, 0.16); }
    .vx-modal-close svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
    .vx-modal-body {
        padding: 18px 20px;
        overflow-y: auto;
    }
    .vx-modal-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--vx-border-soft);
    }
    .vx-modal-meta label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--vx-text-mute);
        margin-bottom: 2px;
    }
    .vx-modal-meta span {
        font-size: 13px;
        font-weight: 500;
        color: var(--vx-text);
    }
    .vx-modal-items {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .vx-modal-item {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
        color: var(--vx-text-soft);
    }
    .vx-modal-item strong { color: var(--vx-text); font-weight: 600; }
    .vx-modal-foot {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--vx-border-soft);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .vx-modal-foot-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
    }
    .vx-modal-foot-row.is-total {
        font-size: 15px;
        font-weight: 700;
        color: var(--vx-text);
    }

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
        border-radius: 12px;
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
        box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.16);
    }
    .vx-input[type="file"] { padding: 8px 10px; }
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
    .vx-help { margin-top: 6px; font-size: 12px; color: var(--vx-text-mute); }
    .vx-error { margin-top: 6px; font-size: 12px; color: var(--vx-danger); }
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
        background: #f7f8fa;
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
    .vx-table tbody tr:hover td { background: #fafbfc; }
    .vx-table-filter-row th {
        background: #fbfbfd;
        padding: 8px 12px;
        border-bottom: 1px solid var(--vx-border-soft);
        vertical-align: middle;
    }
    .vx-table-col-filter {
        width: 100%;
        min-width: 72px;
        appearance: none;
        background: var(--vx-surface);
        border: 1px solid var(--vx-border);
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 500;
        color: var(--vx-text);
        font-family: inherit;
    }
    .vx-table-col-filter:focus {
        outline: none;
        border-color: var(--vx-primary-bright);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    select.vx-table-col-filter {
        padding-right: 28px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
    }
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
        font-weight: 700;
    }

    /* ---------- Section title ---------- */
    .vx-section-title {
        margin: 0 0 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--vx-text-mute);
    }

    /* ---------- Profit & loss statement ---------- */
    .vx-pl-wrap { padding: 0; }
    .vx-pl-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }
    .vx-pl-table td,
    .vx-pl-table th {
        padding: 13px 20px;
        border-bottom: 1px solid var(--vx-border-soft);
        vertical-align: middle;
    }
    .vx-pl-table tr:last-child td { border-bottom: 0; }
    .vx-pl-section th {
        background: #f7f8fa;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--vx-text-mute);
    }
    .vx-pl-section.is-spaced th { padding-top: 22px; }
    .vx-pl-row td:first-child { color: var(--vx-text-soft); }
    .vx-pl-row td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .vx-pl-subtotal td {
        font-weight: 600;
        color: var(--vx-text);
        background: #fafbfc;
    }
    .vx-pl-subtotal td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .vx-pl-total td {
        font-weight: 700;
        color: var(--vx-text);
        border-top: 1px solid var(--vx-border);
    }
    .vx-pl-total td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .vx-pl-net td {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
        border-top: 2px solid var(--vx-primary-tint);
        border-bottom: 0;
    }
    .vx-pl-net td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .vx-pl-net.is-negative td {
        background: linear-gradient(135deg, #fef2f2 0%, #fff7f7 100%);
        border-top-color: #fecaca;
    }
    .vx-pl-share td {
        font-size: 14px;
        font-weight: 700;
        color: var(--vx-text);
        background: #f8fafc;
        border-bottom: 0;
    }
    .vx-pl-share td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .vx-pl-share.is-negative td { background: #fff7f7; }
    .vx-pl-note {
        margin-left: 8px;
        font-size: 11px;
        font-weight: 500;
        color: var(--vx-text-mute);
    }
    .vx-hero-value.is-negative { color: #fca5a5; }

    @media print {
        .vx-sidebar-shell,
        .vx-topbar,
        .vx-page-head,
        .vx-filter-strip,
        .vx-breadcrumbs,
        .vx-card-head .vx-btn,
        .vx-hero-strip { display: none !important; }
        .vx-app { background: #fff !important; }
        body, .vx-app, main { overflow: visible !important; height: auto !important; }
        main { padding: 0 !important; }
        .vx-table-wrap { box-shadow: none !important; border: none !important; }
        .vx-pl-net td,
        .vx-pl-share td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    /* ---------- Responsive sidebar ---------- */
    @media (max-width: 1023px) {
        .vx-sidebar-shell {
            position: fixed;
            inset-y: 0;
            left: 0;
            z-index: 50;
            width: var(--vx-sidebar-w);
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        .vx-sidebar-shell.is-open {
            transform: translateX(0);
        }
        .vx-sidebar-shell.is-collapsed {
            width: var(--vx-sidebar-w);
            transform: translateX(-100%);
        }
        .vx-sidebar-shell.is-open:not(.is-collapsed) {
            transform: translateX(0);
        }
    }
</style>
