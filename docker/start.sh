#!/bin/bash

# Ensure we are in the right directory
cd /var/www

echo "==> Starting AgriPower Portal Startup Script..."

# Use Render's PORT or fallback to 80
PORT="${PORT:-80}"
echo "==> Using Port: $PORT"

# 1. Prepare Nginx Config
echo "==> Preparing Nginx configuration..."
mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled
sed "s/RENDER_PORT/$PORT/g" /etc/nginx/sites-available/default.template > /etc/nginx/sites-enabled/default

# 2. Setup storage permissions
echo "==> Setting permissions..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache
chown -R www-data:www-data /var/www
chmod -R 775 storage bootstrap/cache

# 3. Handle Laravel setup tasks
echo "==> Running Laravel setup tasks..."

# Force critical env vars for Render stability
export APP_DEBUG=true
export SESSION_DRIVER=cookie
export SESSION_SECURE_COOKIE=true

# Wait for DB
sleep 2

# Run migrations
echo "==> Running database migrations..."
php artisan migrate --force 2>&1

# Run seeders
echo "==> Running database seeders..."
php artisan db:seed --force 2>&1 || echo "==> Seeders finished."

# Cache config
echo "==> Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link --force 2>/dev/null || true

# 4. Start PHP-FPM in background
echo "==> Starting PHP-FPM..."
php-fpm -D

# Wait for PHP-FPM
echo "==> Waiting for PHP-FPM to listen on port 9000..."
while ! (timeout 1 bash -c "cat < /dev/null > /dev/tcp/127.0.0.1/9000") >/dev/null 2>&1; do
    sleep 1
done

# 5. Start Nginx in foreground
echo "==> Starting Nginx..."
exec nginx -g 'daemon off;'
