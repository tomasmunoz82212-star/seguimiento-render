<?php

namespace App\Helpers;

use App\Models\Usuario;
use App\Models\Notificacion;

class NotificacionHelper
{
    /**
     * Retorna el número de notificaciones no leídas del usuario actual de sesión.
     * SOLO cuenta notificaciones del período activo en sesión.
     *
     * @return int
     */
    public static function contarNoLeidas()
    {
        if (!session()->has('usuario')) {
            return 0;
        }

        $usuario = Usuario::where('usuario', session('usuario'))->first();
        if (!$usuario) {
            return 0;
        }

        // Obtener el período de sesión
        $periodoId = PeriodoHelper::getPeriodoIdSesion();
        
        // Si no hay período en sesión, usar el período activo
        if (!$periodoId) {
            $periodoActual = PeriodoHelper::getPeriodoActual();
            $periodoId = $periodoActual ? $periodoActual->id : null;
        }
        
        // Si no hay período, contador 0
        if (!$periodoId) {
            return 0;
        }
        
        // SOLO contar notificaciones del período actual
        $count = Notificacion::where('usuario_id', $usuario->id)
            ->where('leida', false)
            ->where('periodo_id', $periodoId)
            ->count();
        
        return $count;
    }
}