#!/bin/sh
set -e

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Seed only if the tenants table is empty
TENANT_COUNT=$(php artisan tinker --no-interaction --execute="echo \App\Models\Tenant::count();" 2>/dev/null | tail -1)
if [ "$TENANT_COUNT" = "0" ]; then
    php artisan db:seed --force
fi

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
