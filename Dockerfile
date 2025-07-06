FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

# Enable Apache's rewrite module for URL rewriting
RUN a2enmod rewrite

COPY . /var/www/html/

# Set appropriate ownership and permissions for storage and logs directories
# This ensures the web server (www-data user) can write to these directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/logs \
    && chmod -R 775 /var/www/html/storage /var/www/html/logs

EXPOSE 80
