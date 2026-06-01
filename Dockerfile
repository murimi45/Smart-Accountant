# ----------------------------
# 1️⃣ Base image
# ----------------------------
FROM php:8.2-fpm

# ----------------------------
# 2️⃣ System dependencies
# ----------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# ----------------------------
# 3️⃣ Install Composer
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----------------------------
# 4️⃣ Set working directory
# ----------------------------
WORKDIR /var/www/html

# ----------------------------
# 5️⃣ Copy application
# ----------------------------
COPY . .

# ----------------------------
# 6️⃣ Install PHP dependencies
# ----------------------------
RUN composer install --no-dev --optimize-autoloader

# ----------------------------
# 7️⃣ Supervisor configs
# ----------------------------
COPY docker/supervisor/*.conf /etc/supervisor/conf.d/

# ----------------------------
# 8️⃣ Permissions
# ----------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# ----------------------------
# 9️⃣ Expose PHP-FPM port
# ----------------------------
EXPOSE 9000

# ----------------------------
# 🔟 Supervisor is the engine
# ----------------------------
CMD ["supervisord", "-n"]
