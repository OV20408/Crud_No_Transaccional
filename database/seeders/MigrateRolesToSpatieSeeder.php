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

        // 1️⃣ Crear roles desde tu tabla 'rol'
        echo "📋 Paso 1: Migrando roles desde tabla 'rol'...\n";
        $rolesAntiguos = DB::table('rol')->get();
        
        if ($rolesAntiguos->isEmpty()) {
            echo "⚠️  No se encontraron roles en la tabla 'rol'\n";
        } else {
            foreach ($rolesAntiguos as $rolAntiguo) {
                $role = Role::firstOrCreate([
                    'name' => $rolAntiguo->descripcion,
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
        
        // Buscar roles (ajusta los nombres según tu tabla 'rol')
        $adminRole = Role::where('name', 'LIKE', '%Administrador%')->first();
        $voluntarioRole = Role::where('name', 'LIKE', '%Voluntario%')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo([
                'gestionar_usuarios',
                'gestionar_roles',
                'gestionar_capacitaciones',
                'gestionar_reportes',
                'ver_dashboard_admin',
                'gestionar_certificados',
                'responder_consultas',
            ]);
            echo "   ✅ Permisos asignados a: {$adminRole->name}\n";
        } else {
            echo "   ⚠️  No se encontró rol de Administrador\n";
        }

        if ($voluntarioRole) {
            $voluntarioRole->givePermissionTo([
                'ver_capacitaciones',
                'completar_etapas',
                'enviar_reportes',
                'solicitar_ayuda',
                'chat_emergencias',
                'descargar_certificados',
            ]);
            echo "   ✅ Permisos asignados a: {$voluntarioRole->name}\n";
        } else {
            echo "   ⚠️  No se encontró rol de Voluntario\n";
        }

        // 4️⃣ Asignar roles a usuarios existentes
        echo "\n📋 Paso 4: Asignando roles de Spatie a usuarios...\n";
        
        $usuarios = User::all();
        $contadorAsignados = 0;
        $contadorFallidos = 0;
        
        foreach ($usuarios as $usuario) {
            if ($usuario->id_rol) {
                $rolAntiguo = DB::table('rol')->find($usuario->id_rol);
                
                if ($rolAntiguo) {
                    try {
                        // Asignar rol de Spatie
                        $usuario->assignRole($rolAntiguo->descripcion);
                        $contadorAsignados++;
                        echo "   ✅ {$usuario->email} -> {$rolAntiguo->descripcion}\n";
                    } catch (\Exception $e) {
                        $contadorFallidos++;
                        echo "   ❌ Error con {$usuario->email}: {$e->getMessage()}\n";
                    }
                } else {
                    echo "   ⚠️  Usuario {$usuario->email} tiene id_rol inválido: {$usuario->id_rol}\n";
                }
            } else {
                echo "   ⚠️  Usuario {$usuario->email} no tiene id_rol asignado\n";
            }
        }

        // Resumen final
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🎉 MIGRACIÓN COMPLETADA\n";
        echo str_repeat("=", 50) . "\n";
        echo "   📊 Roles migrados: " . $rolesAntiguos->count() . "\n";
        echo "   🔑 Permisos creados: " . count($permisos) . "\n";
        echo "   👥 Usuarios actualizados: {$contadorAsignados}\n";
        if ($contadorFallidos > 0) {
            echo "   ⚠️  Usuarios con error: {$contadorFallidos}\n";
        }
        echo str_repeat("=", 50) . "\n\n";
    }
}