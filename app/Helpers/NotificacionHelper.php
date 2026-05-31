<?php

namespace App\Helpers;

use App\Models\Usuario;
use App\Models\Notificacion;

class NotificacionHelper
{
    /**
     * Retorna el número de notificaciones no leídas del usuario actual de sesión.
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

        // Usar el nuevo método que filtra por período
        return PeriodoHelper::contarNotificacionesNoLeidas($usuario->id);
    }
}