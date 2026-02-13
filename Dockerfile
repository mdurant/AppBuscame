FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev

RUN docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app
WORKDIR /var/www
COPY --chown=app:app . .

USER app
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

USER root
RUN chown -R app:app /var/www/storage /var/www/bootstrap/cache
USER app

EXPOSE 9000
CMD ["php-fpm"]
