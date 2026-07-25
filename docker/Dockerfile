FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./php/php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www/html
