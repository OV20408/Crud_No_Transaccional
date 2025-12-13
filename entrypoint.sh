#!/bin/bash

# Salir si algún comando falla
set -e

# Crear .env si no existe
if [ ! -f .env ]; then
    echo "📄 No existe .env — creando desde .env.example"
    cp .env.example .env
else
    echo "✔️ Archivo .env ya existe — no se copia"
fi

echo "📦 Instalando dependencias de Composer..."
composer install --no-interaction --prefer-dist

echo "🔑 Generando APP_KEY (si no existe)..."
php artisan key:generate --force || true

echo "⚙️ Aplicando permisos..."
chmod -R 777 storage bootstrap/cache

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || true

echo "🌱 Ejecutando Seeder..."
php artisan db:seed --force || true

if [ ! -f public/build/manifest.json ]; then
    echo "🎨 Compilando Vite (npm run build)..."

    if command -v npm >/dev/null 2>&1; then
        npm install
        npm run build
    else
        echo "❌ npm NO está instalado dentro del contenedor."
        echo "➡ Debes compilar Vite en tu host y copiar public/build"
    fi
else
    echo "✔️ Vite build ya existe — no se compila"
fi

echo "🚀 Iniciando PHP-FPM..."
exec php-fpm