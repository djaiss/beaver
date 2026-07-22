# syntax=docker/dockerfile:1

# KolleK production image.
#
# Multi-stage build:
#   1. vendor  — install PHP dependencies with Composer.
#   2. assets  — compile the front-end bundle with Vite.
#   3. app     — the runtime image (PHP-FPM + nginx + supervisor).
#
# The same final image runs the web server, the queue worker and the
# scheduler; the role is selected through the CONTAINER_ROLE environment
# variable and the container command (see docker-compose.yml).

############################################
# Stage 1 — PHP dependencies (Composer)
############################################
FROM composer:2 AS vendor

WORKDIR /app

# Install dependencies first (better layer caching), without running the
# artisan-based scripts since the framework is not bootstrappable yet.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Bring in the source and build the optimized, authoritative autoloader.
COPY . .
RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-interaction

############################################
# Stage 2 — Front-end assets (Vite)
############################################
FROM node:22-alpine AS assets

WORKDIR /app

# Install JS dependencies against the lockfile for reproducible builds.
COPY package.json package-lock.json ./
RUN npm ci

# The Tailwind theme scans the Blade templates and the vendored pagination
# views (see the @source directives in resources/css/app.css), so the whole
# source tree and the composer vendor directory must be present at build time.
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

############################################
# Stage 3 — Runtime (PHP-FPM + nginx)
############################################
FROM php:8.4-fpm-alpine AS app

# System packages and PHP extensions the stack needs:
#   gd + exif  — image processing (intervention/image) and QR codes.
#   pcntl      — graceful queue worker restarts.
#   bcmath     — two-factor authentication.
#   pdo_mysql  — the default database driver (pdo_sqlite kept as a fallback).
#   redis      — optional cache/queue/session driver.
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN apk add --no-cache nginx supervisor su-exec \
    && install-php-extensions \
        pdo_mysql \
        pdo_sqlite \
        gd \
        exif \
        bcmath \
        pcntl \
        intl \
        zip \
        opcache \
        redis \
    && rm -rf /var/cache/apk/* /tmp/*

WORKDIR /var/www/html

# PHP / FPM / nginx / supervisor configuration.
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Application source, vendored PHP dependencies and the compiled assets.
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

# Generate the package manifest and prepare the writable directories.
RUN php artisan package:discover --ansi \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/app/private \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80

# The web container answers Laravel's health endpoint. Worker and scheduler
# containers have no HTTP surface, so this check is a no-op for them.
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD wget --spider -q http://127.0.0.1/up || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
