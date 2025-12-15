# Guía de Instalación y Configuración - GEVOPI

Esta guía proporciona instrucciones paso a paso para instalar y configurar el proyecto GEVOPI utilizando Docker.

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Docker** (versión 20.10 o superior)
- **Docker Compose** (versión 2.0 o superior)
- **Git** (para clonar el repositorio)
- Al menos **2GB de RAM** disponible para los contenedores
- Puerto **8085** disponible para Laravel Reverb (WebSockets)

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd Crud_No_Transaccional
```

### 2. Configurar Variables de Entorno

El proyecto incluye dos archivos de configuración de entorno:
- `.env` - Para desarrollo local
- Variables en `docker-compose.yml` - Para producción

#### Para Desarrollo Local:

Copia el archivo `.env.example` (si existe) o usa el `.env` proporcionado:

```bash
cp .env.example .env
```

#### Variables Importantes a Verificar/Configurar:

**Base de Datos:**
```env
DB_CONNECTION=pgsql
DB_HOST=gevopi-db
DB_PORT=5432
DB_DATABASE=gevopi_db
DB_USERNAME=admin
DB_PASSWORD=admin123
```

**Correo Electrónico (Gmail):**
> [!WARNING]
> Necesitas configurar una contraseña de aplicación de Gmail. No uses tu contraseña personal.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
```

**Laravel Reverb (WebSockets):**
```env
REVERB_APP_ID=885889
REVERB_APP_KEY=ljtplxexpbq7atbjzrzp
REVERB_APP_SECRET=yy9plhe0c7ffjltaidfh
REVERB_HOST=192.168.0.4  # Cambia por tu IP local o dominio
REVERB_PORT=8080
REVERB_SCHEME=http
```

**Google Gemini API:**
```env
GOOGLE_GEMINI_API_KEY_CURSOS=tu_api_key
GOOGLE_GEMINI_API_KEY_NECESIDADES=tu_api_key
```

**API Helpdesk:**
```env
HELPDESK_API_URL=https://proyecto-de-ultimo-minuto.online
HELPDESK_API_KEY=tu_api_key_aqui
```

### 3. Crear Redes Docker Externas

El proyecto requiere dos redes externas. Créalas antes de iniciar los contenedores:

```bash
docker network create internal-network
docker network create proxy-network
```

### 4. Verificar el Dockerfile

Asegúrate de tener un `Dockerfile` en la raíz del proyecto. Si no existe, créalo con el siguiente contenido básico:

```dockerfile
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . /var/www

# Permisos
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer puerto 9000
EXPOSE 9000

CMD ["php-fpm"]
```

### 5. Crear el Archivo nginx.conf

Crea el archivo `nginx.conf` en la raíz del proyecto:

```nginx
server {
    listen 80;
    index index.php index.html;
    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass laravel:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 6. Construir e Iniciar los Contenedores

```bash
# Construir las imágenes
docker-compose build

# Iniciar todos los servicios
docker-compose up -d
```

### 7. Instalar Dependencias de Laravel

```bash
# Acceder al contenedor de Laravel
docker exec -it gevopi-laravel bash

# Dentro del contenedor:
composer install

# Si usas Node.js/NPM para assets
npm install
npm run build

# Salir del contenedor
exit
```

### 8. Generar Clave de Aplicación

```bash
docker exec -it gevopi-laravel php artisan key:generate
```

### 9. Ejecutar Migraciones

```bash
# Ejecutar migraciones
docker exec -it gevopi-laravel php artisan migrate

# Si tienes seeders (opcional)
docker exec -it gevopi-laravel php artisan db:seed
```

### 10. Configurar Permisos (si es necesario)

```bash
docker exec -it gevopi-laravel bash
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
exit
```

### 11. Limpiar Cache

```bash
docker exec -it gevopi-laravel php artisan config:clear
docker exec -it gevopi-laravel php artisan cache:clear
docker exec -it gevopi-laravel php artisan view:clear
docker exec -it gevopi-laravel php artisan route:clear
```

## ✅ Verificación de la Instalación

### 1. Verificar que los Contenedores Estén Corriendo

```bash
docker-compose ps
```

Deberías ver:
- `gevopi-laravel` - corriendo
- `gevopi` (nginx) - corriendo
- `gevopi-reverb` - corriendo
- `gevopi-db` - corriendo

### 2. Verificar Logs

```bash
# Logs de Laravel
docker logs gevopi-laravel

# Logs de Nginx
docker logs gevopi

# Logs de Reverb
docker logs gevopi-reverb

# Logs de PostgreSQL
docker logs gevopi-db
```

### 3. Acceder a la Aplicación

Abre tu navegador y ve a:
- **Aplicación principal:** `http://localhost` (si usas proxy-network)
- **WebSockets (Reverb):** `http://localhost:8085`

### 4. Verificar Conexión a la Base de Datos

```bash
# Conectarse a PostgreSQL
docker exec -it gevopi-db psql -U admin -d gevopi_db

# Listar tablas
\dt

# Salir
\q
```

## 🔧 Comandos Útiles

### Detener los Contenedores

```bash
docker-compose down
```

### Reiniciar los Servicios

```bash
docker-compose restart
```

### Ver Logs en Tiempo Real

```bash
docker-compose logs -f
```

### Reconstruir Contenedores (después de cambios)

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Acceder a la Base de Datos

```bash
docker exec -it gevopi-db psql -U admin -d gevopi_db
```

### Ejecutar Comandos Artisan

```bash
docker exec -it gevopi-laravel php artisan <comando>
```

## 🐛 Solución de Problemas Comunes

### Error: "Networks not found"

```bash
docker network create internal-network
docker network create proxy-network
```

### Error: "Port already in use"

Verifica qué proceso está usando el puerto:

```bash
# Windows PowerShell
netstat -ano | findstr :8085

# Detén el proceso o cambia el puerto en docker-compose.yml
```

### Error: "Permission denied" en storage

```bash
docker exec -it gevopi-laravel bash
chmod -R 777 storage bootstrap/cache
exit
```

### La aplicación no carga

1. Verifica los logs: `docker logs gevopi-laravel`
2. Asegúrate de que todos los contenedores estén corriendo: `docker-compose ps`
3. Verifica la configuración de nginx: `docker exec -it gevopi nginx -t`

### Problemas con Reverb/WebSockets

1. Verifica que el puerto 8085 esté disponible
2. Revisa los logs: `docker logs gevopi-reverb`
3. Verifica la configuración en `.env`:
   - `REVERB_HOST` debe ser accesible desde el navegador
   - `REVERB_PORT` debe coincidir con el puerto mapeado (8085)

## 📝 Notas Adicionales

### Gmail SMTP

Para usar Gmail como servidor SMTP:

1. Habilita la verificación en 2 pasos en tu cuenta de Google
2. Genera una "Contraseña de aplicación" en: https://myaccount.google.com/apppasswords
3. Usa esa contraseña en `MAIL_PASSWORD`

### Producción vs Desarrollo

El `docker-compose.yml` está configurado para **producción** con:
- `APP_ENV=production`
- `APP_DEBUG=false`

Para desarrollo, modifica estas variables en el servicio `laravel`:
```yaml
environment:
  APP_ENV: local
  APP_DEBUG: true
```

### API Keys de Google Gemini

1. Obtén tus API keys desde: https://makersuite.google.com/app/apikey
2. Configúralas en `.env`:
   - `GOOGLE_GEMINI_API_KEY_CURSOS`
   - `GOOGLE_GEMINI_API_KEY_NECESIDADES`

### Proxy Reverso (Nginx Proxy Manager / Traefik)

Si usas un proxy reverso en `proxy-network`, asegúrate de:
1. Configurar el dominio `gevopi.dasalas.shop`
2. Enrutar el tráfico al contenedor `gevopi` en el puerto 80
3. Configurar SSL/TLS si es necesario

## 🆘 Soporte

Si encuentras problemas:
1. Revisa los logs de los contenedores
2. Verifica que todas las variables de entorno estén configuradas
3. Asegúrate de que los puertos no estén en uso
4. Consulta la documentación de Laravel: https://laravel.com/docs

---

**Última actualización:** Diciembre 2024
