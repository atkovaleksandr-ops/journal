<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Скачать Journal для Windows</title>
    @include('download._style')
</head>
<body>
    <main class="wrap">
        <section class="card">
            <div class="badge">Для компьютера</div>
            <h1>Установить Journal на Windows</h1>
            <p>
                Скачайте установщик и откройте его. Он установит приложение Journal,
                добавит его в меню Windows и предложит создать ярлык на рабочем столе.
            </p>

            <ol>
                <li>Нажмите кнопку скачивания ниже.</li>
                <li>Откройте файл <strong>Journal-Windows-Setup.exe</strong>.</li>
                <li>Выберите папку установки и отметьте, нужен ли ярлык на рабочем столе.</li>
                <li>Нажмите <strong>Установить</strong> и дождитесь завершения.</li>
                <li>После установки откройте Journal с рабочего стола или из меню Пуск.</li>
            </ol>

            <p class="note">
                Если Windows покажет предупреждение SmartScreen, откройте пункт
                <strong>Подробнее</strong> и подтвердите запуск. Это обычное предупреждение
                для новых приложений без цифровой подписи.
            </p>

            <div class="actions">
                <a href="{{ route('download.windows.installer', [], false) }}" class="btn">Скачать установщик Windows</a>
                <a href="{{ route('welcome', [], false) }}#platforms" class="btn btn-secondary">Назад</a>
            </div>
        </section>
    </main>
</body>
</html>
