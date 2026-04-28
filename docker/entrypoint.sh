#!/bin/sh
set -e

# Discover and register package service providers
php artisan package:discover --ansi

# Generate app key if not provided
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php artisan key:generate --show --no-ansi)
fi

# Run migrations
php artisan migrate --force

# Seed only on first run (empty tenants table)
TENANT_COUNT=$(php -r "
    require 'vendor/autoload.php';
    \$app = require 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo \App\Models\Tenant::count();
" 2>/dev/null || echo "0")

if [ "$TENANT_COUNT" = "0" ]; then
    php artisan db:seed --force
fi

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
