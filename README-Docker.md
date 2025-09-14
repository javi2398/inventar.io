# Docker Setup para Inventar.io

Este proyecto ahora está configurado para funcionar con Docker y puede ser desplegado en Railway.

## Archivos Docker Creados

- `Dockerfile` - Configuración principal del contenedor
- `docker-compose.yml` - Configuración para desarrollo local
- `.dockerignore` - Archivos a ignorar en el build
- `railway.json` - Configuración específica para Railway
- `env.railway.example` - Variables de entorno para Railway

## Desarrollo Local

Para ejecutar el proyecto localmente con Docker:

```bash
# Construir y ejecutar los contenedores
docker-compose up -d

# Ver logs
docker-compose logs -f

# Ejecutar comandos de Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Parar los contenedores
docker-compose down
```

El proyecto estará disponible en:
- Aplicación: http://localhost:8000
- phpMyAdmin: http://localhost:8080

## Despliegue en Railway

### 1. Preparar el repositorio

```bash
# Asegúrate de que todos los archivos estén committeados
git add .
git commit -m "Add Docker configuration for Railway deployment"
git push origin main
```

### 2. Configurar Railway

1. Ve a [Railway.app](https://railway.app)
2. Conecta tu repositorio de GitHub
3. Selecciona este proyecto
4. Railway detectará automáticamente el `Dockerfile`

### 3. Variables de Entorno en Railway

Configura las siguientes variables de entorno en Railway:

#### Variables Obligatorias:
- `APP_KEY` - Genera con: `php artisan key:generate --show`
- `APP_URL` - URL de tu aplicación en Railway (ej: https://tu-app.railway.app)
- `APP_ENV=production`
- `APP_DEBUG=false`

#### Variables de Base de Datos (Railway las proporciona automáticamente):
- `DB_CONNECTION=mysql`
- `DB_HOST` - Railway lo proporcionará automáticamente
- `DB_PORT=3306`
- `DB_DATABASE` - Railway lo proporcionará automáticamente
- `DB_USERNAME` - Railway lo proporcionará automáticamente
- `DB_PASSWORD` - Railway lo proporcionará automáticamente

#### Variables Opcionales:
- `MAIL_MAILER=smtp`
- `MAIL_HOST` - Tu servidor SMTP
- `MAIL_PORT=587`
- `MAIL_USERNAME` - Tu email
- `MAIL_PASSWORD` - Tu contraseña de email
- `MAIL_FROM_ADDRESS` - Email de envío
- `MAIL_FROM_NAME` - Nombre de la aplicación

### 4. Base de Datos

1. En Railway, añade un servicio MySQL
2. Las variables de entorno de la base de datos se configurarán automáticamente
3. Las migraciones se ejecutarán automáticamente durante el despliegue

### 5. Despliegue

Una vez configurado, Railway desplegará automáticamente tu aplicación cada vez que hagas push a la rama principal.

### 6. Verificar el Despliegue

Después del despliegue:
1. Verifica que la aplicación esté funcionando en la URL proporcionada
2. Revisa los logs en Railway para asegurar que no hay errores
3. Verifica que las migraciones se ejecutaron correctamente

## Comandos Útiles

```bash
# Construir la imagen localmente
docker build -t inventar-app .

# Ejecutar el contenedor localmente
docker run -p 8000:80 inventar-app

# Ver logs del contenedor
docker logs <container_id>

# Acceder al contenedor
docker exec -it <container_id> bash
```

## Solución de Problemas

### Error de permisos
```bash
# En el contenedor, ejecutar:
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache
```

### Error de base de datos
- Verifica que las variables de entorno de la base de datos estén configuradas correctamente
- Asegúrate de que la base de datos esté disponible antes de que la aplicación inicie

### Error de assets
- Los assets se construyen durante el build del Docker
- Si hay problemas, verifica que `npm run build` funcione localmente
