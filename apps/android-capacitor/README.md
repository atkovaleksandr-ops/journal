# Journal для Android

Capacitor-оболочка показывает экран подключения к сайту Journal. Адрес вводится в приложении и сохраняется на устройстве.

## Важно про локальный адрес

`http://journal.test` работает на компьютере с Laragon. Для телефона нужен адрес, который телефон реально видит: домен, HTTPS-публикация или IP компьютера в локальной сети.

## Подготовка

```bash
cd C:\laragon\www\journal\apps\android-capacitor
npm install
npm run sync
```

## Открыть в Android Studio

```bash
npm run open
```

## Собрать debug APK

```bash
npm run build:apk
```

Корневой скрипт `scripts\build-android-apk.ps1` собирает APK и копирует его в `public\downloads`.
