FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize && php artisan package:discover --ansi

FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm ci --ignore-scripts && npm run build

FROM php:8.3-cli-alpine

WORKDIR /app

RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev sqlite-dev su-exec \
    && docker-php-ext-install intl mbstring pdo pdo_sqlite zip

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build
COPY deploy/start.sh /usr/local/bin/start

RUN chmod +x /usr/local/bin/start \
    && mkdir -p /app/storage/logs /app/bootstrap/cache /data \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database /data

EXPOSE 8080

CMD ["start"]
