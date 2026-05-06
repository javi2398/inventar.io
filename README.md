# 🧾 Inventar.io

[![Tests](https://github.com/javi2398/inventar.io/actions/workflows/tests.yml/badge.svg)](https://github.com/javi2398/inventar.io/actions/workflows/tests.yml)

**Inventar.io** es una aplicación web de **gestión de inventario** orientada a **autónomos, mayoristas y pequeños negocios**.  
Permite registrar compras, ventas, stock y gastos de forma sencilla, centralizando toda la información comercial en una sola plataforma accesible desde cualquier dispositivo.

---

## 🚀 Características principales

- 📦 Gestión completa de inventario: productos, compras, ventas y gastos.  
- 📊 Dashboard con estadísticas y control de stock en tiempo real.  
- 👥 Sistema de autenticación y usuarios protegidos (Laravel Auth Middleware).  
- 🖼️ Subida y gestión de imágenes con **Cloudinary**.  
- 🔍 Búsqueda, filtrado y paginación de productos.  
- 💻 Interfaz SPA moderna desarrollada con **React + Tailwind CSS**.  
- ☁️ Despliegue en **Laravel Cloud** (entorno de producción).  

---

## 🧩 Stack tecnológico

| Capa | Tecnología | Descripción |
|------|-------------|-------------|
| **Frontend** | React, Tailwind CSS, Vite | Interfaz dinámica y responsiva |
| **Backend** | Laravel (PHP) | API REST y lógica del servidor |
| **Base de datos** | PostgreSQL | Gestión relacional de datos |
| **Almacenamiento** | Cloudinary | Gestión y optimización de imágenes |
| **Control de versiones** | Git + GitHub | Flujo de desarrollo colaborativo |
| **Despliegue** | Laravel Cloud | Hosting y CI/CD |

---

## Instalación del proyecto

## Desarrollo Local

Para ejecutar el proyecto localmente con Docker:  

# Construir y ejecutar los contenedores  
docker-compose up -d  

# Ver logs  
docker-compose logs -f  

# Ejecutar comandos de Laravel  
docker-compose exec app php artisan migrate  
docker-compose exec app php artisan db:seed  

# Parar los contenedores  
docker-compose down  


El proyecto estará disponible en:
- Aplicación: http://localhost:8000
- pgAdmin: http://localhost:8080 (usuario: `admin@inventar.io`, contraseña: `password`)

### Backend (Laravel)

composer install  
cp .env.example .env  
php artisan key:generate  

### Edita el archivo .env con tus credenciales de PostgreSQL

DB_CONNECTION=pgsql  
DB_HOST=127.0.0.1  
DB_PORT=5432  
DB_DATABASE=inventario  
DB_USERNAME=postgres  
DB_PASSWORD=password  

> Si usas Docker (recomendado), estas credenciales coinciden con las del `docker-compose.yml`.  
> Si usas PostgreSQL nativo, ajusta usuario y contraseña a tu instalación.

### Requisito de la extensión PHP

Asegúrate de tener la extensión `pdo_pgsql` habilitada en tu PHP. Comprueba con:

php -m | grep pgsql

### Ejecuta migraciones y seeders

php artisan migrate --seed  
php artisan serve  

### Frontend (React)

npm install  
npm run dev  

---

## 🧪 Tests

La suite de tests usa la base de datos `inventario_testing` del mismo contenedor de Postgres definido en `docker-compose.yml` (se crea automáticamente la primera vez que se inicializa el volumen, vía `docker/postgres-init/init.sql`).

### Ejecutar los tests en local

```bash
# 1. Levantar Postgres si no está arriba
docker compose up -d postgres

# 2. Ejecutar la suite completa
./vendor/bin/phpunit

# 3. Filtrar por nombre de test
./vendor/bin/phpunit --filter ProductoController
```

### CI

Cada `push` a `main` y cada Pull Request dispara el workflow [`tests.yml`](.github/workflows/tests.yml), que levanta un servicio de Postgres 16 y ejecuta toda la suite. El estado se refleja en el badge superior.
