# Sử dụng PHP 8.2 có sẵn Apache
FROM php:8.2-apache

# 1. Cài đặt các công cụ hệ thống và Python 3
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    python3 python3-pip python3-venv

# BỔ SUNG: Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Cài đặt PHP extensions cho Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Cài đặt các thư viện Python cho AI
RUN pip3 install --no-cache-dir fastapi uvicorn numpy scikit-learn requests --break-system-packages

# 4. Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# 5. Copy toàn bộ mã nguồn vào Container
COPY . /var/www/html

# BỔ SUNG: Chạy lệnh cài đặt thư viện PHP
RUN composer install --no-dev --optimize-autoloader

# 6. Cấu hình Apache trỏ vào thư mục public của Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 7. Cấp quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Lệnh khởi chạy song song cả 2 Service
CMD uvicorn ai-service.main:app --host 0.0.0.0 --port 8000 & apache2-foreground

EXPOSE 80