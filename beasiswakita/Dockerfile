FROM php:8.3-fpm

# UID/GID dipakai agar file hasil `composer`/`artisan` di dalam container
# dimiliki oleh user Anda di host (hindari masalah permission di Linux).
ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        curl \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_HOME=/tmp/composer \
    COMPOSER_ALLOW_SUPERUSER=1

# Konfigurasi PHP
COPY docker/php/local.ini /usr/local/etc/php/conf.d/zz-local.ini

# Samakan UID/GID worker php-fpm (www-data) dengan user host
RUN groupmod -o -g ${GID} www-data \
    && usermod -o -u ${UID} -g ${GID} www-data

WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]
