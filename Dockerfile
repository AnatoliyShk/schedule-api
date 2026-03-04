FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libpng-dev libonig-dev \
    libxml2-dev libzip-dev libpq-dev netstat-nat && \
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

# Remove default nginx config
RUN rm -f /etc/nginx/sites-enabled/default && \
    rm -f /etc/nginx/conf.d/default.conf

# Write nginx config
RUN echo 'server { \n\
    listen 8080; \n\
    root /var/www/html/public; \n\
    index index.php index.html; \n\
    client_max_body_size 100M; \n\
    location / { \n\
        try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_index index.php; \n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
        include fastcgi_params; \n\
    } \n\
    location ~ /\.ht { \n\
        deny all; \n\
    } \n\
}' > /etc/nginx/conf.d/laravel.conf

EXPOSE 8080

CMD bash -c "php-fpm -D && nginx -g 'daemon off;'"
