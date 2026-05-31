<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Notificacion;

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

        $count = Notificacion::where('usuario_id', $usuario->id)
            ->where('leida', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}