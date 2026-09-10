FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql gd mbstring zip intl opcache exif \
    && a2enmod rewrite expires headers \
    && rm -rf /var/lib/apt/lists/*
COPY bitrix.ini /usr/local/etc/php/conf.d/bitrix.ini
COPY site.conf /etc/apache2/sites-available/000-default.conf
