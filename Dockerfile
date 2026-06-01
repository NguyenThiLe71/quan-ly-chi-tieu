FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    python3 python3-pip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN pip3 install --no-cache-dir fastapi uvicorn numpy scikit-learn requests --break-system-packages

RUN a2enmod rewrite

COPY . /var/www/html

# Cài đặt thư viện PHP
RUN composer install --no-dev --optimize-autoloader

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Cấp quyền và chạy cache cấu hình để nhận thiết lập mới nhất
# Thay đoạn RUN cache cũ bằng đoạn này
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
    cd /var/www/html && \
    rm -f bootstrap/cache/config.php bootstrap/cache/services.php bootstrap/cache/packages.php && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# CMD chạy service
CMD bash -c "uvicorn ai-service.main:app --host 0.0.0.0 --port 8000 & apache2-foreground"

EXPOSE 80