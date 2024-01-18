#!/bin/sh
# Wait for the database, apply migrations, then hand off to the container CMD.
set -eu

echo "[entrypoint] waiting for database..."
until php artisan db:monitor >/dev/null 2>&1; do
    sleep 2
done

echo "[entrypoint] running migrations"
php artisan migrate --force

if [ "${APP_SEED:-false}" = "true" ]; then
    echo "[entrypoint] seeding"
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache

exec "$@"
