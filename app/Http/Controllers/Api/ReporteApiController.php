<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reporte;

class ReporteApiController extends Controller
{
    public function nivelAlerta($id)
    {
        $reporte = Reporte::findOrFail($id);
        
        if ($reporte->estado !== 'cerrado') {
            $reporte->actualizarNivelAlerta();
        }
        
        return response()->json([
            'nivel_alerta'         => $reporte->nivel_alerta,
            'fecha_limite_legible' => $reporte->fecha_limite_legible,
        ]);
    }

    public function actualizarTodosLosNiveles()
    {
        $reportes = Reporte::where('estado', 'pendiente')->get();
        $actualizados = 0;

        foreach ($reportes as $reporte) {
            $nivelAnterior = $reporte->nivel_alerta;
            $reporte->actualizarNivelAlerta();
            if ($nivelAnterior !== $reporte->nivel_alerta) {
                $actualizados++;
            }
        }

        return response()->json(['actualizados' => $actualizados]);
    }
}