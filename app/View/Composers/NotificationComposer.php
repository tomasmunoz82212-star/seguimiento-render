<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Usuario;
use App\Models\Notificacion;

class NotificationComposer
{
    public function compose(View $view)
    {
        if (session()->has('usuario')) {
            $usuario = Usuario::where('usuario', session('usuario'))->first();
            if ($usuario) {
                $count = Notificacion::where('usuario_id', $usuario->id)
                            ->where('leida', false)
                            ->count();
                $view->with('notificaciones_count', $count);
                return;
            }
        }
        $view->with('notificaciones_count', 0);
    }
}