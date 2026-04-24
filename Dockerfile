# Use an official PHP image with Apache
FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    curl \
    unzip \
    git \
    nodejs \
    npm

# 2. Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy project files
COPY . .

# 7. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 8. Build your Frontend (Tailwind/Vite)
RUN npm install && npm run build

# 9. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Update Apache config to point to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 11. Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# 12. Expose port 80
EXPOSE 80

# 13. THE FIX: Creates storage link, runs migrations, and starts Apache
# Using sh -c is the most reliable way to chain these commands on Render.
CMD ["sh", "-c", "php artisan storage:link && php artisan migrate --force && apache2-foreground"]