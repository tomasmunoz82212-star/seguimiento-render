<?php

namespace App\Helpers;

use App\Models\Usuario;
use App\Models\Notificacion;

class NotificacionHelper
{
    /**
     * Retorna el número de notificaciones no leídas del usuario actual de sesión,
     * filtradas por el período activo en sesión.
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
        
        $query = Notificacion::where('usuario_id', $usuario->id)
            ->where('leida', false);
        
        // Filtrar por período si existe
        if ($periodoId) {
            $query->where(function($q) use ($periodoId) {
                $q->where('periodo_id', $periodoId)
                  ->orWhereNull('periodo_id');
            });
        }
        
        return $query->count();
    }
}
