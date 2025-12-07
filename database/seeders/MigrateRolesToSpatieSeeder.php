<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MigrateRolesToSpatieSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "\n🚀 INICIANDO MIGRACIÓN DE ROLES A SPATIE\n";
        echo str_repeat("=", 50) . "\n\n";

        // 1️⃣ Crear roles desde tu tabla 'rol' (usando 'nombre' en lugar de 'descripcion')
        echo "📋 Paso 1: Migrando roles desde tabla 'rol'...\n";
        $rolesAntiguos = DB::table('rol')->get();
        
        if ($rolesAntiguos->isEmpty()) {
            echo "⚠️  No se encontraron roles en la tabla 'rol'\n";
        } else {
            foreach ($rolesAntiguos as $rolAntiguo) {
                // ✅ CAMBIO: Usar 'nombre' en lugar de 'descripcion'
                $role = Role::firstOrCreate([
                    'name' => $rolAntiguo->nombre, // ← CORREGIDO
                    'guard_name' => 'web'
                ]);
                echo "   ✅ Rol creado: {$role->name}\n";
            }
        }

        // 2️⃣ Crear permisos básicos
        echo "\n📋 Paso 2: Creando permisos básicos...\n";
        
        $permisos = [
            // Permisos de Administrador
            'gestionar_usuarios',
            'gestionar_roles',
            'gestionar_capacitaciones',
            'gestionar_reportes',
            'ver_dashboard_admin',
            'gestionar_certificados',
            'responder_consultas',
            
            // Permisos de Voluntario
            'ver_capacitaciones',
            'completar_etapas',
            'enviar_reportes',
            'solicitar_ayuda',
            'chat_emergencias',
            'descargar_certificados',
            
            // Permisos de Instructor
            'crear_cursos',
            'editar_cursos',
            'ver_progreso_alumnos',
            
            // Permisos de Evaluador
            'crear_evaluaciones',
            'calificar_evaluaciones',
            'ver_resultados',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
            echo "   ✅ Permiso creado: {$permiso}\n";
        }

        // 3️⃣ Asignar permisos a roles
        echo "\n📋 Paso 3: Asignando permisos a roles...\n";
        
        // Administrador
        $adminRole = Role::findByName('Administrador');
        if ($adminRole) {
            $adminRole->syncPermissions([
                'gestionar_usuarios',
                'gestionar_roles',
                'gestionar_capacitaciones',
                'gestionar_reportes',
                'ver_dashboard_admin',
                'gestionar_certificados',
                'responder_consultas',
            ]);
            echo "   ✅ Permisos asignados a: {$adminRole->name}\n";
        }

        // Voluntario
        $voluntarioRole = Role::findByName('Voluntario');
        if ($voluntarioRole) {
            $voluntarioRole->syncPermissions([
                'ver_capacitaciones',
                'completar_etapas',
                'enviar_reportes',
                'solicitar_ayuda',
                'chat_emergencias',
                'descargar_certificados',
            ]);
            echo "   ✅ Permisos asignados a: {$voluntarioRole->name}\n";
        }

        // Instructor (opcional - crear si existe en la BD)
        $instructorRole = Role::firstOrCreate(['name' => 'Instructor', 'guard_name' => 'web']);
        $instructorRole->syncPermissions([
            'crear_cursos',
            'editar_cursos',
            'ver_progreso_alumnos',
            'ver_capacitaciones',
        ]);
        echo "   ✅ Permisos asignados a: {$instructorRole->name}\n";

        // Evaluador (opcional - crear si existe en la BD)
        $evaluadorRole = Role::firstOrCreate(['name' => 'Evaluador', 'guard_name' => 'web']);
        $evaluadorRole->syncPermissions([
            'crear_evaluaciones',
            'calificar_evaluaciones',
            'ver_resultados',
        ]);
        echo "   ✅ Permisos asignados a: {$evaluadorRole->name}\n";

        // 4️⃣ Reasignar roles a usuarios (limpiar asignaciones incorrectas)
        echo "\n📋 Paso 4: Reasignando roles correctos a usuarios...\n";
        
        // Primero eliminar todas las asignaciones incorrectas
        DB::table('model_has_roles')->delete();
        echo "   🗑️  Asignaciones antiguas eliminadas\n";
        
        $usuarios = User::all();
        $contadorAsignados = 0;
        
        foreach ($usuarios as $usuario) {
            if ($usuario->id_rol) {
                $rolAntiguo = DB::table('rol')->find($usuario->id_rol);
                
                if ($rolAntiguo) {
                    try {
                        // ✅ CAMBIO: Usar 'nombre' en lugar de 'descripcion'
                        $usuario->assignRole($rolAntiguo->nombre); // ← CORREGIDO
                        $contadorAsignados++;
                        echo "   ✅ {$usuario->email} -> {$rolAntiguo->nombre}\n";
                    } catch (\Exception $e) {
                        echo "   ❌ Error con {$usuario->email}: {$e->getMessage()}\n";
                    }
                }
            }
        }

        // Resumen final
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🎉 MIGRACIÓN COMPLETADA\n";
        echo str_repeat("=", 50) . "\n";
        echo "   📊 Roles migrados: " . $rolesAntiguos->count() . "\n";
        echo "   🔑 Permisos creados: " . count($permisos) . "\n";
        echo "   👥 Usuarios actualizados: {$contadorAsignados}\n";
        echo str_repeat("=", 50) . "\n\n";
    }
}