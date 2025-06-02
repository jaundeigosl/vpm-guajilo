# Usa una imagen ligera de PHP con FPM
FROM php:8.2-fpm-alpine

# Instalar extensiones necesarias
RUN apk add --no-cache \
    nginx \
    mysql-client \
    bash \
    libzip-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli gd zip

# Configurar PHP para manejar archivos grandes - Usando un archivo ini personalizado
COPY ./uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la app
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto de PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]