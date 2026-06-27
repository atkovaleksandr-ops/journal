<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Journal') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *,
            *::before,
            *::after {
                box-sizing: border-box;
            }

            html {
                width: 100%;
                overflow-x: hidden;
                -webkit-text-size-adjust: 100%;
            }

            body {
                margin: 0;
                width: 100%;
                min-height: 100vh;
                font-family: 'Figtree', Arial, sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 28%),
                    radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 30%),
                #0f172a;
                color: #e5e7eb;
                overflow-x: hidden;
            }

            .guest-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 28px 18px;
            }

            .guest-card {
                width: 100%;
                max-width: 390px;
                padding: 28px 30px;
                border-radius: 22px;
                background: rgba(15, 23, 42, 0.82);
                border: 1px solid rgba(148, 163, 184, 0.22);
                box-shadow: 0 24px 80px rgba(0, 0, 0, 0.32);
                color: #e5e7eb;
                overflow: hidden;
            }

            .guest-card h1 {
                margin: 0;
                color: #ffffff;
                font-size: 24px;
                line-height: 1.2;
                font-weight: 800;
                letter-spacing: 0;
            }

            .guest-card p {
                color: #cbd5e1;
                line-height: 1.5;
                margin: 8px 0 0;
            }

            .guest-logo {
                display: inline-flex;
                margin-bottom: 22px;
                color: #ffffff;
                font-size: 28px;
                font-weight: 800;
                text-decoration: none;
            }

            .guest-logo span {
                color: #38bdf8;
            }

            .guest-card label,
            .guest-card .text-gray-600,
            .guest-card .text-gray-700,
            .guest-card .text-gray-900 {
                color: #cbd5e1 !important;
                font-family: inherit;
            }

            .guest-card form {
                display: grid;
                gap: 13px;
                min-width: 0;
            }

            .guest-card form > div {
                margin-top: 0 !important;
            }

            .guest-card label {
                display: inline-block;
                margin-bottom: 8px;
                font-size: 14px;
                font-weight: 700;
            }

            .guest-card a {
                color: #7dd3fc !important;
                font-family: inherit;
                font-weight: 700;
                line-height: 1.35;
            }

            .guest-card input:not([type="checkbox"]),
            .guest-card select,
            .auth-input {
                width: 100%;
                color: #ffffff !important;
                background: rgba(15, 23, 42, 0.92) !important;
                border-color: rgba(148, 163, 184, 0.32) !important;
                border-radius: 11px !important;
                height: 38px !important;
                min-height: 38px !important;
                max-height: 38px !important;
                padding: 7px 11px !important;
                font-family: inherit !important;
                font-size: 15px !important;
                line-height: 1.2 !important;
            }

            .guest-card input:focus,
            .guest-card select:focus {
                border-color: #38bdf8 !important;
                box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16) !important;
            }

            .remember-control {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 0 !important;
                cursor: pointer;
                user-select: none;
            }

            .remember-control input {
                width: 18px;
                height: 18px;
                min-height: 18px;
                flex: 0 0 18px;
                cursor: pointer;
                accent-color: #38bdf8;
            }

            .remember-control span {
                color: #cbd5e1;
                font-size: 14px;
                line-height: 1.35;
            }

            .guest-card button {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 86px;
                width: auto !important;
                min-height: 38px !important;
                padding: 8px 16px !important;
                border-radius: 11px !important;
                background: linear-gradient(135deg, #2563eb, #06b6d4) !important;
                color: #ffffff !important;
                font-weight: 800 !important;
                font-family: inherit !important;
                font-size: 14px !important;
                line-height: 1.2 !important;
                text-transform: none !important;
                letter-spacing: 0 !important;
                border: none !important;
                cursor: pointer;
                white-space: nowrap;
            }

            .guest-card .flex.items-center.justify-between {
                align-items: center;
                justify-content: space-between;
                gap: 24px;
                margin-top: 2px !important;
            }

            .auth-form {
                display: grid;
                gap: 13px;
                min-width: 0;
            }

            .auth-field {
                display: grid;
                gap: 7px;
                min-width: 0;
            }

            .auth-field label {
                margin-bottom: 0 !important;
            }

            .auth-help-link {
                font-size: 13px;
                text-decoration: none;
                white-space: nowrap;
            }

            .auth-help-link:hover,
            .auth-alt-action a:hover {
                text-decoration: underline;
            }

            .auth-options {
                display: grid;
                gap: 8px;
                margin-top: -1px;
                padding: 2px 0 0;
            }

            .auth-help-row {
                display: flex;
                justify-content: flex-start;
                margin-top: -1px !important;
                padding-bottom: 0;
            }

            .auth-options-single {
                padding-bottom: 2px;
            }

            .auth-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                margin-top: 0;
            }

            .auth-submit-row {
                margin-top: 2px;
                display: grid;
            }

            .auth-submit {
                width: 100% !important;
                min-height: 42px !important;
                border-radius: 12px !important;
                box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
            }

            .auth-footer {
                color: #cbd5e1;
                font-size: 15px;
                line-height: 1.45;
            }

            .auth-alt-action {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-wrap: wrap;
                gap: 5px;
                color: #cbd5e1;
                font-size: 14px;
                line-height: 1.45;
                text-align: center;
                padding-top: 2px;
            }

            .auth-alt-action a {
                text-decoration: none;
            }

            .guest-card .flex.items-center.justify-between > a {
                flex: 1 1 auto;
                min-width: 0;
            }

            .guest-card .flex.items-center.justify-between > button {
                flex: 0 0 auto;
            }

            .guest-card .mt-5.text-sm {
                color: #cbd5e1 !important;
                margin-top: 0 !important;
                line-height: 1.45;
            }

            @media (max-width: 640px) {
                .guest-shell {
                    align-items: flex-start;
                    padding: 18px 12px;
                }

                .guest-card {
                    width: min(100%, 430px);
                    max-width: none;
                    padding: 22px 18px;
                    border-radius: 18px;
                }

                .guest-logo {
                    font-size: 24px;
                    margin-bottom: 18px;
                }

                .guest-card .flex {
                    flex-wrap: wrap;
                }

                .guest-card button {
                    width: auto;
                    justify-content: center;
                    margin-top: 8px;
                }

                .guest-card .flex.items-center.justify-between {
                    align-items: stretch;
                    flex-direction: column;
                    gap: 12px;
                }

                .guest-card .flex.items-center.justify-between > button {
                    width: 100% !important;
                }

                .auth-actions {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 10px;
                }
            }

            @media (max-width: 420px) {
                .guest-card {
                    padding: 18px 14px;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="guest-shell">
            <div class="guest-card">
                <a href="{{ route('welcome') }}" class="guest-logo">Journal<span>.</span></a>
                {{ $slot }}
            </div>
        </div>

        <script>
            (() => {
                const emailSymbolMap = {
                    А: 'a',
                    а: 'a',
                    В: 'b',
                    Е: 'e',
                    е: 'e',
                    К: 'k',
                    к: 'k',
                    М: 'm',
                    Н: 'h',
                    О: 'o',
                    о: 'o',
                    Р: 'p',
                    р: 'p',
                    С: 'c',
                    с: 'c',
                    Т: 't',
                    Х: 'x',
                    х: 'x',
                    У: 'y',
                    у: 'y',
                };

                const normalizeEmail = (value) => value
                    .replace(/[АаВЕеКкМНОоРрСсТХхУу]/g, (symbol) => emailSymbolMap[symbol] ?? symbol)
                    .replace(/\s+/g, '')
                    .toLowerCase();

                document.querySelectorAll('input[type="email"]').forEach((input) => {
                    const syncValue = () => {
                        const nextValue = normalizeEmail(input.value);

                        if (nextValue !== input.value) {
                            const cursor = input.selectionStart ?? nextValue.length;
                            input.value = nextValue;
                            input.setSelectionRange(Math.min(cursor, nextValue.length), Math.min(cursor, nextValue.length));
                        }
                    };

                    input.setAttribute('inputmode', 'email');
                    input.setAttribute('spellcheck', 'false');
                    input.setAttribute('autocapitalize', 'none');
                    input.addEventListener('input', syncValue);
                    input.addEventListener('blur', syncValue);
                    syncValue();
                });
            })();
        </script>
    </body>
</html>
