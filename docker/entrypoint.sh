#!/bin/bash
set -e

# Limpiar caches por si vienen precacheadas
php artisan config:clear || true
php artisan cache:clear || true

# Descubrir credenciales desde DB_* (fallback MYSQL_*)
DBH="${DB_HOST:-${MYSQLHOST:-${MYSQL_HOST}}}"
DBP="${DB_PORT:-${MYSQLPORT:-${MYSQL_PORT:-3306}}}"
DBU="${DB_USERNAME:-${MYSQLUSER:-${MYSQL_USER}}}"
DBW="${DB_PASSWORD:-${MYSQLPASSWORD:-${MYSQL_PASSWORD}}}"

# Esperar DB si hay host/usuario
if [ -n "$DBH" ] && [ -n "$DBU" ]; then
  echo "Esperando a que la base de datos esté lista en ${DBH}:${DBP} ..."
  for i in $(seq 1 60); do
    if php -r "try{new PDO('mysql:host=${DBH};port=${DBP}','${DBU}','${DBW}'); exit(0);}catch(Exception \$e){exit(1);}"; then
      echo "Base de datos lista!"
      break
    fi
    echo "Intento $i/60 - Esperando base de datos..."
    sleep 2
  done
fi

# APP_KEY
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY no está configurado; generando..."
  php artisan key:generate --force
else
  echo "Usando APP_KEY de variables de entorno..."
fi

# Migraciones (no bloquees el healthcheck si tarda)
php artisan migrate --force || true &

# Calentar caches (que no rompa si algo falta)
php artisan view:cache || true
php artisan route:cache || true
php artisan config:cache || true

# Apache debe escuchar en $PORT para pasar el healthcheck de Railway
if [ -n "$PORT" ]; then
  sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
  sed -ri "s/\*:80/*:${PORT}/" /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground
