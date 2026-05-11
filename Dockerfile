FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    nginx nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build frontend assets
RUN npm install && npm run build

# Setup Nginx configuration
# Copy our custom config as a template
COPY docker/nginx.conf /etc/nginx/sites-available/default.template

# Ensure log directories are linked to stdout/stderr
RUN ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log

# Copy startup script and make it executable
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Set initial permissions (start.sh will refine these)
RUN chown -R www-data:www-data /var/www

# Render uses the PORT env variable (default 10000)
EXPOSE 10000

# Start the application via the startup script
CMD ["/usr/local/bin/start.sh"]
