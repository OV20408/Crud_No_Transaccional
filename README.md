Dame este documento estetico para presentar: # Guía de Instalación y Configuración - GEVOPI Esta guía proporciona instrucciones paso a paso para instalar y configurar el proyecto GEVOPI utilizando Docker. ## 📋 Requisitos Previos Antes de comenzar, asegúrate de tener instalado: - \*\*Docker\*\* (versión 20.10 o superior) - \*\*Docker Compose\*\* (versión 2.0 o superior) - \*\*Git\*\* (para clonar el repositorio) - Al menos \*\*2GB de RAM\*\* disponible para los contenedores - Puerto \*\*8085\*\* disponible para Laravel Reverb (WebSockets) ## 🚀 Instalación ### 1. Clonar el Repositorio

bash

git clone <URL\_DEL\_REPOSITORIO>

cd Crud\_No\_Transaccional

\### 2. Configurar Variables de Entorno El proyecto incluye dos archivos de configuración de entorno: - .env - Para desarrollo local - Variables en docker-compose.yml - Para producción #### Para Desarrollo Local: Copia el archivo .env.example (si existe) o usa el .env proporcionado:

bash

cp .env.example .env

\#### Variables Importantes a Verificar/Configurar: \*\*Base de Datos:\*\*

env

DB\_CONNECTION=pgsql

DB\_HOST=gevopi-db

DB\_PORT=5432

DB\_DATABASE=gevopi\_db

DB\_USERNAME=admin

DB\_PASSWORD=admin123

\*\*Correo Electrónico (Gmail):\*\* > \[!WARNING] > Necesitas configurar una contraseña de aplicación de Gmail. No uses tu contraseña personal.

env

MAIL\_MAILER=smtp

MAIL\_HOST=smtp.gmail.com

MAIL\_PORT=587

MAIL\_USERNAME=tu\_email@gmail.com

MAIL\_PASSWORD=tu\_contraseña\_de\_aplicacion

MAIL\_ENCRYPTION=tls

MAIL\_FROM\_ADDRESS=tu\_email@gmail.com

\*\*Laravel Reverb (WebSockets):\*\*

env

REVERB\_APP\_ID=885889

REVERB\_APP\_KEY=ljtplxexpbq7atbjzrzp

REVERB\_APP\_SECRET=yy9plhe0c7ffjltaidfh

REVERB\_HOST=192.168.0.4  # Cambia por tu IP local o dominio

REVERB\_PORT=8080

REVERB\_SCHEME=http

\*\*Google Gemini API:\*\*

env

GOOGLE\_GEMINI\_API\_KEY\_CURSOS=tu\_api\_key

GOOGLE\_GEMINI\_API\_KEY\_NECESIDADES=tu\_api\_key

\*\*API Helpdesk:\*\*

env

HELPDESK\_API\_URL=https://proyecto-de-ultimo-minuto.online

HELPDESK\_API\_KEY=tu\_api\_key\_aqui

\### 3. Crear Redes Docker Externas El proyecto requiere dos redes externas. Créalas antes de iniciar los contenedores:

bash

docker network create internal-network

docker network create proxy-network

\### 4. Verificar el Dockerfile Asegúrate de tener un Dockerfile en la raíz del proyecto. Si no existe, créalo con el siguiente contenido básico:

dockerfile

FROM php:8.2-fpm



\# Instalar dependencias del sistema

RUN apt-get update \&\& apt-get install -y \\

&nbsp;   git \\

&nbsp;   curl \\

&nbsp;   libpng-dev \\

&nbsp;   libonig-dev \\

&nbsp;   libxml2-dev \\

&nbsp;   zip \\

&nbsp;   unzip \\

&nbsp;   libpq-dev \\

&nbsp;   nodejs \\

&nbsp;   npm



\# Limpiar cache

RUN apt-get clean \&\& rm -rf /var/lib/apt/lists/\*



\# Instalar extensiones de PHP

RUN docker-php-ext-install pdo pdo\_pgsql pgsql mbstring exif pcntl bcmath gd



\# Obtener Composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer



\# Configurar directorio de trabajo

WORKDIR /var/www



\# Copiar archivos del proyecto

COPY . /var/www



\# Permisos

RUN chown -R www-data:www-data /var/www

RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache



\# Exponer puerto 9000

EXPOSE 9000



CMD \["php-fpm"]

\### 5. Crear el Archivo nginx.conf Crea el archivo nginx.conf en la raíz del proyecto:

nginx

server {

&nbsp;   listen 80;

&nbsp;   index index.php index.html;

&nbsp;   root /var/www/public;



&nbsp;   location / {

&nbsp;       try\_files $uri $uri/ /index.php?$query\_string;

&nbsp;   }



&nbsp;   location ~ \\.php$ {

&nbsp;       try\_files $uri =404;

&nbsp;       fastcgi\_split\_path\_info ^(.+\\.php)(/.+)$;

&nbsp;       fastcgi\_pass laravel:9000;

&nbsp;       fastcgi\_index index.php;

&nbsp;       include fastcgi\_params;

&nbsp;       fastcgi\_param SCRIPT\_FILENAME $document\_root$fastcgi\_script\_name;

&nbsp;       fastcgi\_param PATH\_INFO $fastcgi\_path\_info;

&nbsp;   }



&nbsp;   location ~ /\\.ht {

&nbsp;       deny all;

&nbsp;   }

}

\### 6. Construir e Iniciar los Contenedores

bash

\# Construir las imágenes

docker-compose build



\# Iniciar todos los servicios

docker-compose up -d

\### 7. Instalar Dependencias de Laravel

bash

\# Acceder al contenedor de Laravel

docker exec -it gevopi-laravel bash



\# Dentro del contenedor:

composer install



\# Si usas Node.js/NPM para assets

npm install

npm run build



\# Salir del contenedor

exit

\### 8. Generar Clave de Aplicación

bash

docker exec -it gevopi-laravel php artisan key:generate

\### 9. Ejecutar Migraciones

bash

\# Ejecutar migraciones

docker exec -it gevopi-laravel php artisan migrate



\# Si tienes seeders (opcional)

docker exec -it gevopi-laravel php artisan db:seed

\### 10. Configurar Permisos (si es necesario)

bash

docker exec -it gevopi-laravel bash

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

chmod -R 775 /var/www/storage /var/www/bootstrap/cache

exit

\### 11. Limpiar Cache

bash

docker exec -it gevopi-laravel php artisan config:clear

docker exec -it gevopi-laravel php artisan cache:clear

docker exec -it gevopi-laravel php artisan view:clear

docker exec -it gevopi-laravel php artisan route:clear

\## ✅ Verificación de la Instalación ### 1. Verificar que los Contenedores Estén Corriendo

bash

docker-compose ps

Deberías ver: - gevopi-laravel - corriendo - gevopi (nginx) - corriendo - gevopi-reverb - corriendo - gevopi-db - corriendo ### 2. Verificar Logs

bash

\# Logs de Laravel

docker logs gevopi-laravel



\# Logs de Nginx

docker logs gevopi



\# Logs de Reverb

docker logs gevopi-reverb



\# Logs de PostgreSQL

docker logs gevopi-db

\### 3. Acceder a la Aplicación Abre tu navegador y ve a: - \*\*Aplicación principal:\*\* http://localhost (si usas proxy-network) - \*\*WebSockets (Reverb):\*\* http://localhost:8085 ### 4. Verificar Conexión a la Base de Datos

bash

\# Conectarse a PostgreSQL

docker exec -it gevopi-db psql -U admin -d gevopi\_db



\# Listar tablas

\\dt



\# Salir

\\q

\## 🔧 Comandos Útiles ### Detener los Contenedores

bash

docker-compose down

\### Reiniciar los Servicios

bash

docker-compose restart

\### Ver Logs en Tiempo Real

bash

docker-compose logs -f

\### Reconstruir Contenedores (después de cambios)

bash

docker-compose down

docker-compose build --no-cache

docker-compose up -d

\### Acceder a la Base de Datos

bash

docker exec -it gevopi-db psql -U admin -d gevopi\_db

\### Ejecutar Comandos Artisan

bash

docker exec -it gevopi-laravel php artisan <comando>

\## 🐛 Solución de Problemas Comunes ### Error: "Networks not found"

bash

docker network create internal-network

docker network create proxy-network

\### Error: "Port already in use" Verifica qué proceso está usando el puerto:

bash

\# Windows PowerShell

netstat -ano | findstr :8085



\# Detén el proceso o cambia el puerto en docker-compose.yml

\### Error: "Permission denied" en storage

bash

docker exec -it gevopi-laravel bash

chmod -R 777 storage bootstrap/cache

exit

\### La aplicación no carga 1. Verifica los logs: docker logs gevopi-laravel 2. Asegúrate de que todos los contenedores estén corriendo: docker-compose ps 3. Verifica la configuración de nginx: docker exec -it gevopi nginx -t ### Problemas con Reverb/WebSockets 1. Verifica que el puerto 8085 esté disponible 2. Revisa los logs: docker logs gevopi-reverb 3. Verifica la configuración en .env: - REVERB\_HOST debe ser accesible desde el navegador - REVERB\_PORT debe coincidir con el puerto mapeado (8085) ## 📝 Notas Adicionales ### Gmail SMTP Para usar Gmail como servidor SMTP: 1. Habilita la verificación en 2 pasos en tu cuenta de Google 2. Genera una "Contraseña de aplicación" en: https://myaccount.google.com/apppasswords 3. Usa esa contraseña en MAIL\_PASSWORD ### Producción vs Desarrollo El docker-compose.yml está configurado para \*\*producción\*\* con: - APP\_ENV=production - APP\_DEBUG=false Para desarrollo, modifica estas variables en el servicio laravel:

yaml

environment:

&nbsp; APP\_ENV: local

&nbsp; APP\_DEBUG: true

\### API Keys de Google Gemini 1. Obtén tus API keys desde: https://makersuite.google.com/app/apikey 2. Configúralas en .env: - GOOGLE\_GEMINI\_API\_KEY\_CURSOS - GOOGLE\_GEMINI\_API\_KEY\_NECESIDADES ### Proxy Reverso (Nginx Proxy Manager / Traefik) Si usas un proxy reverso en proxy-network, asegúrate de: 1. Configurar el dominio gevopi.dasalas.shop 2. Enrutar el tráfico al contenedor gevopi en el puerto 80 3. Configurar SSL/TLS si es necesario ## 🆘 Soporte Si encuentras problemas: 1. Revisa los logs de los contenedores 2. Verifica que todas las variables de entorno estén configuradas 3. Asegúrate de que los puertos no estén en uso 4. Consulta la documentación de Laravel: https://laravel.com/docs

