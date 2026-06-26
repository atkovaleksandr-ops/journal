<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Скачать Journal для Android</title>
    @include('download._style')
</head>
<body>
    <main class="wrap">
        <section class="card">
            <div class="badge">Для телефона</div>
            <h1>Установить Journal на Android</h1>
            <p>
                Скачайте APK-файл на телефон и установите приложение. После запуска
                Journal сразу откроет актуальный адрес сайта в отдельной мобильной оболочке.
            </p>

            <ol>
                <li>Нажмите кнопку скачивания на телефоне.</li>
                <li>Откройте скачанный файл <strong>Journal-Android.apk</strong>.</li>
                <li>Если Android попросит разрешение, включите установку из неизвестных источников.</li>
                <li>Завершите установку и откройте Journal.</li>
            </ol>

            <div class="actions">
                <a href="{{ route('download.android.file', [], false) }}" class="btn">Скачать на Android</a>
                <a href="{{ route('welcome', [], false) }}#platforms" class="btn btn-secondary">Назад</a>
            </div>
        </section>
    </main>
</body>
</html>
