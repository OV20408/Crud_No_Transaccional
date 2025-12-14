FROM php:8.4-fpm
 
# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip pcntl
 
# 2. Instalar Node & NPM (última LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs
 
# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
WORKDIR /var/www
 
COPY . .
 
# 4. Copiar entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
 
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]