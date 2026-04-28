# ── Stage 1: Build frontend assets ───────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

# Temporary .env so Vite can read VITE_ vars at build time
RUN echo "VITE_APP_NAME=PTE Portal" > .env

RUN npm run build

# ── Stage 2: PHP application ──────────────────────────────────────────────────
FROM php:8.2-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        gd \
        zip \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files and install dependencies (no dev)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source
COPY . .

# Copy built frontend assets from node-builder stage
COPY --from=node-builder /app/public/build public/build

# Generate optimised autoloader — skip scripts (package:discover runs at runtime)
RUN composer dump-autoload --optimize --no-scripts

# Copy config files
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisord.conf

# Storage and cache permissions
RUN mkdir -p storage/logs storage/framework/{cache/data,sessions,views} \
             storage/app/{public,speaking-responses} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
