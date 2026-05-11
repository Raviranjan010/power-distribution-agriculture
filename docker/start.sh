#!/bin/bash

echo "==> Starting AgriPower Portal..."
echo "==> PHP Version: $(php -v | head -n 1)"

# Use Render's PORT or fallback to 80
PORT="${PORT:-80}"
echo "==> Using PORT: $PORT"

# Substitute ONLY the port into nginx config using sed (safe for nginx variables)
sed "s/RENDER_PORT/$PORT/g" /etc/nginx/sites-available/default.template > /etc/nginx/sites-enabled/default

# Test nginx config
echo "==> Testing nginx config..."
nginx -t 2>&1

# Ensure storage directories exist with proper permissions
echo "==> Setting up storage directories..."
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/app/public
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create storage link (ignore if already exists)
php artisan storage:link 2>/dev/null || true

# Clear stale caches before rebuilding
echo "==> Clearing caches..."
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true
php artisan cache:clear 2>&1 || true

# Debug: show key env vars (without revealing secrets)
echo "==> DB_CONNECTION: ${DB_CONNECTION:-not set (will default to mysql)}"
echo "==> DB_HOST: ${DB_HOST:-not set}"
echo "==> DB_DATABASE: ${DB_DATABASE:-not set}"
echo "==> APP_ENV: ${APP_ENV:-not set}"

# Generate application cache for production
echo "==> Caching config..."
php artisan config:cache 2>&1 || {
    echo "==> WARNING: config:cache failed, clearing it..."
    php artisan config:clear 2>&1 || true
}
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

# Run migrations safely (NOT migrate:fresh — that drops everything)
echo "==> Running database migrations..."
php artisan migrate --force 2>&1 || {
    echo "==> WARNING: Migrations had issues, but continuing..."
}

# Run seeders separately (only if tables are empty)
echo "==> Running database seeders..."
php artisan db:seed --force 2>&1 || {
    echo "==> WARNING: Seeding had issues (tables may already have data), continuing..."
}

# Start Nginx in background
echo "==> Starting Nginx..."
service nginx start 2>&1 || {
    echo "==> Nginx service failed, trying direct start..."
    nginx 2>&1 || echo "==> ERROR: Nginx failed to start!"
}

# Verify nginx is running
sleep 1
if pgrep nginx > /dev/null; then
    echo "==> Nginx is running."
else
    echo "==> ERROR: Nginx is NOT running!"
fi

echo "==> Starting PHP-FPM on port 9000..."
echo "==> Application should be available on port $PORT"

# Run PHP-FPM in foreground so the container stays alive
# Using exec replaces the shell process with php-fpm for proper signal handling
exec php-fpm --nodaemonize
