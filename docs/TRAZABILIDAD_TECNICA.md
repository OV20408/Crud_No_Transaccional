# 📋 Documentación Técnica - Sistema de Trazabilidad API Gateway

## Contexto de Implementación

Se implementó un sistema de trazabilidad para integración con API Gateway que permite rastrear todas las acciones realizadas por voluntarios en el sistema GEVOPI mediante su CI (Cédula de Identidad).

---

## 1. Migración de Base de Datos

**Archivo:** `database/migrations/2025_12_10_024818_add_ci_voluntario_to_trazabilidad_tables.php`

### Campo Agregado
- **Nombre:** `ci_voluntario_accion`
- **Tipo:** VARCHAR (string)
- **Nullable:** Sí
- **Propósito:** Almacenar el CI del voluntario que realizó la acción

### Tablas Afectadas

| Tabla | Descripción |
|-------|-------------|
| `evaluacion` | Evaluaciones físicas/emocionales |
| `respuesta` | Respuestas a preguntas de tests |
| `reporte` | Reportes generados |
| `progreso_voluntario` | Progreso en capacitaciones |
| `consultas` | Consultas al sistema |
| `chat_mensajes` | Mensajes de chat |
| `solicitudes_ayuda` | Solicitudes de emergencia |
| `curso_recomendaciones` | Recomendaciones de cursos por IA |
| `aptitud_necesidades` | Evaluaciones de aptitud |
| `historial_clinico` | Historial clínico del voluntario |
| `reporte_necesidad` | Relación reporte-necesidad |
| `reporte_progreso_voluntario` | Relación reporte-progreso |

### Nota Importante
El campo almacena el CI como **texto plano** (NO como FK a usuario) para:
- Mantener trazabilidad histórica incluso si el usuario es eliminado
- Manejar posibles CIs duplicados entre sistemas
- Cumplir con el estándar del API Gateway

---

## 2. TrazabilidadController

**Archivo:** `app/Http/Controllers/TrazabilidadController.php`

### Método Principal
```php
public function porVoluntario($ci)
```

### Lógica de Funcionamiento
1. Valida que el CI no esté vacío
2. Realiza 11 queries independientes a las tablas de acciones
3. Filtra todos los registros por `WHERE ci_voluntario_accion = $ci`
4. Retorna JSON estructurado con totales y registros por categoría

### Queries Ejecutadas
```php
// 1. Evaluaciones
DB::table('evaluacion')->where('ci_voluntario_accion', $ci)

// 2. Respuestas
DB::table('respuesta')->where('ci_voluntario_accion', $ci)

// 3. Reportes
DB::table('reporte')->where('ci_voluntario_accion', $ci)

// 4. Progreso en capacitaciones
DB::table('progreso_voluntario')->where('ci_voluntario_accion', $ci)

// 5. Consultas
DB::table('consultas')->where('ci_voluntario_accion', $ci)

// 6. Mensajes de chat
DB::table('chat_mensajes')->where('ci_voluntario_accion', $ci)

// 7. Solicitudes de ayuda
DB::table('solicitudes_ayuda')->where('ci_voluntario_accion', $ci)

// 8. Recomendaciones de cursos
DB::table('curso_recomendaciones')->where('ci_voluntario_accion', $ci)

// 9. Aptitud de necesidades
DB::table('aptitud_necesidades')->where('ci_voluntario_accion', $ci)

// 10. Historial clínico
DB::table('historial_clinico')->where('ci_voluntario_accion', $ci)

// 11. Necesidades asignadas
DB::table('reporte_necesidad')->where('ci_voluntario_accion', $ci)
```

---

## 3. Controladores Modificados

### EvaluacionVoluntarioController
**Archivo:** `app/Http/Controllers/EvaluacionVoluntarioController.php`

```php
// El voluntario accede por token, no por Auth
// Se obtiene el CI directamente del objeto $voluntario

$reporte = Reporte::create([
    // ... otros campos
    'ci_voluntario_accion' => $voluntario->ci
]);

Evaluacion::create([
    // ... otros campos
    'ci_voluntario_accion' => $voluntario->ci
]);

CursoRecomendacion::create([
    // ... otros campos
    'ci_voluntario_accion' => $voluntario->ci
]);
```

### VoluntarioController
**Archivo:** `app/Http/Controllers/VoluntarioController.php`

```php
// Acciones realizadas por admin logueado
// Se obtiene el CI del usuario autenticado

DB::table('reporte')->insertGetId([
    // ... otros campos
    'ci_voluntario_accion' => Auth::user()->ci ?? null
]);

DB::table('reporte_necesidad')->insert([
    // ... otros campos
    'ci_voluntario_accion' => Auth::user()->ci ?? null
]);
```

### SolicitudAyudaApiController
**Archivo:** `app/Http/Controllers/Api/SolicitudAyudaApiController.php`

```php
// API móvil - se busca el CI por el id_usuario
$dataToCreate = [
    // ... otros campos
    'ci_voluntario_accion' => User::where('id_usuario', $validated['voluntario_id'])->value('ci')
];
```

### ChatMensajeApiController
**Archivo:** `app/Http/Controllers/Api/ChatMensajeApiController.php`

```php
$validated['ci_voluntario_accion'] = User::where('id_usuario', $validated['voluntario_id'])->value('ci');
$mensaje = ChatMensaje::create($validated);
```

### ConsultaApiController
**Archivo:** `app/Http/Controllers/Api/ConsultaApiController.php`

```php
$consulta = Consulta::create([
    // ... otros campos
    'ci_voluntario_accion' => User::where('id_usuario', $validated['voluntario_id'])->value('ci')
]);
```

### CapacitacionApiController
**Archivo:** `app/Http/Controllers/Api/CapacitacionApiController.php`

```php
$progreso = ProgresoVoluntario::create([
    // ... otros campos
    'ci_voluntario_accion' => User::where('id_usuario', $request->id_usuario)->value('ci')
]);
```

### EtapaApiController
**Archivo:** `app/Http/Controllers/Api/EtapaApiController.php`

```php
$progreso->ci_voluntario_accion = User::where('id_usuario', $data['id_usuario'])->value('ci');
$progreso->save();
```

---

## 4. Modelos Actualizados

Se agregó `ci_voluntario_accion` al array `$fillable` de los siguientes modelos:

| Modelo | Archivo |
|--------|---------|
| Reporte | `app/Models/Reporte.php` |
| Evaluacion | `app/Models/Evaluacion.php` |
| CursoRecomendacion | `app/Models/CursoRecomendacion.php` |
| ProgresoVoluntario | `app/Models/ProgresoVoluntario.php` |
| Consulta | `app/Models/Consulta.php` |
| ChatMensaje | `app/Models/ChatMensaje.php` |
| SolicitudAyuda | `app/Models/SolicitudAyuda.php` |

---

## 5. Ruta API

**Archivo:** `routes/api.php`

```php
use App\Http\Controllers\TrazabilidadController;

// ==================== TRAZABILIDAD - API GATEWAY ====================
Route::get('/trazabilidad/{ci}', [TrazabilidadController::class, 'porVoluntario']);
```

### Endpoint
```
GET /api/trazabilidad/{ci}
```

### Parámetros
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `ci` | string | Cédula de Identidad del voluntario |

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Trazabilidad obtenida exitosamente",
    "data": {
        "ci_consultado": "12345678",
        "fecha_consulta": "2025-12-10 02:30:00",
        "sistema": "GEVOPI",
        "total_acciones": 45,
        "acciones": { ... }
    }
}
```

### Respuesta Error (400)
```json
{
    "success": false,
    "message": "El CI es requerido",
    "data": null
}
```

---

## 6. Estructura de Respuesta JSON

```json
{
    "ci_consultado": "string",
    "fecha_consulta": "datetime",
    "sistema": "GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral",
    "total_acciones": "integer",
    "acciones": {
        "evaluaciones": {
            "descripcion": "string",
            "total": "integer",
            "registros": []
        },
        "respuestas": { ... },
        "reportes": { ... },
        "progreso_capacitaciones": { ... },
        "consultas": { ... },
        "chat_mensajes": { ... },
        "solicitudes_ayuda": { ... },
        "recomendaciones_cursos": { ... },
        "aptitud_necesidades": { ... },
        "historial_clinico": { ... },
        "necesidades_asignadas": { ... }
    }
}
```

---

## 7. Diagrama de Flujo

```
┌─────────────────┐
│  API Gateway    │
│  (Externo)      │
└────────┬────────┘
         │
         │ GET /api/trazabilidad/{ci}
         ▼
┌─────────────────┐
│ TrazabilidadController │
│ porVoluntario() │
└────────┬────────┘
         │
         │ Queries con WHERE ci_voluntario_accion = $ci
         ▼
┌─────────────────────────────────────────┐
│              Base de Datos              │
├─────────────────────────────────────────┤
│ evaluacion          │ respuesta         │
│ reporte             │ progreso_voluntario│
│ consultas           │ chat_mensajes     │
│ solicitudes_ayuda   │ curso_recomendaciones│
│ aptitud_necesidades │ historial_clinico │
│ reporte_necesidad   │                   │
└─────────────────────────────────────────┘
         │
         │ JSON Response
         ▼
┌─────────────────┐
│  API Gateway    │
└─────────────────┘
```

---

## 8. Consideraciones de Rendimiento

- Cada consulta al endpoint ejecuta **11 queries** independientes
- Se utilizan **JOINs** para enriquecer la información con datos relacionados
- Los resultados se ordenan por `created_at DESC` para mostrar lo más reciente primero
- Se recomienda agregar **índices** a la columna `ci_voluntario_accion` si el volumen de datos crece

### Índices Recomendados (futuro)
```sql
CREATE INDEX idx_evaluacion_ci ON evaluacion(ci_voluntario_accion);
CREATE INDEX idx_reporte_ci ON reporte(ci_voluntario_accion);
CREATE INDEX idx_consultas_ci ON consultas(ci_voluntario_accion);
-- ... para todas las tablas
```

---

**Fecha de implementación:** 10 de Diciembre de 2025
**Commit:** `c9bbcd3`
