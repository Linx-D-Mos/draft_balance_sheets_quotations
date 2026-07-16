#!/bin/sh
set -e

# 1. Configurar el puerto de Apache dinámicamente según la variable $PORT de Railway
PORT="${PORT:-80}"
echo "Configurando Apache para escuchar en el puerto $PORT..."
sed -i "s/Listen [0-9]*/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf

# 2. Asegurar que la base de datos de SQLite exista y tenga los permisos correctos
DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"

if [ "$DB_PATH" != ":memory:" ]; then
    # Hacer la ruta absoluta si es relativa (no empieza con /)
    if [ "${DB_PATH#/*}" = "$DB_PATH" ]; then
        DB_PATH="/var/www/html/$DB_PATH"
    fi

    # Crear el directorio base si no existe
    DB_DIR=$(dirname "$DB_PATH")
    if [ ! -d "$DB_DIR" ]; then
        mkdir -p "$DB_DIR"
        echo "Directorio de base de datos creado en $DB_DIR"
    fi
    
    # Crear el archivo si no existe
    if [ ! -f "$DB_PATH" ]; then
        touch "$DB_PATH"
        echo "Base de datos SQLite creada en $DB_PATH"
    fi
    
    # Asegurar permisos correctos para que www-data pueda escribir en el archivo y el directorio
    chown -R www-data:www-data "$DB_DIR"
    chmod -R 775 "$DB_DIR"
    chmod 664 "$DB_PATH"
fi

# 3. Cachear la configuración de Laravel en producción para mayor rendimiento
echo "Optimizando la configuración de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Ejecutar migraciones y seeders automáticamente
echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --force

# 5. Ejecutar el comando por defecto (generalmente apache2-foreground)
exec "$@"
