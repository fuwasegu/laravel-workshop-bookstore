FROM dunglas/frankenphp:1.4-php8.4.5-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && \
    apt-get install -y zip unzip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pdo_mysql bcmath

COPY --from=composer:2.7.1 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock /app/
RUN --mount=type=cache,target=/root/.composer/cache composer install --no-scripts

COPY . /app
RUN composer dump-autoload

COPY ./Caddyfile /etc/caddy/Caddyfile

EXPOSE 80
