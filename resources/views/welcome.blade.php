<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal - Электронный журнал</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        (() => {
            const params = new URLSearchParams(window.location.search);

            if (params.has('journal_app')) {
                const appMarker = params.get('journal_app') || '1';
                localStorage.setItem('journal_app', appMarker);
                localStorage.setItem('journal_native_app', appMarker);
            }

            if (localStorage.getItem('journal_app') || localStorage.getItem('journal_native_app')) {
                document.documentElement.classList.add('is-native-app');
            }
        })();
    </script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        body {
            width: 100%;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(45, 212, 191, 0.18), transparent 26%),
                radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.18), transparent 28%),
                linear-gradient(135deg, #0f172a, #111827 48%, #132f39);
            color: #ffffff;
            overflow-x: hidden;
        }

        html.menu-open,
        body.menu-open {
            height: 100%;
            overflow: hidden;
            overscroll-behavior: none;
        }

        a {
            color: inherit;
        }

        .page {
            width: min(1280px, 100%);
            margin: 0 auto;
            padding: 24px 36px 48px;
            overflow-x: clip;
        }

        .header {
            position: sticky;
            top: 14px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 0 0 58px;
            padding: 14px 16px 14px 20px;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 22px;
            backdrop-filter: blur(18px);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.22);
        }

        .logo {
            flex: 0 0 auto;
            color: #ffffff;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0;
            text-decoration: none;
        }

        .logo span {
            color: #38bdf8;
        }

        .nav {
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 9px 12px;
            color: #dbeafe;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.2;
            white-space: nowrap;
            border-radius: 12px;
            border: 1px solid transparent;
            transition: 0.2s;
        }

        .nav-link:hover {
            color: #ffffff;
            background: rgba(59, 130, 246, 0.18);
            border-color: rgba(96, 165, 250, 0.28);
        }

        .nav-link-account {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.24), rgba(37, 99, 235, 0.28));
            border-color: rgba(125, 211, 252, 0.26);
        }

        .mobile-menu-toggle,
        .menu-button,
        .menu-overlay,
        .mobile-menu-head {
            display: none;
        }

        .mobile-menu-toggle {
            position: fixed;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .app-refresh-button {
            position: fixed;
            left: max(14px, env(safe-area-inset-left));
            top: max(14px, env(safe-area-inset-top));
            z-index: 80;
            display: none;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            color: #fff;
            font: inherit;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            border: 1px solid rgba(91, 213, 255, 0.38);
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            box-shadow: 0 16px 34px rgba(2, 6, 23, 0.42);
            cursor: pointer;
        }

        .is-native-app .app-refresh-button {
            display: inline-flex;
        }

        .app-refresh-button:active {
            transform: translateY(1px);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(340px, 0.92fr);
            align-items: center;
            gap: 56px;
            min-height: 600px;
        }

        .eyebrow {
            display: inline-flex;
            max-width: 100%;
            margin-bottom: 22px;
            padding: 9px 14px;
            color: #86efac;
            font-weight: 800;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.22);
            line-height: 1.35;
        }

        h1 {
            max-width: 700px;
            font-size: clamp(42px, 5.5vw, 72px);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: 0;
            margin-bottom: 26px;
        }

        h1 span {
            background: linear-gradient(90deg, #38bdf8, #4ade80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lead {
            max-width: 650px;
            color: #dbeafe;
            font-size: 20px;
            line-height: 1.7;
            margin-bottom: 34px;
        }

        .buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .is-native-app .app-download-only {
            display: none !important;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 54px;
            padding: 15px 20px;
            border-radius: 15px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: #ffffff;
            text-decoration: none;
            font-weight: 800;
            line-height: 1.2;
            text-align: center;
            transition: 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            box-shadow: 0 16px 34px rgba(37, 99, 235, 0.34);
        }

        .btn-secondary {
            background: rgba(30, 41, 59, 0.78);
        }

        .hero-card,
        .section,
        .mini-card {
            background: rgba(30, 41, 59, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.28);
        }

        .hero-card {
            min-width: 0;
            border-radius: 28px;
            padding: 34px;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .status {
            padding: 10px 16px;
            color: #4ade80;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 800;
            background: rgba(34, 197, 94, 0.14);
        }

        .circle {
            flex: 0 0 14px;
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: #4ade80;
            box-shadow: 0 0 18px #4ade80;
        }

        .reviews {
            display: grid;
            gap: 14px;
        }

        .review {
            display: flex;
            gap: 13px;
            align-items: flex-start;
            padding: 16px;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.34);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .avatar {
            display: grid;
            place-items: center;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            color: #0f172a;
            font-weight: 800;
            background: linear-gradient(135deg, #38bdf8, #4ade80);
        }

        .review-name {
            margin-bottom: 5px;
            color: #ffffff;
            font-weight: 800;
        }

        .review-text {
            color: #cbd5e1;
            line-height: 1.5;
        }

        .section {
            margin-top: 72px;
            padding: 34px;
            border-radius: 26px;
            scroll-margin-top: 110px;
        }

        .section h2 {
            margin-bottom: 12px;
            font-size: 34px;
            letter-spacing: 0;
        }

        .section > p {
            max-width: 840px;
            color: #cbd5e1;
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .mini-card {
            min-width: 0;
            min-height: 170px;
            padding: 24px;
            border-radius: 18px;
        }

        .mini-card h3 {
            margin-bottom: 12px;
            font-size: 21px;
        }

        .mini-card p,
        .mini-card li {
            color: #cbd5e1;
            line-height: 1.65;
        }

        .mini-card ol,
        .mini-card ul {
            padding-left: 20px;
        }

        .mini-card strong {
            color: #ffffff;
        }

        .step-card {
            position: relative;
            padding-top: 54px;
        }

        .step-number {
            position: absolute;
            top: 20px;
            left: 22px;
            display: inline-grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 10px;
            color: #0f172a;
            font-weight: 800;
            font-size: 13px;
            background: linear-gradient(135deg, #38bdf8, #4ade80);
            border: 0;
        }

        .wide-card {
            grid-column: span 2;
        }

        .footer {
            margin-top: 72px;
            padding-top: 28px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid rgba(148, 163, 184, 0.18);
            line-height: 1.5;
        }

        @media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
                min-height: auto;
                gap: 34px;
            }

            .hero-card {
                max-width: 720px;
            }
        }

        @media (max-width: 900px) {
            .page {
                padding: 16px 18px 38px;
            }

            .header {
                top: 8px;
                align-items: flex-start;
                flex-direction: column;
                margin-bottom: 42px;
                padding: 14px;
            }

            .logo {
                font-size: 28px;
            }

            .nav {
                width: 100%;
                justify-content: flex-start;
            }

            .nav-link {
                flex: 1 1 150px;
            }

            .hero-card,
            .section {
                padding: 24px;
            }

            .buttons .btn {
                flex: 1 1 220px;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 12px 12px 32px;
            }

            .header {
                position: sticky;
                top: 8px;
                align-items: center;
                flex-direction: row;
                justify-content: space-between;
                border-radius: 18px;
                margin-bottom: 30px;
                padding: 12px 14px;
            }

            .menu-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 10px 14px;
                color: #ffffff;
                font-weight: 800;
                border-radius: 13px;
                border: 1px solid rgba(125, 211, 252, 0.25);
                background: rgba(30, 41, 59, 0.82);
                cursor: pointer;
            }

            .menu-overlay {
                position: fixed;
                inset: 0;
                z-index: 19;
                background: rgba(2, 6, 23, 0.86);
                touch-action: none;
                display: none;
            }

            .nav {
                position: fixed;
                left: 12px;
                right: 12px;
                top: 72px;
                z-index: 20;
                width: auto;
                max-height: min(70dvh, 520px);
                padding: 18px;
                align-content: start;
                align-items: stretch;
                justify-content: flex-start;
                flex-direction: column;
                flex-wrap: nowrap;
                gap: 10px;
                background:
                    linear-gradient(180deg, rgba(30, 41, 59, 0.98), rgba(15, 23, 42, 0.98)),
                    #0f172a;
                border: 1px solid rgba(125, 211, 252, 0.22);
                border-radius: 24px;
                box-shadow: 0 24px 90px rgba(0, 0, 0, 0.48);
                transform: translateY(-16px) scale(0.98);
                transition: transform 0.24s ease, opacity 0.24s ease;
                overflow-y: auto;
                opacity: 0;
                pointer-events: none;
            }

            .nav::before {
                content: '';
                width: 46px;
                height: 5px;
                margin: 0 auto 4px;
                border-radius: 999px;
                background: rgba(148, 163, 184, 0.35);
            }

            .mobile-menu-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 6px;
                color: #ffffff;
                font-size: 24px;
                font-weight: 800;
            }

            .menu-close {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 46px;
                color: #e5e7eb;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(15, 23, 42, 0.72);
                cursor: pointer;
                font-size: 26px;
                line-height: 1;
            }

            .nav-link {
                flex: 0 0 auto;
                width: 100%;
                min-height: 54px;
                padding: 14px 16px;
                font-size: 16px;
                white-space: normal;
                text-align: left;
                justify-content: flex-start;
                border-radius: 17px;
                background: rgba(15, 23, 42, 0.46);
                border-color: rgba(148, 163, 184, 0.14);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
            }

            .nav-link-account {
                justify-content: center;
                margin-top: 6px;
                min-height: 56px;
                background: linear-gradient(135deg, #38bdf8, #2563eb);
                border-color: rgba(125, 211, 252, 0.28);
                box-shadow: 0 14px 32px rgba(37, 99, 235, 0.28);
            }

            .mobile-menu-toggle:checked ~ .menu-overlay {
                display: block;
            }

            .mobile-menu-toggle:checked ~ .nav {
                transform: translateY(0);
                opacity: 1;
                pointer-events: auto;
            }

            .eyebrow {
                border-radius: 16px;
                font-size: 14px;
            }

            h1 {
                font-size: 38px;
            }

            .lead {
                font-size: 17px;
            }

            .btn,
            .buttons .btn {
                width: 100%;
                flex-basis: 100%;
            }

            .hero-card,
            .section,
            .mini-card {
                border-radius: 18px;
            }

            .hero-card,
            .section {
                padding: 18px;
            }

            .card-top {
                align-items: center;
                flex-direction: row;
            }

            .review {
                padding: 14px;
            }

            .section {
                margin-top: 42px;
            }

            .section h2 {
                font-size: 28px;
            }

            .section > p {
                font-size: 16px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .wide-card {
                grid-column: auto;
            }
        }

        @media (max-width: 420px) {
            h1 {
                font-size: 32px;
            }

            .lead {
                font-size: 16px;
            }

            .hero-card,
            .section {
                padding: 16px 14px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="header">
            <a href="{{ route('welcome') }}" class="logo">Journal<span>.</span></a>

            <input type="checkbox" id="mobile-menu-toggle" class="mobile-menu-toggle" aria-hidden="true">
            <label for="mobile-menu-toggle" class="menu-button">Меню</label>
            <label for="mobile-menu-toggle" class="menu-overlay" aria-hidden="true"></label>

            <nav class="nav" aria-label="Меню главной страницы">
                <div class="mobile-menu-head">
                    <span>Меню</span>
                    <label for="mobile-menu-toggle" class="menu-close" aria-label="Закрыть меню">×</label>
                </div>
                <a href="#features" class="nav-link">Возможности</a>
                <a href="#platforms" class="nav-link app-download-only">Приложения</a>
                <a href="#workflow" class="nav-link">Как работает</a>
                <a href="#contacts" class="nav-link">Контакты</a>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="nav-link nav-link-account">
                    {{ auth()->check() ? 'Личный кабинет' : 'Войти' }}
                </a>
            </nav>
        </header>

        <main>
            <section class="hero">
                <div>
                    <div class="eyebrow">Единая система для учебного процесса</div>

                    <h1>
                        Journal - <span>электронный журнал</span> для преподавателей и студентов
                    </h1>

                    <p class="lead">
                        Ведите группы, предметы, уроки и посещаемость в одном месте.
                        Преподаватель управляет журналом, студент видит свои данные в личном кабинете,
                        а администратор контролирует доступ.
                    </p>

                    <div class="buttons">
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary">
                            Открыть журнал
                        </a>

                        <a href="{{ route('download.windows', [], false) }}" class="btn btn-secondary app-download-only">
                            Скачать на Windows
                        </a>

                        <a href="{{ route('download.android', [], false) }}" class="btn btn-secondary app-download-only">
                            Скачать на Android
                        </a>

                    </div>
                </div>

                <div class="hero-card" aria-label="Отзывы пользователей">
                    <div class="card-top">
                        <div class="status">Отзывы пользователей</div>
                        <div class="circle"></div>
                    </div>

                    <div class="reviews">
                        <div class="review">
                            <div class="avatar">А</div>
                            <div>
                                <div class="review-name">Александр</div>
                                <div class="review-text">Удобный интерфейс, быстро отмечаем присутствующих и пропуски.</div>
                            </div>
                        </div>

                        <div class="review">
                            <div class="avatar">В</div>
                            <div>
                                <div class="review-name">Владислав</div>
                                <div class="review-text">Легко вести группы, предметы и смотреть историю по студентам.</div>
                            </div>
                        </div>

                        <div class="review">
                            <div class="avatar">А</div>
                            <div>
                                <div class="review-name">Артем</div>
                                <div class="review-text">Бумажный журнал больше не нужен, все данные под рукой.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="section">
                <h2>Возможности</h2>
                <p>Journal помогает заменить разрозненные таблицы и бумажные журналы понятным личным кабинетом для каждой роли.</p>

                <div class="grid">
                    <article class="mini-card">
                        <h3>История посещений</h3>
                        <p>Студент видит посещённые и пропущенные занятия по каждому предмету.</p>
                    </article>

                    <article class="mini-card">
                        <h3>Посещаемость</h3>
                        <p>Отмечайте присутствие и пропуски. История сохраняется и доступна в личном кабинете.</p>
                    </article>

                    <article class="mini-card">
                        <h3>Группы и студенты</h3>
                        <p>Создавайте учебные группы, добавляйте студентов и связывайте записи с email для входа.</p>
                    </article>

                    <article class="mini-card">
                        <h3>Предметы</h3>
                        <p>Назначайте предметы группам, открывайте уроки и ведите журнал по конкретному направлению.</p>
                    </article>
                </div>
            </section>

            <section id="platforms" class="section app-download-only">
                <h2>Приложения</h2>
                <p>Journal можно открыть в браузере, установить на компьютер или поставить на Android-телефон. Пользователь входит в тот же аккаунт и продолжает работать с привычными разделами.</p>

                <div class="grid">
                    <article class="mini-card">
                        <h3>Windows</h3>
                        <p>Установите Journal на компьютер и открывайте журнал отдельным окном, без поиска вкладки в браузере.</p>
                    </article>

                    <article class="mini-card">
                        <h3>Android</h3>
                        <p>Поставьте приложение на телефон, чтобы быстро проверять предметы и посещаемость.</p>
                    </article>

                </div>
            </section>

            <section id="workflow" class="section">
                <h2>Как работает Journal</h2>
                <p>Каждая роль видит только свои задачи: администратор управляет доступом, преподаватель ведет журнал, студент следит за результатами.</p>

                <div class="grid">
                    <article class="mini-card step-card">
                        <div class="step-number">1</div>
                        <h3>Доступ под контролем</h3>
                        <p>Администратор добавляет преподавателей и помогает держать список пользователей в порядке.</p>
                    </article>

                    <article class="mini-card step-card">
                        <div class="step-number">2</div>
                        <h3>Журнал ведется быстрее</h3>
                        <p>Преподаватель открывает нужную группу, выбирает предмет и отмечает присутствующих на уроке.</p>
                    </article>

                    <article class="mini-card step-card">
                        <div class="step-number">3</div>
                        <h3>Студент видит результат</h3>
                        <p>Студент заходит в личный кабинет и сразу видит посещённые, пропущенные и ещё не отмеченные занятия.</p>
                    </article>
                </div>
            </section>

            <section id="contacts" class="section">
                <h2>Контакты</h2>
                <p>Напишите или позвоните, если нужна помощь со входом, установкой приложения или работой с журналом.</p>

                <div class="grid">
                    <article class="mini-card">
                        <h3>Email</h3>
                        <p><a href="mailto:wey333221@gmail.kz">wey333221@gmail.kz</a></p>
                    </article>

                    <article class="mini-card">
                        <h3>Телефон</h3>
                        <p><a href="tel:+77771234567">+7 (777) 123-45-67</a></p>
                    </article>

                </div>
            </section>
        </main>

        <footer class="footer">
            © 2026 Journal - электронный журнал для учебного процесса
        </footer>
    </div>

    <button type="button" class="app-refresh-button" id="appRefreshButton" aria-label="Обновить страницу" title="Обновить страницу">
        ↻
    </button>

    <script>
        document.getElementById('appRefreshButton')?.addEventListener('click', () => {
            window.location.reload();
        });

        (() => {
            const toggle = document.getElementById('mobile-menu-toggle');
            const links = document.querySelectorAll('.nav-link');
            let lockedScrollY = 0;

            if (!toggle) {
                return;
            }

            const closeMenu = () => {
                const wasOpen = document.body.classList.contains('menu-open');
                toggle.checked = false;
                document.documentElement.classList.remove('menu-open');
                document.body.classList.remove('menu-open');
                if (wasOpen) {
                    window.scrollTo(0, lockedScrollY);
                }
            };

            const openMenu = () => {
                lockedScrollY = window.scrollY;
                document.documentElement.classList.add('menu-open');
                document.body.classList.add('menu-open');
                if (history.state?.mobileMenu !== true) {
                    history.pushState({ mobileMenu: true }, '', window.location.href);
                }
            };

            toggle.addEventListener('change', () => {
                if (toggle.checked) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            links.forEach((link) => {
                link.addEventListener('click', closeMenu);
            });

            window.addEventListener('popstate', () => {
                if (toggle.checked) {
                    closeMenu();
                }
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && toggle.checked) {
                    closeMenu();
                }
            });
        })();
    </script>
</body>
</html>
