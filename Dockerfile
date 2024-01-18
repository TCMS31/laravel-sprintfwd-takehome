# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 - composer dependencies
#
# Installed in their own stage so the (slow, network-bound) dependency layer is
# cached on composer.json/composer.lock alone and is not invalidated every time
# application source changes.
# ---------------------------------------------------------------------------
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

# ---------------------------------------------------------------------------
# Stage 2 - runtime
# ---------------------------------------------------------------------------
FROM php:8.3-cli-alpine AS runtime

# pdo_mysql for the shared database, pcntl so the queue worker can handle
# signals. The sqlite driver ships with the base image.
RUN set -eux; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql pcntl; \
    apk add --no-cache curl

# Production-leaning PHP defaults; opcache is already compiled into the image.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Non-root runtime user. Created before the copy so ownership is set once.
RUN addgroup -g 1000 -S app \
    && adduser -u 1000 -S -G app -h /app app

COPY --from=vendor --chown=app:app /app/vendor ./vendor
COPY --chown=app:app . .

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache \
    && chown -R app:app storage bootstrap/cache

COPY --chown=app:app docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER app

EXPOSE 8000

# Hits a real application route, so the check fails if the framework boots but
# the router or database config is broken - not just if the port is open.
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS -H 'Accept: application/json' http://127.0.0.1:8000/api/teams || exit 1

# artisan serve is adequate for a review environment. A production deployment
# would put php-fpm behind nginx instead; see "Limitations" in the README.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
