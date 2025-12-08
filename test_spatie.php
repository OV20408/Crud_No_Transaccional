<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           VERIFICACIÓN DE SPATIE PERMISSION                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// 1. Roles
echo "📋 ROLES REGISTRADOS:\n";
echo str_repeat("─", 60) . "\n";
$roles = Role::all();
if ($roles->count() > 0) {
    foreach ($roles as $role) {
        $permCount = $role->permissions->count();
        echo "  ✅ {$role->name} ({$permCount} permisos)\n";
    }
} else {
    echo "  ❌ No hay roles registrados\n";
}

// 2. Permisos
echo "\n🔐 PERMISOS REGISTRADOS:\n";
echo str_repeat("─", 60) . "\n";
$permissions = Permission::all();
if ($permissions->count() > 0) {
    $chunks = $permissions->chunk(3);
    foreach ($chunks as $chunk) {
        $names = $chunk->pluck('name')->map(fn($n) => "  • $n")->join("\t");
        echo "$names\n";
    }
} else {
    echo "  ❌ No hay permisos registrados\n";
}

// 3. Usuarios con roles
echo "\n👥 USUARIOS Y SUS ROLES:\n";
echo str_repeat("─", 60) . "\n";
$usuarios = User::with('roles')->get();
if ($usuarios->count() > 0) {
    foreach ($usuarios as $usuario) {
        $rolesStr = $usuario->roles->pluck('name')->join(', ') ?: 'Sin rol';
        echo "  📧 {$usuario->email}\n";
        echo "     └─ Rol: {$rolesStr}\n";
        
        // Verificar algunos permisos
        if ($usuario->hasRole('Administrador')) {
            echo "     └─ ✅ Tiene permisos de Administrador\n";
        } elseif ($usuario->hasRole('Voluntario')) {
            echo "     └─ ✅ Tiene permisos de Voluntario\n";
        }
    }
} else {
    echo "  ❌ No hay usuarios registrados\n";
}

// 4. Test de permisos
echo "\n🧪 TEST DE PERMISOS:\n";
echo str_repeat("─", 60) . "\n";

$admin = User::whereHas('roles', function($q) {
    $q->where('name', 'Administrador');
})->first();

if ($admin) {
    echo "  Testing usuario administrador: {$admin->email}\n";
    echo "     └─ ¿Tiene rol 'Administrador'? " . ($admin->hasRole('Administrador') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "     └─ ¿Puede gestionar_usuarios? " . ($admin->can('gestionar_usuarios') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "     └─ ¿Puede gestionar_capacitaciones? " . ($admin->can('gestionar_capacitaciones') ? '✅ SÍ' : '❌ NO') . "\n";
}

$voluntario = User::whereHas('roles', function($q) {
    $q->where('name', 'Voluntario');
})->first();

if ($voluntario) {
    echo "\n  Testing usuario voluntario: {$voluntario->email}\n";
    echo "     └─ ¿Tiene rol 'Voluntario'? " . ($voluntario->hasRole('Voluntario') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "     └─ ¿Puede ver_capacitaciones? " . ($voluntario->can('ver_capacitaciones') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "     └─ ¿Puede gestionar_usuarios? " . ($voluntario->can('gestionar_usuarios') ? '❌ NO (correcto)' : '✅ SÍ (incorrecto)') . "\n";
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ TEST COMPLETADO                        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
