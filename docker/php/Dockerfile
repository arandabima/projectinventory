FROM php:8.4-fpm-alpine

WORKDIR /var/www/html
RUN apk add --no-cache git unzip libzip-dev oniguruma-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
CMD ["php-fpm"]
