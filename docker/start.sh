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

# Wait a bit for the DB to be ready if it's just starting (Aiven DB is usually always up)
sleep 2

# Check if artisan is working and show info
php artisan --version || echo "==> ERROR: artisan not found or PHP failing"

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations
echo "==> Running database migrations..."
php artisan migrate --force 2>&1 || echo "==> WARNING: Migrations failed. Check your DB credentials."

# Run seeders
echo "==> Running database seeders..."
php artisan db:seed --force 2>&1 || echo "==> WARNING: Seeding failed. This is expected if data already exists."

# Cache config for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link --force 2>/dev/null || true

# 4. Start PHP-FPM in background
echo "==> Starting PHP-FPM..."
php-fpm -D

# Wait for PHP-FPM to be ready on port 9000
echo "==> Waiting for PHP-FPM to listen on port 9000..."
MAX_RETRIES=10
COUNT=0
while ! (timeout 1 bash -c "cat < /dev/null > /dev/tcp/127.0.0.1/9000") >/dev/null 2>&1; do
    echo "    Waiting for PHP-FPM... ($COUNT/$MAX_RETRIES)"
    sleep 2
    COUNT=$((COUNT + 1))
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "==> ERROR: PHP-FPM failed to start on port 9000"
        break
    fi
done

# 5. Start Nginx in foreground
echo "==> Starting Nginx in foreground..."
# We run nginx in foreground so Render can manage the process
exec nginx -g 'daemon off;'
