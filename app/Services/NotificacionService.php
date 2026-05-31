<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Reporte;
use App\Models\Usuario;

class NotificacionService
{
    /**
     * Notificar cambio de nivel de alerta
     */
    public static function notificarCambioNivel(Reporte $reporte, $nivelAnterior, $nivelNuevo)
    {
        if ($nivelAnterior === $nivelNuevo) {
            return;
        }

        $rolesNotificar = ['COO', 'BIE'];
        $usuarios = Usuario::whereHas('rol', function ($query) use ($rolesNotificar) {
            $query->whereIn('sigla', $rolesNotificar);
        })->where('estado', 'activo')->get();

        $mensaje = "El reporte #{$reporte->id} del estudiante {$reporte->estudiante->nombre} ha cambiado de nivel '{$nivelAnterior}' a '{$nivelNuevo}'.";

        foreach ($usuarios as $usuario) {
            Notificacion::create([
                'usuario_id' => $usuario->id,
                'reporte_id' => $reporte->id,
                'periodo_id' => $reporte->periodo_id,
                'tipo'       => 'cambio_nivel',
                'mensaje'    => $mensaje,
                'leida'      => false,
            ]);
        }
    }

    /**
     * Notificar nuevo reporte a Bienestar
     */
    public static function notificarNuevoReporte(Reporte $reporte)
    {
        $usuarios = Usuario::whereHas('rol', function ($query) {
            $query->where('sigla', 'BIE');
        })->where('estado', 'activo')->get();

        $mensaje = "📋 Nuevo reporte #{$reporte->id} - Estudiante: {$reporte->estudiante->nombre} - Tipo: " . ucfirst($reporte->tipo);

        foreach ($usuarios as $usuario) {
            Notificacion::create([
                'usuario_id' => $usuario->id,
                'reporte_id' => $reporte->id,
                'periodo_id' => $reporte->periodo_id,
                'tipo'       => 'nuevo_reporte',
                'mensaje'    => $mensaje,
                'leida'      => false,
            ]);
        }
    }
}