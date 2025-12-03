<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Universidad;

class UniversidadesInicialSeeder extends Seeder
{
    /**
     * Crear universidades principales de Bolivia
     * EJECUTAR: php artisan db:seed --class=UniversidadesInicialSeeder
     */
    public function run(): void
    {
        echo "\universidades de Bolivia...\n\n";

        $universidades = [
            // La Paz
            [
                'nombre' => 'Universidad Mayor de San Andrés (UMSA)',
                'direccion' => 'Av. Villazón Nro. 1995, La Paz',
                'telefono' => '2440071',
                'email' => 'rectorado@umsa.bo',
            ],
            [
                'nombre' => 'Universidad Católica Boliviana "San Pablo"',
                'direccion' => 'Av. 14 de Septiembre Nro. 4807, La Paz',
                'telefono' => '2782222',
                'email' => 'info@ucb.edu.bo',
            ],
            [
                'nombre' => 'Universidad Privada Boliviana (UPB)',
                'direccion' => 'Av. Hernando Siles, La Paz',
                'telefono' => '2141800',
                'email' => 'informaciones@upb.edu',
            ],
            
            // Cochabamba

            [
                'nombre' => 'Universidad del Valle',
                'direccion' => 'Km 7 Carretera a Sacaba, Cochabamba',
                'telefono' => '4217171',
                'email' => 'informacion@univalle.edu',
            ],
            
            // Santa Cruz
            [
                'nombre' => 'Universidad Autónoma Gabriel René Moreno (UAGRM)',
                'direccion' => 'Av. Busch esquina Guapay, Santa Cruz',
                'telefono' => '3346106',
                'email' => 'rectorado@uagrm.edu.bo',
            ],
            [
                'nombre' => 'Universidad Privada de Santa Cruz (UPSA)',
                'direccion' => 'Av. Paraguá y Cuarto Anillo, Santa Cruz',
                'telefono' => '3636000',
                'email' => 'info@upsa.edu.bo',
            ],
            
            
        ];

        foreach ($universidades as $uni) {
            Universidad::firstOrCreate(
                ['nombre' => $uni['nombre']],
                [
                    'direccion' => $uni['direccion'],
                    'telefono' => $uni['telefono'],
                    'email' => $uni['email'],
                ]
            );
            echo "    {$uni['nombre']}\n";
        }

        echo "\n " . count($universidades) . " universidades creadas exitosamente\n\n";
    }
}