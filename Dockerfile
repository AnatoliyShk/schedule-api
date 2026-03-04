FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libpng-dev libonig-dev \
    libxml2-dev libzip-dev libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD bash -c "sed -i 's/PORT/${PORT}/g' /etc/nginx/nginx.conf && php-fpm -D && nginx -g 'daemon off;'"
