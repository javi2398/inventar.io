# 🧾 Inventar.io

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
| **Base de datos** | MySQL | Gestión relacional de datos |
| **Almacenamiento** | Cloudinary | Gestión y optimización de imágenes |
| **Control de versiones** | Git + GitHub | Flujo de desarrollo colaborativo |
| **Despliegue** | Laravel Cloud | Hosting y CI/CD |

---

## Instalación del proyecto

### Backend (Laravel)

composer install  
cp .env.example .env  
php artisan key:generate  

### Edita el archivo .env con tus credenciales de MySQL

DB_CONNECTION=mysql  
DB_HOST=127.0.0.1  
DB_PORT=3306  
DB_DATABASE=inventario  
DB_USERNAME=root  
DB_PASSWORD=  

### Ejecuta migraciones y seeders

php artisan migrate --seed  
php artisan serve  

### Frontend (React)

npm install  
npm run dev  
