# Journal для Windows

Electron-оболочка открывает сайт Journal в отдельном окне.

## Адрес сайта

Порядок выбора адреса:

1. Переменная окружения `JOURNAL_APP_URL`.
2. Файл `journal-url.txt` рядом с `Journal.exe`.
3. Файл `journal-url.txt` в пользовательской папке приложения.
4. Значение по умолчанию: `http://journal.test`.

Пример `journal-url.txt`:

```text
https://journal.example.com
```

## Запуск для проверки

```bash
cd C:\laragon\www\journal\apps\windows-electron
npm install
npm start
```

## Сборка установщика

```bash
npm run dist
```

Корневой скрипт `scripts\build-windows-pack.ps1` собирает установщик и копирует его в `public\downloads`.
