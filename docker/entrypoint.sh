#!/bin/bash
set -e

# Esperar a que la base de datos esté lista (si es necesario)
if [ ! -z "$MYSQLHOST" ] || [ ! -z "$MYSQL_HOST" ]; then
    echo "Esperando a que la base de datos esté lista..."
    # Usar php para verificar la conexión en lugar de nc
    for i in {1..30}; do
        if php -r "
            try {
                \$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
                \$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';
                \$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
                \$pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD');
                \$pdo = new PDO('mysql:host=' . \$host . ';port=' . \$port, \$user, \$pass);
                echo 'Base de datos lista!' . PHP_EOL;
                exit(0);
            } catch (Exception \$e) {
                exit(1);
            }
        "; then
            break
        fi
        echo "Intento $i/30 - Esperando base de datos..."
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

# Verificar si APP_KEY está configurado
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY no está configurado, generando nueva clave..."
    php artisan key:generate --force
else
    echo "Usando APP_KEY de variables de entorno..."
    # Actualizar el archivo .env con la clave de la variable de entorno
    sed -i "s/APP_KEY=.*/APP_KEY=$APP_KEY/" .env
fi

# Actualizar variables de entorno de la base de datos si están disponibles
if [ ! -z "$MYSQLHOST" ]; then
    echo "Configurando variables de base de datos de Railway..."
    sed -i "s/DB_HOST=.*/DB_HOST=$MYSQLHOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=${MYSQLPORT:-3306}/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$MYSQLDATABASE/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$MYSQLUSER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$MYSQLPASSWORD/" .env
elif [ ! -z "$MYSQL_HOST" ]; then
    echo "Configurando variables de base de datos de Railway (formato alternativo)..."
    sed -i "s/DB_HOST=.*/DB_HOST=$MYSQL_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=${MYSQL_PORT:-3306}/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$MYSQL_DATABASE/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$MYSQL_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$MYSQL_PASSWORD/" .env
fi

# Actualizar APP_URL si está disponible
if [ ! -z "$RAILWAY_PUBLIC_DOMAIN" ]; then
    echo "Configurando APP_URL de Railway..."
    sed -i "s|APP_URL=.*|APP_URL=https://$RAILWAY_PUBLIC_DOMAIN|" .env
fi

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar cache (solo en producción)
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Iniciar Apache
exec apache2-foreground
