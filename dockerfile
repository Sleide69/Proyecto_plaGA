FROM php:8.3-fpm

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia solo composer.json y composer.lock primero
COPY composer.json composer.lock ./

# Instala dependencias
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copia el resto del proyecto
COPY . .

# Instala dependencias de PHP y JS
RUN composer install
RUN php artisan storage:link || true
RUN npm install && npm run build

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]