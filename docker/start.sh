#!/bin/bash
set -e

echo "==> Starting AgriPower Portal..."

# Use Render's PORT or fallback to 80
export PORT="${PORT:-80}"

# Substitute the PORT into nginx config
envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default

# Ensure storage directories exist with proper permissions
mkdir -p /var/www/storage/framework/{sessions,views,cache}
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/app/public
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate application cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link (ignore if already exists)
php artisan storage:link 2>/dev/null || true

# Run migrations safely (NOT migrate:fresh — that drops everything)
# --force is required in production
echo "==> Running database migrations..."
php artisan migrate --force --seed 2>&1 || {
    echo "==> Migration with seed failed, trying without seed..."
    php artisan migrate --force 2>&1 || {
        echo "==> WARNING: Migrations failed. App will start but DB may be incomplete."
    }
}

echo "==> Starting Nginx..."
service nginx start

echo "==> Starting PHP-FPM..."
# Run PHP-FPM in foreground so the container stays alive
php-fpm --nodaemonize
