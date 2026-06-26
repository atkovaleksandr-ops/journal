#!/usr/bin/env sh
set -eu

cd /app

DB_PATH="${DB_DATABASE:-/app/database/database.sqlite}"

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p "$(dirname "$DB_PATH")"
    touch "$DB_PATH"
    export DB_DATABASE="$DB_PATH"
fi

php artisan migrate --force

if [ "${RUN_DEMO_SEEDER:-true}" = "true" ]; then
    php artisan db:seed --class=DemoSeeder --force
fi

php artisan config:cache
php artisan view:cache
php artisan storage:link >/dev/null 2>&1 || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
