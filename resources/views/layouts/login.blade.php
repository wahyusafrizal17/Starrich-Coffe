<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Masuk — {{ config('app.name', 'Starrich') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @include('partials.pwa-head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .login-shell * { box-sizing: border-box; }
            .login-shell {
                font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
                -webkit-font-smoothing: antialiased;
                min-height: 100dvh;
                display: flex;
                align-items: center;
                justify-content: center;
                background:
                    radial-gradient(ellipse 90% 80% at 0% 100%, rgba(56, 189, 248, 0.35), transparent 55%),
                    radial-gradient(ellipse 75% 70% at 100% 0%, rgba(96, 165, 250, 0.28), transparent 50%),
                    radial-gradient(ellipse 55% 45% at 50% 110%, rgba(14, 165, 233, 0.2), transparent 45%),
                    linear-gradient(155deg, #0b3a5a 0%, #1d4ed8 38%, #2563eb 62%, #38bdf8 100%);
                background-size: 100% 100%, 100% 100%, 100% 100%, 200% 200%;
                background-position: 0% 0%, 0% 0%, 0% 0%, 0% 50%;
                position: relative;
                overflow-x: hidden;
                overflow-y: auto;
                padding:
                    max(clamp(1.25rem, 4vh, 2.5rem), env(safe-area-inset-top))
                    max(clamp(1rem, 3vw, 2rem), env(safe-area-inset-right))
                    max(clamp(1.25rem, 4vh, 2.5rem), env(safe-area-inset-bottom))
                    max(clamp(1rem, 3vw, 2rem), env(safe-area-inset-left));
                animation: loginBgGradientDrift 18s ease-in-out infinite alternate;
            }

            .login-bg-stripes {
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 0;
                background: repeating-linear-gradient(
                    -38deg,
                    transparent,
                    transparent 62px,
                    rgba(255, 255, 255, 0.04) 62px,
                    rgba(255, 255, 255, 0.04) 63px
                );
                background-size: 120px 120px;
                animation: loginStripeDrift 50s linear infinite;
                opacity: 0.9;
            }

            .login-bg-aurora {
                position: absolute;
                inset: -30%;
                pointer-events: none;
                z-index: 0;
                background:
                    radial-gradient(circle at 25% 35%, rgba(125, 211, 252, 0.35), transparent 42%),
                    radial-gradient(circle at 78% 65%, rgba(59, 130, 246, 0.28), transparent 48%),
                    radial-gradient(circle at 50% 100%, rgba(14, 165, 233, 0.2), transparent 40%);
                animation: loginAurora 14s ease-in-out infinite alternate;
                will-change: transform, opacity;
            }

            .login-shell::before,
            .login-shell::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
                z-index: 0;
            }
            .login-shell::before {
                width: min(520px, 95vw);
                height: min(520px, 95vw);
                background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 68%);
                top: -22%;
                left: -12%;
                animation: loginBlobA 22s ease-in-out infinite alternate;
                will-change: transform, opacity;
            }
            .login-shell::after {
                width: min(380px, 85vw);
                height: min(380px, 85vw);
                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 65%);
                bottom: -18%;
                right: -8%;
                animation: loginBlobB 28s ease-in-out infinite alternate;
                will-change: transform, opacity;
            }

            @keyframes loginBgGradientDrift {
                0% {
                    background-position: 0% 0%, 0% 0%, 0% 0%, 0% 40%;
                }
                100% {
                    background-position: 8% 5%, -5% 8%, 3% -4%, 100% 60%;
                }
            }
            @keyframes loginStripeDrift {
                0% { transform: translate(0, 0); }
                100% { transform: translate(-48px, 28px); }
            }
            @keyframes loginAurora {
                0% {
                    opacity: 0.45;
                    transform: scale(1) translate(0, 0) rotate(0deg);
                }
                100% {
                    opacity: 0.85;
                    transform: scale(1.06) translate(2%, -1.5%) rotate(2deg);
                }
            }
            @keyframes loginBlobA {
                0% {
                    transform: translate(0, 0) scale(1);
                    opacity: 0.85;
                }
                100% {
                    transform: translate(3%, 4%) scale(1.06);
                    opacity: 1;
                }
            }
            @keyframes loginBlobB {
                0% {
                    transform: translate(0, 0) scale(1);
                    opacity: 0.75;
                }
                100% {
                    transform: translate(-4%, -3%) scale(1.08);
                    opacity: 0.95;
                }
            }
            @keyframes loginWaveMotion {
                0%, 100% {
                    transform: translateX(0) translateY(0);
                }
                50% {
                    transform: translateX(-3%) translateY(6px);
                }
            }
            @keyframes loginBubbleY {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-12px); }
            }
            @keyframes loginBubbleY2 {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-18px) scale(1.03); }
            }

            .login-bg-wave {
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 0;
                overflow: hidden;
            }
            .login-bg-wave svg {
                position: absolute;
                width: 140%;
                min-width: 900px;
                height: auto;
                bottom: -5%;
                left: -20%;
                opacity: 0.22;
                color: #93c5fd;
                animation: loginWaveMotion 10s ease-in-out infinite;
                will-change: transform;
            }
            .login-bg-bubbles {
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 0;
            }
            .login-bg-bubbles span {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.12);
            }
            .login-bg-bubbles span:nth-child(1) {
                width: 120px;
                height: 120px;
                top: 12%;
                right: 8%;
                animation: loginBubbleY2 7s ease-in-out infinite;
                animation-delay: -1s;
            }
            .login-bg-bubbles span:nth-child(2) {
                width: 64px;
                height: 64px;
                bottom: 24%;
                left: 6%;
                background: rgba(255, 255, 255, 0.06);
                animation: loginBubbleY 9s ease-in-out infinite;
                animation-delay: -2.5s;
            }
            .login-bg-bubbles span:nth-child(3) {
                width: 40px;
                height: 40px;
                top: 38%;
                left: 14%;
                animation: loginBubbleY 6s ease-in-out infinite;
                animation-delay: -0.5s;
            }

            .login-card {
                width: min(100%, 980px);
                min-height: auto;
                flex-shrink: 0;
                margin-block: auto;
                background: #fff;
                border-radius: 24px;
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                align-items: stretch;
                overflow: hidden;
                position: relative;
                z-index: 1;
                box-shadow:
                    0 1px 0 rgba(255, 255, 255, 0.95) inset,
                    0 30px 80px -24px rgba(15, 23, 42, 0.45),
                    0 12px 32px -14px rgba(15, 23, 42, 0.22);
            }

            .login-col-form {
                padding: 1.25rem clamp(1.25rem, 3vw, 1.75rem) 1rem;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                background: #fff;
            }

            .login-col-visual {
                background: #eef2ff;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: clamp(1.25rem, 3vw, 1.75rem);
                position: relative;
            }
            .login-col-visual::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse 80% 60% at 50% 80%, rgba(59, 130, 246, 0.09), transparent 65%);
                pointer-events: none;
            }

            .login-hero {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 560px;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-hero .login-hero-img {
                width: 100%;
                height: auto;
                max-height: min(520px, 62vh);
                max-width: min(100%, 560px);
                display: block;
                object-fit: contain;
            }

            .login-logo-row {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                margin-bottom: 1.5rem;
            }
            .login-logo-row .login-logo-img {
                height: 120px;
                width: auto;
                max-width: min(480px, 92vw);
                object-fit: contain;
                flex-shrink: 0;
            }

            .login-alert {
                margin-bottom: 1.25rem;
                padding: 12px 14px;
                border-radius: 12px;
                font-size: 13px;
                font-weight: 500;
                background: #ecfdf5;
                color: #047857;
                border: 1px solid #a7f3d0;
            }

            .login-field {
                position: relative;
                margin-bottom: 16px;
            }
            .login-field input {
                width: 100%;
                padding: 15px 17px 15px 48px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 16px;
                font-family: inherit;
                color: #111827;
                background: #f9fafb;
                outline: none;
                transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            }
            .login-field input::placeholder { color: #9ca3af; }
            .login-field input:hover { border-color: #d1d5db; }
            .login-field input:focus {
                border-color: #2563eb;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            }

            .login-field--password input {
                padding-right: 48px;
            }

            .login-fi-l {
                position: absolute;
                left: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                display: flex;
                pointer-events: none;
                transition: color 0.2s;
            }
            .login-field:focus-within .login-fi-l { color: #2563eb; }

            .login-fi-r {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border: none;
                border-radius: 10px;
                background: transparent;
                padding: 0;
                transition: color 0.2s, background 0.2s;
            }
            .login-fi-r:hover {
                color: #2563eb;
                background: rgba(37, 99, 235, 0.08);
            }

            .login-field svg.field-svg {
                width: 18px;
                height: 18px;
                stroke: currentColor;
                fill: none;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .login-remember {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 22px;
                margin-top: 4px;
            }
            .login-remember input[type="checkbox"] {
                accent-color: #2563eb;
                width: 17px;
                height: 17px;
                cursor: pointer;
                border-radius: 4px;
            }
            .login-remember label {
                font-size: 14px;
                color: #4b5563;
                cursor: pointer;
                font-weight: 500;
            }

            .login-btn {
                width: 100%;
                padding: 16px 22px;
                background: #2563eb;
                color: #fff;
                border: none;
                border-radius: 12px;
                font-size: 15px;
                font-weight: 700;
                font-family: inherit;
                cursor: pointer;
                letter-spacing: -0.01em;
                transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease, background 0.2s;
                box-shadow: 0 4px 14px -2px rgba(37, 99, 235, 0.45);
            }
            .login-btn:hover {
                background: #1d4ed8;
                filter: brightness(1.02);
                transform: translateY(-1px);
                box-shadow: 0 8px 22px -4px rgba(37, 99, 235, 0.5);
            }
            .login-btn:active {
                transform: translateY(0);
            }

            .login-footer {
                text-align: center;
                font-size: 12px;
                color: #9ca3af;
                margin-top: 1rem;
                font-weight: 500;
            }

            .login-error {
                font-size: 13px;
                color: #b91c1c;
                margin-top: 8px;
                margin-bottom: 0;
                font-weight: 500;
            }

            @media (prefers-reduced-motion: reduce) {
                .login-shell,
                .login-bg-stripes,
                .login-bg-aurora,
                .login-shell::before,
                .login-shell::after,
                .login-bg-wave svg,
                .login-bg-bubbles span {
                    animation: none !important;
                }
                .login-shell {
                    background-position: 0% 0%, 0% 0%, 0% 0%, 0% 50% !important;
                }
            }

            /* Tablet & mobile: satu kolom, kartu lebih ramping */
            @media (max-width: 1100px) {
                .login-shell {
                    align-items: center;
                    justify-content: center;
                }

                .login-card {
                    grid-template-columns: 1fr;
                    width: min(100%, 520px);
                    margin-inline: auto;
                    border-radius: 20px;
                }

                .login-col-form {
                    order: 1;
                    padding: clamp(1.35rem, 3.5vw, 1.75rem) clamp(1.25rem, 4vw, 1.75rem) clamp(1rem, 2.5vw, 1.25rem);
                }

                .login-col-visual {
                    order: 2;
                    padding: clamp(1rem, 3vw, 1.35rem) clamp(1rem, 4vw, 1.5rem) clamp(1.25rem, 3vw, 1.75rem);
                    min-height: 0;
                    max-height: min(240px, 32vh);
                }

                .login-hero {
                    max-width: 100%;
                }

                .login-hero .login-hero-img {
                    max-height: min(220px, 30vh);
                    width: auto;
                    max-width: 100%;
                    margin-inline: auto;
                }

                .login-logo-row {
                    margin-bottom: clamp(1rem, 3vw, 1.35rem);
                }

                .login-logo-row .login-logo-img {
                    height: clamp(72px, 14vw, 96px);
                }
            }

            /* Tablet landscape: kartu tetap satu kolom, ilustrasi lebih ringkas */
            @media (max-width: 1100px) and (min-width: 601px) {
                .login-card {
                    width: min(100%, 480px);
                }

                .login-col-visual {
                    max-height: min(200px, 28vh);
                }

                .login-hero .login-hero-img {
                    max-height: min(180px, 26vh);
                }
            }

            /* Tablet landscape pendek (tinggi layar kecil) */
            @media (max-width: 1100px) and (max-height: 720px) {
                .login-col-visual {
                    max-height: min(150px, 24vh);
                    padding-top: 0.75rem;
                    padding-bottom: 0.75rem;
                }

                .login-hero .login-hero-img {
                    max-height: min(140px, 22vh);
                }

                .login-logo-row .login-logo-img {
                    height: 64px;
                }

                .login-logo-row {
                    margin-bottom: 0.85rem;
                }

                .login-field {
                    margin-bottom: 12px;
                }

                .login-field input {
                    padding-top: 13px;
                    padding-bottom: 13px;
                }

                .login-remember {
                    margin-bottom: 16px;
                }

                .login-btn {
                    padding-top: 14px;
                    padding-bottom: 14px;
                }
            }

            @media (max-width: 600px) {
                .login-card {
                    width: 100%;
                    border-radius: 18px;
                }

                .login-col-form {
                    padding: 1.25rem 1.15rem 1rem;
                }

                .login-col-visual {
                    max-height: min(200px, 36vh);
                }

                .login-hero .login-hero-img {
                    max-height: min(180px, 34vh);
                }
            }

            [x-cloak] { display: none !important; }
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
        </style>
    </head>
    <body class="login-shell">
        <div class="login-bg-stripes" aria-hidden="true"></div>
        <div class="login-bg-aurora" aria-hidden="true"></div>
        <div class="login-bg-wave" aria-hidden="true">
            <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path fill="currentColor" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/>
            </svg>
        </div>
        <div class="login-bg-bubbles" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="login-card">
            <div class="login-col-visual">
                <figure class="login-hero">
                    <img
                        class="login-hero-img"
                        src="{{ asset('images/login/hero-starrich.png') }}"
                        width="560"
                        height="520"
                        alt="Starrich — aplikasi penjualan kafe dan POS"
                        decoding="async"
                    />
                </figure>
            </div>

            <div class="login-col-form">
                @yield('content')
            </div>
        </div>

        @include('partials.toast')
    </body>
</html>
