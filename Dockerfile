FROM php:8.2-fpm

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instala utilitários (opcional)
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html 