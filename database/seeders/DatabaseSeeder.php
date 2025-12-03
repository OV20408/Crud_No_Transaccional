<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * INSTALACION NUEVA (ejecutar en orden):
     * 1. php artisan migrate
     * 2. php artisan db:seed
     * 
     * Esto ejecutará todos los seeders necesarios para una instalación limpia
     */
    public function run(): void
    {
        echo "==========================================\n";
        echo " GEVOPI - Instalacion Inicial del Sistema\n";
        echo "==========================================\n";

        echo "Paso 1/3: Configurando roles y permisos...\n";
        $this->call(RolesInicialSeeder::class);

        echo "\nPaso 2/3: Creando usuario administrador...\n";
        $this->call(AdminInicialSeeder::class);

        echo "\nPaso 3/3: Cargando datos de catálogo...\n";
        $this->call(UniversidadesInicialSeeder::class);

        echo "\n==========================================\n";
        echo " INSTALACION COMPLETADA EXITOSAMENTE\n";
        echo "==========================================\n";
        echo "\n";
        echo "Credenciales de acceso:\n";
        echo "  Email:    admin@gevopi.bo\n";
        echo "  Password: gevopi2024\n";
        echo "\n";
        echo "IMPORTANTE: Cambia la contraseña después del primer inicio de sesión\n";
        echo "\n";
    }
}