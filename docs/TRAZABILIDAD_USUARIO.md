# 📖 Guía de Usuario - Sistema de Trazabilidad GEVOPI

## ¿Qué es el Sistema de Trazabilidad?

El sistema de trazabilidad es una funcionalidad que permite al **API Gateway central** consultar todas las acciones que un voluntario ha realizado dentro del sistema GEVOPI utilizando únicamente su **Cédula de Identidad (CI)**.

---

## ¿Por qué se implementó?

El API Gateway necesita poder rastrear las actividades de los voluntarios en todos los sistemas integrados (Logística, GEVOPI, Incendios ALAS, etc.) usando un identificador común: el CI del voluntario.

Esto permite:
- Generar reportes unificados de actividad
- Auditar acciones de voluntarios
- Integrar información entre sistemas
- Mantener un historial completo de cada voluntario

---

## ¿Cómo funciona?

### 1. Registro Automático de Acciones

Cada vez que un voluntario realiza una acción en el sistema, se guarda automáticamente su CI en un campo especial. Por ejemplo:

- ✅ Cuando completa una evaluación física/emocional
- ✅ Cuando envía un mensaje en el chat
- ✅ Cuando hace una consulta
- ✅ Cuando crea una solicitud de ayuda
- ✅ Cuando avanza en una capacitación
- ✅ Cuando recibe una recomendación de curso

### 2. Almacenamiento del CI

El CI se guarda como **texto plano**, no como una referencia al usuario. Esto es importante porque:

- Permite rastrear acciones históricas aunque el usuario sea eliminado
- Maneja casos de CIs duplicados entre sistemas
- Es el formato estándar acordado con el equipo de API Gateway

---

## ¿Cómo consultar la trazabilidad?

### Endpoint

```
GET /api/trazabilidad/{ci}
```

### Ejemplo de Uso

```bash
# Consultar todas las acciones del voluntario con CI 12345678
curl -X GET https://tu-servidor.com/api/trazabilidad/12345678
```

### Parámetros

| Parámetro | Tipo | Obligatorio | Descripción |
|-----------|------|-------------|-------------|
| `ci` | texto | Sí | Cédula de Identidad del voluntario a consultar |

---

## ¿Qué información devuelve?

El endpoint devuelve un JSON organizado con todas las acciones del voluntario agrupadas por categoría:

### Categorías de Acciones

| Categoría | Descripción |
|-----------|-------------|
| **evaluaciones** | Tests físicos y emocionales completados por el voluntario |
| **respuestas** | Respuestas individuales a preguntas de evaluaciones |
| **reportes** | Reportes de evaluación generados por el sistema |
| **progreso_capacitaciones** | Avance del voluntario en cursos y etapas de capacitación |
| **consultas** | Consultas realizadas al sistema por el voluntario |
| **chat_mensajes** | Mensajes enviados en el chat de comunicación |
| **solicitudes_ayuda** | Emergencias o solicitudes de ayuda creadas |
| **recomendaciones_cursos** | Cursos recomendados por la Inteligencia Artificial |
| **aptitud_necesidades** | Evaluaciones de aptitud del voluntario |
| **historial_clinico** | Modificaciones al historial clínico |
| **necesidades_asignadas** | Necesidades vinculadas a reportes del voluntario |

---

## Ejemplo de Respuesta

```json
{
    "success": true,
    "message": "Trazabilidad obtenida exitosamente",
    "data": {
        "ci_consultado": "12345678",
        "fecha_consulta": "2025-12-10 02:30:00",
        "sistema": "GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral",
        "total_acciones": 45,
        "acciones": {
            "evaluaciones": {
                "descripcion": "Tests y evaluaciones físicas/emocionales completadas",
                "total": 5,
                "registros": [
                    {
                        "id": 123,
                        "fecha": "2025-12-09 10:30:00",
                        "ci_voluntario_accion": "12345678",
                        "test_nombre": "Evaluación Física Integral",
                        "test_categoria": "Físico",
                        "estado_general": "Procesado por IA"
                    }
                ]
            },
            "consultas": {
                "descripcion": "Consultas realizadas al sistema",
                "total": 12,
                "registros": [
                    {
                        "id": 456,
                        "mensaje": "Necesito información sobre el próximo curso",
                        "estado": "respondido",
                        "respuesta_admin": "El curso inicia el 15 de diciembre",
                        "ci_voluntario_accion": "12345678",
                        "created_at": "2025-12-08 14:20:00"
                    }
                ]
            },
            "solicitudes_ayuda": {
                "descripcion": "Solicitudes de ayuda/emergencia creadas",
                "total": 2,
                "registros": [
                    {
                        "id": 789,
                        "tipo": "Emergencia Médica",
                        "nivel_emergencia": "ALTO",
                        "descripcion": "Voluntario con lesión en el brazo",
                        "estado": "resuelta",
                        "ci_voluntario_accion": "12345678",
                        "created_at": "2025-12-05 09:15:00"
                    }
                ]
            }
            // ... más categorías
        }
    }
}
```

---

## Posibles Respuestas de Error

### CI no proporcionado (400)
```json
{
    "success": false,
    "message": "El CI es requerido",
    "data": null
}
```

### Sin acciones encontradas (200)
```json
{
    "success": true,
    "message": "Trazabilidad obtenida exitosamente",
    "data": {
        "ci_consultado": "99999999",
        "total_acciones": 0,
        "acciones": {
            "evaluaciones": { "total": 0, "registros": [] },
            "consultas": { "total": 0, "registros": [] }
            // ... todas las categorías vacías
        }
    }
}
```

---

## Tablas de la Base de Datos Involucradas

Las siguientes tablas tienen el campo `ci_voluntario_accion` para registrar quién realizó cada acción:

1. `evaluacion` - Evaluaciones de voluntarios
2. `respuesta` - Respuestas a preguntas
3. `reporte` - Reportes generados
4. `progreso_voluntario` - Progreso en capacitaciones
5. `consultas` - Consultas al sistema
6. `chat_mensajes` - Mensajes de chat
7. `solicitudes_ayuda` - Solicitudes de emergencia
8. `curso_recomendaciones` - Recomendaciones de cursos
9. `aptitud_necesidades` - Evaluaciones de aptitud
10. `historial_clinico` - Historial clínico
11. `reporte_necesidad` - Relación reporte-necesidad
12. `reporte_progreso_voluntario` - Relación reporte-progreso

---

## Preguntas Frecuentes

### ¿Por qué el CI se guarda como texto y no como referencia?

Para cumplir con los requisitos del API Gateway:
- Mantener trazabilidad histórica
- Manejar CIs duplicados entre sistemas
- No depender de la existencia del usuario en la BD

### ¿Qué pasa si un voluntario es eliminado?

Sus acciones históricas permanecen intactas en la trazabilidad porque el CI se guardó como texto independiente.

### ¿Se puede filtrar por rango de fechas?

Actualmente no. El endpoint devuelve todas las acciones del voluntario. Si se requiere filtrado, se debe implementar como mejora futura.

### ¿El endpoint requiere autenticación?

Actualmente no requiere autenticación ya que será consumido por el API Gateway. Si se requiere seguridad adicional, se puede agregar middleware.

---

## Contacto y Soporte

- **Sistema:** GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral
- **Fecha de implementación:** 10 de Diciembre de 2025
- **Repositorio:** `OV20408/Crud_No_Transaccional`

---

*Este documento fue generado como parte de la integración con el API Gateway central.*
