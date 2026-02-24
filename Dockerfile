FROM php:8.2-fpm-alpine

# Install system deps
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libcurl \
    libpq \
    libzip-dev \
    icu-dev \
    oniguruma-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache intl zip bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

# Install dependencies (composer.lock があれば利用、なければ解決してインストール)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
