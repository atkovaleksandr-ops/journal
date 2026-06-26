# Приложения Journal

В этой папке лежат оболочки для сайта Journal.

- `windows-electron` - Windows-приложение, открывающее сайт в отдельном окне.
- `android-capacitor` - Android-приложение с экраном подключения к адресу сайта.

## Адрес сайта

По умолчанию используется:

```text
http://journal.test
```

Для Windows адрес можно переопределить через переменную `JOURNAL_APP_URL` или файл `journal-url.txt` рядом с `Journal.exe`.

Для Android адрес вводится на стартовом экране приложения и сохраняется на устройстве.

## Сборки

Скрипты в корне сайта:

```powershell
scripts\build-windows-pack.ps1
scripts\build-android-apk.ps1
```

Готовые установщики копируются в:

```text
public\downloads
```
