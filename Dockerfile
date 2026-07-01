# Dockerfile for Praveen Kumar K Portfolio (Coolify / Self-Hosted)
FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite sqlite3 \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage

EXPOSE 80
