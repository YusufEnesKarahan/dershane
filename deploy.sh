#!/usr/bin/env bash

set -e

echo "=== Starting Production Deployment ==="

# 1. Enable Maintenance Mode
php artisan down || true

# 2. Pull Latest Code
echo "Pulling latest code from repository..."
git pull origin main

# 3. Install Composer Dependencies (No Dev)
echo "Installing Composer dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader

# 4. Build Frontend Assets
echo "Building assets with NPM..."
if [ -f "package.json" ]; then
    npm ci
    npm run build
fi

# 5. Database Migrations
echo "Running database migrations..."
php artisan migrate --force

# 6. Storage Link Ensure
php artisan storage:link || true

# 7. Clear & Cache Configurations
echo "Warming production caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Restart Queue Workers
echo "Restarting queue workers..."
php artisan queue:restart

# 9. Disable Maintenance Mode
php artisan up

echo "=== Deployment Completed Successfully ==="
