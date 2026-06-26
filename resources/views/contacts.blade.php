<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты — Journal</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #132f39);
            color: #fff;
        }

        .wrap {
            max-width: 920px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .card {
            padding: 34px;
            border-radius: 24px;
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
        }

        h1 {
            margin: 0 0 14px;
            font-size: 42px;
        }

        p {
            color: #cbd5e1;
            font-size: 18px;
            line-height: 1.7;
        }

        a {
            color: #7dd3fc;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin: 28px 0;
        }

        .item {
            padding: 20px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .btn {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 14px;
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #38bdf8, #2563eb);
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="card">
            <h1>Контакты</h1>
            <p>Свяжитесь с нами, если нужна помощь со входом, установкой приложения или работой с журналом.</p>

            <div class="grid">
                <div class="item">
                    <h2>Email</h2>
                    <p><a href="mailto:wey333221@gmail.kz">wey333221@gmail.kz</a></p>
                </div>

                <div class="item">
                    <h2>Телефон</h2>
                    <p><a href="tel:+77771234567">+7 (777) 123-45-67</a></p>
                </div>
            </div>

            <a href="{{ route('welcome') }}#contacts" class="btn">Вернуться на главную</a>
        </section>
    </main>
</body>
</html>
