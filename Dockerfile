FROM php:8.2-fpm

# Cài đặt dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev

# Cài đặt extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài đặt Node.js và npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục ứng dụng
WORKDIR /var/www/html

# Sao chép mã nguồn
COPY . /var/www/html

# Cài đặt dependencies của ứng dụng
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# Thiết lập quyền
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 (FPM)
EXPOSE 9000

CMD ["php-fpm"]