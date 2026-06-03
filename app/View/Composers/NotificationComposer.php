<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Usuario;
use App\Models\Notificacion;
use App\Helpers\PeriodoHelper;

class NotificationComposer
{
    public function compose(View $view)
    {
        if (session()->has('usuario')) {
            $usuario = Usuario::where('usuario', session('usuario'))->first();
            if ($usuario) {
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
                
                $count = $query->count();
                $view->with('notificaciones_count', $count);
                return;
            }
        }
        $view->with('notificaciones_count', 0);
    }
}