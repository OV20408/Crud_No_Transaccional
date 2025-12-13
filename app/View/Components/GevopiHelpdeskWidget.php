<?php

namespace App\View\Components;

use Lukehowland\HelpdeskWidget\View\Components\HelpdeskWidget;

/**
 * Componente personalizado de Helpdesk Widget para GEVOPI
 * 
 * Este componente extiende el widget de Helpdesk para mapear
 * los campos 'nombres' y 'apellidos' del modelo User de GEVOPI
 * a los campos esperados por el widget (first_name, last_name)
 */
class GevopiHelpdeskWidget extends HelpdeskWidget
{
    /**
     * Obtener el primer nombre del usuario
     * 
     * GEVOPI usa 'nombres' en lugar de 'first_name'
     */
    protected function getUserFirstName($user): string
    {
        // Intentar obtener 'nombres' primero (campo de GEVOPI)
        if (!empty($user->nombres)) {
            // Si hay múltiples nombres, tomar solo el primero
            $nombres = explode(' ', trim($user->nombres));
            return $nombres[0] ?? '';
        }
        
        // Fallback a 'name' si existe
        if (!empty($user->name)) {
            $parts = explode(' ', trim($user->name));
            return $parts[0] ?? '';
        }
        
        return '';
    }
    
    /**
     * Obtener el apellido del usuario
     * 
     * GEVOPI usa 'apellidos' en lugar de 'last_name'
     */
    protected function getUserLastName($user): string
    {
        // Intentar obtener 'apellidos' primero (campo de GEVOPI)
        if (!empty($user->apellidos)) {
            return trim($user->apellidos);
        }
        
        // Fallback a 'name' si existe (tomar todo excepto primera palabra)
        if (!empty($user->name)) {
            $parts = explode(' ', trim($user->name));
            array_shift($parts); // Quitar primera palabra (nombre)
            return implode(' ', $parts);
        }
        
        return '';
    }
}
