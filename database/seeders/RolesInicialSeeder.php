<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesInicialSeeder extends Seeder
{
    /**
     * Crear roles y permisos iniciales del sistema GEVOPI
     * EJECUTAR: php artisan db:seed --class=RolesInicialSeeder
     */
    public function run(): void
    {
        echo "\n Creando roles y permisos iniciales del sistema GEVOPI...\n\n";

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 1. CREAR ROLES EN TABLA 'rol' (sistema antiguo, compatibilidad)
        // ========================================
        echo "Paso 1: Creando roles en tabla 'rol'...\n";
        
        $roles = [
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema web. Gestiona usuarios, capacitaciones, reportes y certificados.'
            ],
            [
                'id' => 2,
                'nombre' => 'Voluntario',
                'descripcion' => 'Usuario de la aplicación móvil. Accede a capacitaciones asignadas, envía reportes y solicita ayuda.'
            ],
            [
                'id' => 3,
                'nombre' => 'Instructor',
                'descripcion' => 'Crea y gestiona contenido de cursos. Evalúa progreso de voluntarios.'
            ],
            [
                'id' => 4,
                'nombre' => 'Evaluador',
                'descripcion' => 'Crea evaluaciones y califica respuestas de voluntarios.'
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('rol')->updateOrInsert(
                ['id' => $rol['id']],
                [
                    'nombre' => $rol['nombre'],
                    'descripcion' => $rol['descripcion'],
                    'created_at' => now(),
                ]
            );
            echo " Rol creado: {$rol['nombre']} (id: {$rol['id']})\n";
        }

        // ========================================
        // 2. CREAR ROLES EN SPATIE
        // ========================================
        echo "\n Paso 2: Creando roles en Spatie Permission...\n";
        
        foreach ($roles as $rol) {
            Role::firstOrCreate(
                ['name' => $rol['nombre']],
                ['guard_name' => 'web']
            );
            echo " Rol Spatie: {$rol['nombre']}\n";
        }

        // ========================================
        // 3. CREAR PERMISOS
        // ========================================
        echo "\n Paso 3: Creando permisos del sistema...\n";
        
        $permisos = [
            [
                'name' => 'gestionar_usuarios',
                'descripcion' => 'Crear, editar, eliminar y gestionar usuarios del sistema'
            ],
            [
                'name' => 'gestionar_roles',
                'descripcion' => 'Administrar roles y permisos'
            ],
            [
                'name' => 'gestionar_capacitaciones',
                'descripcion' => 'Crear, editar, eliminar capacitaciones'
            ],
            [
                'name' => 'asignar_capacitaciones',
                'descripcion' => 'Asignar capacitaciones a voluntarios'
            ],
            [
                'name' => 'gestionar_reportes',
                'descripcion' => 'Ver y gestionar reportes de voluntarios'
            ],
            [
                'name' => 'ver_dashboard_admin',
                'descripcion' => 'Acceder al dashboard administrativo'
            ],
            [
                'name' => 'gestionar_certificados',
                'descripcion' => 'Generar y gestionar certificados'
            ],
            [
                'name' => 'responder_consultas',
                'descripcion' => 'Responder consultas de voluntarios'
            ],
            [
                'name' => 'chat_admin',
                'descripcion' => 'Chat con voluntarios desde panel web'
            ],
            [
                'name' => 'ver_ayudas_solicitadas',
                'descripcion' => 'Ver solicitudes de ayuda de emergencia'
            ],
            
            // === PERMISOS DE VOLUNTARIO ===
            [
                'name' => 'ver_capacitaciones_asignadas',
                'descripcion' => 'Ver capacitaciones que le fueron asignadas'
            ],
            [
                'name' => 'completar_etapas',
                'descripcion' => 'Marcar etapas de capacitación como completadas'
            ],
            [
                'name' => 'ver_progreso',
                'descripcion' => 'Ver su propio progreso en capacitaciones'
            ],
            [
                'name' => 'enviar_reportes',
                'descripcion' => 'Enviar reportes físicos y emocionales'
            ],
            [
                'name' => 'solicitar_ayuda',
                'descripcion' => 'Solicitar ayuda en situaciones de emergencia'
            ],
            [
                'name' => 'chat_emergencias',
                'descripcion' => 'Chat de emergencias con administradores'
            ],
            [
                'name' => 'descargar_certificados',
                'descripcion' => 'Descargar sus propios certificados'
            ],
            [
                'name' => 'enviar_consultas',
                'descripcion' => 'Enviar consultas al equipo administrativo'
            ],
            [
                'name' => 'ver_perfil',
                'descripcion' => 'Ver su propio perfil'
            ],
            [
                'name' => 'actualizar_perfil',
                'descripcion' => 'Actualizar datos personales'
            ],
            
            // === PERMISOS DE INSTRUCTOR ===
            [
                'name' => 'crear_cursos',
                'descripcion' => 'Crear nuevos cursos y contenido educativo'
            ],
            [
                'name' => 'editar_cursos',
                'descripcion' => 'Editar cursos existentes'
            ],
            [
                'name' => 'crear_etapas',
                'descripcion' => 'Crear etapas dentro de los cursos'
            ],
            [
                'name' => 'ver_progreso_alumnos',
                'descripcion' => 'Ver progreso de voluntarios en sus cursos'
            ],
            [
                'name' => 'evaluar_etapas',
                'descripcion' => 'Evaluar completitud de etapas'
            ],
            
            // === PERMISOS DE EVALUADOR ===
            [
                'name' => 'crear_evaluaciones',
                'descripcion' => 'Crear evaluaciones y cuestionarios'
            ],
            [
                'name' => 'editar_evaluaciones',
                'descripcion' => 'Editar evaluaciones existentes'
            ],
            [
                'name' => 'calificar_evaluaciones',
                'descripcion' => 'Calificar respuestas de evaluaciones'
            ],
            [
                'name' => 'ver_resultados',
                'descripcion' => 'Ver resultados de evaluaciones'
            ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name']],
                ['guard_name' => 'web']
            );
            echo " Permiso: {$permiso['name']}\n";
        }

        // ========================================
        // 4. ASIGNAR PERMISOS A ROLES
        // ========================================
        echo "\n Paso 4: Asignando permisos a roles...\n";
        
        // ADMINISTRADOR - Todos los permisos de gestión
        $adminRole = Role::findByName('Administrador');
        $adminRole->syncPermissions([
            'gestionar_usuarios',
            'gestionar_roles',
            'gestionar_capacitaciones',
            'asignar_capacitaciones',
            'gestionar_reportes',
            'ver_dashboard_admin',
            'gestionar_certificados',
            'responder_consultas',
            'chat_admin',
            'ver_ayudas_solicitadas',
            // Permisos de evaluaciones (para acceso a /evaluacion y /evaluacion_pruebas)
            'crear_evaluaciones',
            'editar_evaluaciones',
            'calificar_evaluaciones',
            'ver_resultados',
        ]);
        echo " Permisos asignados a: Administrador (14 permisos)\n";

        // VOLUNTARIO - Permisos de app móvil
        $voluntarioRole = Role::findByName('Voluntario');
        $voluntarioRole->syncPermissions([
            'ver_capacitaciones_asignadas',
            'completar_etapas',
            'ver_progreso',
            'enviar_reportes',
            'solicitar_ayuda',
            'chat_emergencias',
            'descargar_certificados',
            'enviar_consultas',
            'ver_perfil',
            'actualizar_perfil',
        ]);
        echo "   ✅ Permisos asignados a: Voluntario (10 permisos)\n";

        // INSTRUCTOR - Permisos de gestión de cursos
        $instructorRole = Role::findByName('Instructor');
        $instructorRole->syncPermissions([
            'crear_cursos',
            'editar_cursos',
            'crear_etapas',
            'ver_progreso_alumnos',
            'evaluar_etapas',
            'ver_capacitaciones_asignadas', // Para ver cursos
        ]);
        echo " Permisos asignados a: Instructor (6 permisos)\n";

        // EVALUADOR - Permisos de evaluaciones
        $evaluadorRole = Role::findByName('Evaluador');
        $evaluadorRole->syncPermissions([
            'crear_evaluaciones',
            'editar_evaluaciones',
            'calificar_evaluaciones',
            'ver_resultados',
        ]);
        echo " Permisos asignados a: Evaluador (4 permisos)\n";

        // ========================================
        // RESUMEN FINAL
        // ========================================
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "ROLES Y PERMISOS CREADOS EXITOSAMENTE\n";
        echo str_repeat("=", 60) . "\n";
        echo "Resumen:\n";
        echo "   • Roles creados: " . count($roles) . "\n";
        echo "   • Permisos creados: " . count($permisos) . "\n";
        echo "   • Sistema: Spatie Permission + compatibilidad con tabla 'rol'\n";
        echo str_repeat("=", 60) . "\n\n";
    }
}


