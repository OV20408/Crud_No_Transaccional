# CRUD No Transaccional - API Laravel

Backend API para la aplicación móvil GEVOPI (Gestión de Voluntarios).

## 🚀 Configuración del Proyecto

### Requisitos
- Docker Desktop
- Git
- Postman (para pruebas de API)

### Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/OV20408/Crud_No_Transaccional.git
cd Crud_No_Transaccional
```

2. **Configurar variables de entorno**
```bash
cp .env.example .env
```

Actualizar las credenciales de la base de datos en `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=crud_lav
DB_USERNAME=postgres
DB_PASSWORD=tilin
```

3. **Levantar los contenedores Docker**
```bash
docker-compose up -d
```

4. **Instalar dependencias y configurar Laravel**
```bash
docker exec laravel_app composer install
docker exec laravel_app php artisan key:generate
docker exec laravel_app php artisan migrate
```

5. **Acceder a la aplicación**
- Web: `http://localhost:8010`
- API: `http://localhost:8010/api`

---

## 📱 Configuración para App Móvil

Si estás configurando la aplicación móvil React Native para conectarse a esta API:

### 1. Obtener tu dirección IP
```bash
ipconfig  # Windows
ifconfig  # Mac/Linux
```

Busca tu IPv4 (ejemplo: `192.168.0.4`)

### 2. Configurar la API en la app móvil

Edita el archivo `src/services/api.js` en el proyecto móvil:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://TU_IP_AQUI:8010/api',  // Ejemplo: http://192.168.0.4:8010/api
  timeout: 5000,
  headers: {
    'Content-Type': 'application/json',
  },
});

export default api;
```

**⚠️ Importante:** 
- Reemplaza `TU_IP_AQUI` con la IP de tu computadora
- Asegúrate de que tu computadora y tu dispositivo móvil estén en la misma red WiFi
- El puerto por defecto es `8010`

### 3. CORS ya está configurado
El backend ya tiene CORS habilitado para aceptar peticiones desde cualquier origen.

---

## 🔑 Endpoints Principales

### Autenticación
```
POST /api/usuarios/login
Body: { "ci": "99999999", "contrasena": "dagner123" }
```

### Voluntarios
```
GET  /api/voluntarios                    # Lista todos los voluntarios
GET  /api/voluntario/voluntarios         # Para app móvil
GET  /api/voluntarios/{id}               # Obtener por ID
POST /api/voluntarios                    # Crear voluntario
```

### Usuarios
```
GET /api/usuarios                        # Lista todos
GET /api/usuarios/{id}                   # Por ID
GET /api/usuarios/ci/{ci}                # Por CI
```

### Consultas
```
GET  /api/consultas                      # Listar
POST /api/consultas                      # Crear
```

---

## 👤 Usuarios de Prueba

**Voluntario - Dagner**
- CI: `99999999`
- Contraseña: `dagner123`

**Voluntario - Juan**
- CI: `87654321`
- Contraseña: `password`

**Voluntario - Carlos**
- CI: `11223344`
- Contraseña: `password123`

---

## 🛠️ Comandos Útiles

```bash
# Ver logs del contenedor
docker logs laravel_app -f

# Reiniciar contenedores
docker-compose restart

# Detener contenedores
docker-compose down

# Crear un nuevo voluntario desde terminal
docker exec laravel_app php artisan tinker
User::create([...])
```

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
