FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=10000

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
    /etc/apache2/sites-enabled/000-default.conf

# Crear script de inicio
RUN echo '#!/bin/bash\n\
echo "Esperando a que la base de datos esté lista..."\n\
max_tries=30\n\
count=0\n\
until php artisan migrate:status > /dev/null 2>&1 || [ $count -eq $max_tries ]; do\n\
  echo "Intento $((count+1))/$max_tries..."\n\
  sleep 2\n\
  count=$((count+1))\n\
done\n\
\n\
if [ $count -eq $max_tries ]; then\n\
  echo "ERROR: No se pudo conectar a la base de datos"\n\
  exit 1\n\
fi\n\
\n\
echo "Ejecutando migraciones..."\n\
php artisan migrate --force\n\
\n\
echo "Iniciando Apache..."\n\
apache2-foreground\n' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
