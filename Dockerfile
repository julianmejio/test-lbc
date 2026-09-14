FROM php:8.4-fpm AS base

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    libonig-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable opcache

WORKDIR /var/www

FROM base AS prod

ENV APP_ENV=prod

COPY docker/php/* /usr/local/etc/php/conf.d/*
COPY . .

RUN rm -rf var/cache var/log var/share \
    && composer install --no-interaction --optimize-autoloader --no-dev \
    && composer dump-autoload --no-dev --classmap-authoritative \
    && chown -R www-data:www-data /var/www

USER www-data

VOLUME /var/www

EXPOSE 9000

CMD ["php-fpm"]

FROM base AS dev

ENV APP_ENV=dev

COPY . .

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
