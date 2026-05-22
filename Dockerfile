FROM php:8.2-apache

# Cài đặt các thư viện hệ thống
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl

# Cài đặt PHP extensions cho Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy toàn bộ code vào container
COPY . /var/www/html

# Cấu hình lại thư mục gốc của Apache vào /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Cấp quyền cho storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80