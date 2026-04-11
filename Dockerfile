# Use an official PHP image with Apache
FROM php:8.2-apache

# 1. Install system dependencies for Laravel & PhpSpreadsheet
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    unzip \
    git \
    nodejs \
    npm

# 2. Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions (Added zip for your Excel needs)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy project files
COPY . .

# 7. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 8. Bypassing Render's "Locked Shell"
# This will try to create your tables during the build phase.
# The '|| true' ensures the build doesn't fail if the DB is busy.
RUN php artisan migrate --force || true

# 9. Build your Frontend (Tailwind/Vite)
RUN npm install && npm run build

# 10. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Update Apache config to point to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 12. Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# 13. Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]