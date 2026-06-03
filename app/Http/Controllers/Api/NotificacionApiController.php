<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Notificacion;
use App\Helpers\PeriodoHelper;

class NotificacionApiController extends Controller
{
    public function contador()
    {
        if (!session()->has('usuario')) {
            return response()->json(['count' => 0]);
        }

        $usuario = Usuario::where('usuario', session('usuario'))->first();
        if (!$usuario) {
            return response()->json(['count' => 0]);
        }

        // Obtener el período de sesión
        $periodoId = PeriodoHelper::getPeriodoIdSesion();
        
        $query = Notificacion::where('usuario_id', $usuario->id)
            ->where('leida', false);
        
        // Filtrar por período si existe
        if ($periodoId) {
            $query->where('periodo_id', $periodoId);
        }
        
        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}