FROM php:8.2-cli

# Dependencias necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    unzip \
    git

# Extensiones de PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
