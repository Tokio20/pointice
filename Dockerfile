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
    nodejs \
    npm \
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

EXPOSE 10000

CMD php artisan migrate --force && apache2-foreground
