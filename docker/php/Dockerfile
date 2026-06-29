#############################################
# Stage 1 - Composer
#############################################

FROM composer:2 AS composer

WORKDIR /app


COPY . .

RUN composer clear-cache && \
    composer config preferred-install source && \
    composer install \
    --no-dev \
    --prefer-source \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

#############################################
# Stage 2 - Node / Vite Build
#############################################

FROM node:22-alpine AS node

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build



#############################################
# Stage 3 - Production PHP
#############################################

FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    bcmath \
    gd \
    intl \
    exif \
    zip \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY . .

COPY --from=composer /app/vendor ./vendor

RUN rm -f bootstrap/cache/packages.php \
    bootstrap/cache/services.php

RUN php artisan package:discover --ansi

COPY --from=node /app/public/build ./public/build

RUN mkdir -p /var/www-public-template

RUN cp -a public/. /var/www-public-template/

COPY docker/php/php.ini \
    /usr/local/etc/php/conf.d/custom.ini

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

RUN chmod -R 775 \
    storage \
    bootstrap/cache

EXPOSE 9000

COPY docker/php/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]
