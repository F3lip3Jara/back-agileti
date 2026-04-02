# PHP 8.3 es el estándar actual para Laravel 12
FROM php:8.3-fpm

# Dependencias necesarias para Laravel y extensiones
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git unzip nginx && \
    docker-php-ext-install pdo_mysql gd

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Configurar permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Puerto que usa Cloud Run
EXPOSE 8080

# Comando para iniciar la aplicación
CMD php artisan serve --host=0.0.0.0 --port=8080