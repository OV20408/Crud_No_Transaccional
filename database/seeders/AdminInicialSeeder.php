<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminInicialSeeder extends Seeder
{
    /**
     * Crear usuario administrador inicial del sistema
     * EJECUTAR: php artisan db:seed --class=AdminInicialSeeder
     */
    public function run(): void
    {

        $adminExiste = User::where('id_rol', 1)->exists();
        
        if ($adminExiste) {
            echo "Ya existe un usuario administrador en el sistema.\n";
            return;
        }

        // Crear usuario administrador
        $admin = User::create([
            'nombres' => 'Administrador',
            'apellidos' => 'Sistema GEVOPI',
            'ci' => '0000000',
            'id_rol' => 1,
            'email' => 'admin@gevopi.bo',

            // ⚠️ Importante: usar "password", NO contrasena
            'password' => 'gevopi2024',

            'telefono' => '00000000',
            'direccion_domicilio' => 'La Paz, Bolivia',
            'fecha_nacimiento' => '1990-01-01',

            // ✔ tu migración usa "genero"
            'genero' => 'M',

            'tipo_sangre' => 'O+',
            'estado' => 'activo',
        ]);


        // Asignar rol de Spatie
        $admin->assignRole('Administrador');

        echo "Usuario administrador creado exitosamente:\n\n";
        echo "╔════════════════════════════════════════════════════╗\n";
        echo "║           CREDENCIALES DE ACCESO                   ║\n";
        echo "╠════════════════════════════════════════════════════╣\n";
        echo "║  Email:    admin@gevopi.bo                      ║\n";
        echo "║  Password: gevopi2024                           ║\n";
        echo "║  Rol:      Administrador                        ║\n";
        echo "╠════════════════════════════════════════════════════╣\n";
        echo "║   IMPORTANTE:                                   ║\n";
        echo "║  • Cambia esta contraseña inmediatamente          ║\n";
        echo "║    después del primer inicio de sesión            ║\n";
        echo "║  • No compartas estas credenciales                ║\n";
        echo "╚════════════════════════════════════════════════════╝\n\n";
    }
}