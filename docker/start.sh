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

# Force critical env vars for Render stability at runtime
export APP_DEBUG=true
export SESSION_DRIVER=cookie
export SESSION_SECURE_COOKIE=true
export LOG_CHANNEL=stderr

# Wait for DB to be responsive
echo "==> Checking database connection..."
MAX_DB_RETRIES=5
DB_COUNT=0
while ! php artisan db:show >/dev/null 2>&1; do
    echo "    Waiting for DB... ($DB_COUNT/$MAX_DB_RETRIES)"
    sleep 3
    DB_COUNT=$((DB_COUNT + 1))
    if [ $DB_COUNT -ge $MAX_DB_RETRIES ]; then
        echo "==> WARNING: Database connection check failed. Attempting migrations anyway..."
        break
    fi
done

# Run migrations
echo "==> Running database migrations..."
php artisan migrate --force 2>&1

# Run seeders only if users table is empty
echo "==> Checking if seeding is needed..."
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null)
if [[ "$USER_COUNT" == "0" ]]; then
    echo "==> Seeding database..."
    php artisan db:seed --force 2>&1
else
    echo "==> Database already has $USER_COUNT users, skipping seeder."
fi

# We SKIP config:cache to allow environment variables to be read dynamically
echo "==> Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

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
