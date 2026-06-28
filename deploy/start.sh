#!/usr/bin/env sh
set -eu

cd /app

DB_PATH="${DB_DATABASE:-/app/database/database.sqlite}"
SHOULD_SEED_DEMO="false"

if [ "$(id -u)" = "0" ]; then
    mkdir -p "$(dirname "$DB_PATH")"
    chown -R www-data:www-data /app/storage /app/bootstrap/cache "$(dirname "$DB_PATH")"
    exec su-exec www-data "$0" "$@"
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p "$(dirname "$DB_PATH")"
    if [ "${RUN_DEMO_SEEDER:-true}" = "true" ] && [ ! -s "$DB_PATH" ]; then
        SHOULD_SEED_DEMO="true"
    fi
    touch "$DB_PATH"
    export DB_DATABASE="$DB_PATH"
elif [ "${RUN_DEMO_SEEDER:-true}" = "true" ]; then
    SHOULD_SEED_DEMO="true"
fi

php artisan migrate --force

if [ "$SHOULD_SEED_DEMO" = "true" ]; then
    php artisan db:seed --class=DemoSeeder --force
fi

php artisan config:cache
php artisan view:cache
php artisan storage:link >/dev/null 2>&1 || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
