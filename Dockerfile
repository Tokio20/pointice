# Imagen base con PHP y Apache
FROM php:8.2-apache

# Variables de entorno
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Actualizar y dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Ajustar DocumentRoot a /public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . /var/www/html
WORKDIR /var/www/html

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias JS y compilar (si usas Vite/Tailwind)
RUN npm install && npm run build

# Permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Puerto que usa Render
EXPOSE 10000
ENV PORT=10000

# Cambiar Apache a puerto 10000
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
    /etc/apache2/sites-enabled/000-default.conf

# Comando de inicio

CMD ["apache2-foreground"]

#forzar a migrar la base de datos 

RUN php artisan migrate --force

