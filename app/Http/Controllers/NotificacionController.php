<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Helpers\PeriodoHelper;  // NUEVO
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Usuario::where('usuario', session('usuario'))->first();
        if (!$usuario) {
            return redirect('/Login');
        }

        $notificaciones = PeriodoHelper::getNotificacionesPorPeriodo($usuario->id);

        // Si es petición AJAX, devolver solo el contenido
        if ($request->ajax()) {
            $html = view('notificaciones.partials.content', compact('notificaciones'))->render();
            return response()->json(['html' => $html]);
        }

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['leida' => true]);
        return response()->json(['ok' => true]);
    }

    public function marcarTodasLeidas()
    {
        $usuario = Usuario::where('usuario', session('usuario'))->first();
        if (!$usuario) {
            return response()->json(['ok' => false], 401);
        }

        $periodoId = PeriodoHelper::getPeriodoIdSesion();
        
        $query = Notificacion::where('usuario_id', $usuario->id)
            ->where('leida', false);
        
        // Solo marcar como leídas las del período actual
        if ($periodoId) {
            $query->where(function($q) use ($periodoId) {
                $q->where('periodo_id', $periodoId)
                ->orWhereNull('periodo_id');
            });
        }
        
        $query->update(['leida' => true]);

        return response()->json(['ok' => true]);
    }
}