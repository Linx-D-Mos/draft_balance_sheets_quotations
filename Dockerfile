FROM php:8.4-apache

# 1. Instalar dependencias del sistema y herramientas requeridas
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    git \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_pgsql pgsql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Desactivar MPMs conflictivos, asegurar mpm_prefork (requerido por PHP) y habilitar mod_rewrite
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# 3. Configurar el DocumentRoot de Apache apuntando a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Copiar Composer desde la imagen oficial
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 5. Establecer directorio de trabajo y copiar el proyecto
WORKDIR /var/www/html
COPY . .

# 6. Instalar dependencias de Composer sin ejecutar scripts de Laravel aún
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 7. Asignar los permisos correctos a las carpetas de almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto por defecto que usa Apache
EXPOSE 80

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
