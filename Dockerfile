# Etapa 1: Compilar assets con Node
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Imagen base de PHP para producción
FROM php:8.4-apache

# 1. Instalar dependencias del sistema, SQLite y herramientas requeridas
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    libsqlite3-dev \
    sqlite3 \
    git \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_pgsql pgsql pdo_sqlite bcmath \
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

# 5. Establecer directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar el proyecto
COPY . .

# 7. Copiar los assets compilados en la Etapa 1 (Vite)
COPY --from=assets-builder /app/public/build /var/www/html/public/build

# 8. Instalar dependencias de Composer optimizadas para producción
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --optimize-autoloader --no-scripts --no-dev

# 9. Configurar valores por defecto para SQLite y puertos de Railway
ENV PORT=80
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=database/database.sqlite

# 10. Crear base de datos sqlite inicial y asignar los permisos correctos a carpetas críticas
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 11. Copiar y configurar el script de entrada (Entrypoint)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Indicar el puerto de escucha por defecto (Railway lo puede mapear dinámicamente)
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

