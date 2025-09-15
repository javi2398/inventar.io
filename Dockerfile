# Imagen base PHP + Apache
FROM php:8.2-apache

# ---- Sistema y extensiones PHP necesarias ----
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# ---- Node.js 18 (para build de Vite) ----
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs

# ---- Apache: mod_rewrite ----
RUN a2enmod rewrite

# ---- Directorio de trabajo ----
WORKDIR /var/www/html

# ---- Config de Apache ----
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# ---- Composer (desde imagen oficial) ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Dependencias PHP con caché de capas ----
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# ---- Dependencias Node con caché de capas ----
COPY package*.json ./
RUN npm ci

# ---- Copiar el resto del código ----
COPY . .

# ---- Build de assets y optimización ----
RUN npm run build \
 && npm prune --production \
 && composer dump-autoload -o

# ---- Permisos (Laravel) ----
RUN chown -R www-data:www-data /var/www/html \
 && chgrp -R www-data storage bootstrap/cache \
 && chmod -R ug+rwx storage bootstrap/cache

# ---- Entrypoint ----
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer 80 (Apache). Railway reescribirá al $PORT en runtime.
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
