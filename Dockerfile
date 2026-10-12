FROM php:8.4-apache

RUN apt-get update && apt-get install -y git zip unzip libzip-dev nodejs npm libicu-dev && docker-php-ext-configure intl && docker-php-ext-install zip pdo pdo_mysql intl

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

COPY . /var/www/html
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD php artisan config:clear; php artisan package:discover --ansi; php artisan migrate --force; apache2-foreground