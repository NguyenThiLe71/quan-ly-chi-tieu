# Sử dụng PHP 8.2 có sẵn Apache
FROM php:8.2-apache

# 1. Cài đặt các công cụ hệ thống và Python 3
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    python3 python3-pip python3-venv

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Cài đặt PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Cài đặt Python AI
RUN pip3 install --no-cache-dir fastapi uvicorn numpy scikit-learn requests --break-system-packages

# 4. Bật mod_rewrite
RUN a2enmod rewrite

# 5. Copy mã nguồn
COPY . /var/www/html

# 6. Cài đặt thư viện PHP
RUN composer install --no-dev --optimize-autoloader

# 7. Cấu hình Apache trỏ vào public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 8. Cấp quyền và cấu hình Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# BỔ SUNG: Dọn dẹp cache để tránh lỗi đường dẫn cũ từ máy tính cá nhân
RUN php /var/www/html/artisan config:clear
RUN php /var/www/html/artisan cache:clear
RUN php /var/www/html/artisan view:clear

# 9. Lệnh khởi chạy
# Lưu ý: Nếu web vẫn lỗi 500, hãy đảm bảo lệnh chạy apache là nền tảng chính
# Sửa dòng CMD cuối cùng thành:
CMD cd ai-service && uvicorn main:app --host 0.0.0.0 --port 8000 & apache2-foreground

EXPOSE 80