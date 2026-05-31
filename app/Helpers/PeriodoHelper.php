<?php

namespace App\Helpers;

use App\Models\Periodo;
use App\Models\Notificacion;
use App\Models\Usuario;

class PeriodoHelper
{
    /**
     * Obtener el período activo actual
     */
    public static function getPeriodoActual()
    {
        return Periodo::where('estado', 'activo')->first();
    }
    
    /**
     * Obtener el período de la sesión o el activo
     */
    public static function getPeriodoSesion()
    {
        $periodoId = session('periodo_id');
        
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
            if ($periodo) {
                return $periodo;
            }
        }
        
        return self::getPeriodoActual();
    }
    
    /**
     * Guardar período en sesión
     */
    public static function setPeriodoSesion($periodoId)
    {
        session(['periodo_id' => $periodoId]);
    }
    
    /**
     * Obtener ID del período de sesión o activo
     */
    public static function getPeriodoIdSesion()
    {
        $periodo = self::getPeriodoSesion();
        return $periodo ? $periodo->id : null;
    }

    /**
     * Obtener notificaciones filtradas por el período de sesión
     */
    public static function getNotificacionesPorPeriodo($usuarioId, $perPage = 20)
    {
        $periodoId = self::getPeriodoIdSesion();
        
        $query = Notificacion::where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc');
        
        if ($periodoId) {
            $query->where(function($q) use ($periodoId) {
                $q->where('periodo_id', $periodoId)
                  ->orWhereNull('periodo_id');  // Notificaciones globales sin período
            });
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Contar notificaciones no leídas del período actual
     */
    public static function contarNotificacionesNoLeidas($usuarioId)
    {
        $periodoId = self::getPeriodoIdSesion();
        
        $query = Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false);
        
        if ($periodoId) {
            $query->where(function($q) use ($periodoId) {
                $q->where('periodo_id', $periodoId)
                  ->orWhereNull('periodo_id');
            });
        }
        
        return $query->count();
    }
}