<?php

namespace App\Helpers;

use App\Models\Usuario;

class UsuarioHelper
{
    /**
     * Generar nombre de usuario con formato: cedula.nombre
     * Ejemplo: 1234567890.dulfran
     */
    public static function generarUsuario($documento, $primerNombre)
    {
        // Limpiar nombre (minúscula, sin tildes)
        $nombreLimpio = self::limpiarNombre($primerNombre);
        
        // Construir usuario base: cedula.nombre
        $usuarioBase = $documento . '.' . $nombreLimpio;
        
        // Verificar unicidad
        return self::hacerUnico($usuarioBase);
    }
    
    /**
     * Limpiar nombre: minúscula, sin tildes, sin espacios
     */
    private static function limpiarNombre($texto)
    {
        $texto = strtolower(trim($texto));
        
        // Reemplazar tildes
        $mapa = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n',
            'ü' => 'u', 'Ü' => 'u',
        ];
        $texto = strtr($texto, $mapa);
        
        // Eliminar caracteres no permitidos
        $texto = preg_replace('/[^a-z]/', '', $texto);
        
        return $texto;
    }
    
    /**
     * Verificar unicidad y agregar número si es necesario
     */
    private static function hacerUnico($usuarioBase, $contador = 0)
    {
        $usuario = $contador > 0 ? $usuarioBase . $contador : $usuarioBase;
        
        $existe = Usuario::where('usuario', $usuario)->exists();
        
        if ($existe) {
            return self::hacerUnico($usuarioBase, $contador + 1);
        }
        
        return $usuario;
    }
}