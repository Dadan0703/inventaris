FROM php:8.4-cli

RUN apt-get update && apt-get install -y git zip unzip libzip-dev nodejs npm libicu-dev && docker-php-ext-configure intl && docker-php-ext-install zip pdo pdo_mysql intl

WORKDIR /var/www/html
COPY . .
RUN rm -f .env

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN npm install && npm run build
RUN chown -R www-data:www-data storage bootstrap/cache

CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"