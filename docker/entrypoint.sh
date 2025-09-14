#!/bin/bash
set -e

# Esperar a que la base de datos esté lista (si es necesario)
if [ ! -z "$DB_HOST" ]; then
    echo "Esperando a que la base de datos esté lista..."
    # Usar php para verificar la conexión en lugar de nc
    until php -r "
        try {
            \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
            echo 'Base de datos lista!' . PHP_EOL;
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    "; do
        sleep 2
    done
fi

# Generar clave de aplicación si no existe
if [ ! -f .env ]; then
    echo "Creando archivo .env..."
    if [ -f .env.example ]; then
        cp .env.example .env
    elif [ -f env.example ]; then
        cp env.example .env
    else
        echo "No se encontró archivo de ejemplo de .env"
    fi
fi

# Generar clave de aplicación
php artisan key:generate --force

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Apache
exec apache2-foreground
