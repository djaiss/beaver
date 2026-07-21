#!/bin/sh
#
# Container entrypoint for every Beaver role (web, queue, scheduler).
#
# It prepares the writable directories, waits for the database, applies
# pending migrations (web container only) and warms the framework caches
# before handing control to the container command.
#
# Upgrade safety: this script only ever runs forward migrations
# (`migrate --force`). It never runs `migrate:fresh` or `migrate:refresh`,
# so pulling a newer image applies only the new migrations and never drops
# or resets an existing database.

set -eu

ROLE="${CONTAINER_ROLE:-app}"

# Run artisan and any writes as the unprivileged runtime user.
as_www() {
    su-exec www-data:www-data "$@"
}

# ---------------------------------------------------------------------------
# 1. Writable directories.
#
# A fresh named volume mounted over storage/ starts empty, so recreate the
# framework skeleton every boot and make sure it is owned by www-data.
# ---------------------------------------------------------------------------
mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/private \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# ---------------------------------------------------------------------------
# 2. Application key.
#
# The key must be identical across every container, so it is required rather
# than generated at boot (a per-container key would corrupt sessions and
# encrypted columns).
# ---------------------------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not set." >&2
    echo "Generate one and add it to your .env file:" >&2
    echo "  docker compose run --rm app php artisan key:generate --show" >&2
    exit 1
fi

# ---------------------------------------------------------------------------
# 3. Wait for the database.
# ---------------------------------------------------------------------------
DB_CONNECTION="${DB_CONNECTION:-mysql}"

if [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Waiting for the database at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
    attempt=0
    until as_www php -r '
        $dsn = getenv("DB_CONNECTION").":host=".getenv("DB_HOST").";port=".(getenv("DB_PORT") ?: "3306");
        try {
            new PDO($dsn, getenv("DB_USERNAME"), getenv("DB_PASSWORD"), [PDO::ATTR_TIMEOUT => 2]);
        } catch (Throwable $e) {
            exit(1);
        }
    ' 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 60 ]; then
            echo "ERROR: database not reachable after 60 attempts." >&2
            exit 1
        fi
        sleep 2
    done
    echo "Database is up."
fi

# ---------------------------------------------------------------------------
# 4. Migrations (web container only, so exactly one process migrates).
# ---------------------------------------------------------------------------
if [ "$ROLE" = "app" ] && [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Applying pending migrations (forward only, existing data preserved)..."
    as_www php artisan migrate --force --no-interaction
fi

# ---------------------------------------------------------------------------
# 5. Warm the framework caches.
#
# Route caching is intentionally skipped: the application defines a closure
# route, which cannot be serialized by `route:cache`.
# ---------------------------------------------------------------------------
as_www php artisan config:cache
as_www php artisan event:cache
as_www php artisan view:cache
as_www php artisan docs:cache

echo "Starting role '${ROLE}': $*"
exec "$@"
