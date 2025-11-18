FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev

# Extensiones PHP requeridas por Laravel
RUN docker-php-ext-install pdo_mysql mbstring zip bcmath

# Instalar Composer dentro del contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivo composer
COPY composer.json composer.lock ./

# Instalar dependencias sin ejecutar scripts
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# Copiar todo el proyecto
COPY . .

# Ejecutar scripts de composer YA con artisan presente
RUN composer run-script post-autoload-dump || true

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]

EXPOSE 9000
