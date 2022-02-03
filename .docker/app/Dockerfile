# BASE image
FROM php:8.1-fpm as base

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libzip-dev \
    && docker-php-ext-install opcache zip \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Composer
FROM composer:2 as composer


# Base development image (base image + development tools)
FROM base as dev

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    && pecl install xdebug-3.1.3 pcov \
    && docker-php-ext-enable xdebug pcov \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app

ENV APP_DEBUG=1 \
    COMPOSER_MEMORY_LIMIT=-1

ARG APP_USER_ID
ARG APP_GROUP_ID

COPY ./.docker/app/zz-dev.ini /usr/local/etc/php/conf.d/zz-dev.ini
RUN usermod -u $APP_USER_ID www-data --shell /bin/bash && groupmod -g $APP_GROUP_ID www-data

USER www-data:www-data
