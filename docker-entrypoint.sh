#!/bin/sh
set -e

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Optimizing resources..."
php artisan optimize
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Start FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
