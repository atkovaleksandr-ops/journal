# Journal

Journal - Laravel-приложение для учета посещаемости. Система ведет уроки, присутствия, пропуски, пустые отметки и заметки преподавателя.

## Deployment

For a short public demo, use the Docker setup in `Dockerfile` with `railway.json`. See `DEPLOYMENT.md`.

## Где лежит сайт

```text
C:\laragon\www\journal
```

Laragon vhost:

```text
http://journal.test
DocumentRoot: C:\laragon\www\journal\public
```

## Основные роли

- `admin` управляет преподавателями, студентами и контрольными страницами.
- `teacher` ведет группы, предметы, уроки и посещаемость.
- `student` смотрит свою историю посещаемости по предметам.

## Локальный запуск

1. Запустите Laragon Apache.
2. Откройте `http://journal.test`.
3. Если зависимости не установлены:

```bash
composer install
npm install
php artisan migrate
npm run build
```

Если `npm` не найден в обычной консоли, используйте Node из Laragon:

```powershell
$env:PATH = 'C:\laragon\bin\nodejs\node-v22;' + $env:PATH
npm run build
```

## Проверка

```bash
php artisan test
php artisan view:cache
npm run build
```

## Приложения

Оболочки лежат в `apps/`:

- `apps/windows-electron` - Windows-приложение на Electron.
- `apps/android-capacitor` - Android-приложение на Capacitor.

Готовые файлы для скачивания сайт отдает из:

```text
public\downloads
```

## Посещаемость

Статусы хранятся в `App\Models\Attendance`:

- `present` - присутствовал.
- `absent` - отсутствовал.

Отметка может содержать `note`: причину пропуска, уточнение или короткий комментарий преподавателя.
