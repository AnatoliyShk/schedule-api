FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev \
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
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Apache config
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

# Fix MPM
RUN a2dismod mpm_event mpm_worker mpm_prefork ; \
    a2enmod mpm_prefork && \
    a2enmod rewrite

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Bind to Railway's dynamic PORT
CMD bash -c "sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground"
