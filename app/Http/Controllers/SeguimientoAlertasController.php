<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Estudiante;
use App\Models\Programa;
use App\Models\Periodo;
use App\Helpers\PeriodoHelper;
use Illuminate\Http\Request;

class SeguimientoAlertasController extends Controller
{
    public function index(Request $request)
    {
        $periodos = Periodo::orderBy('id', 'desc')->get();
        
        $periodoId = $request->get('periodo_id');
        if ($periodoId) {
            PeriodoHelper::setPeriodoSesion($periodoId);
        } else {
            $periodoId = PeriodoHelper::getPeriodoSesion()->id ?? null;
        }
        
        $query = Reporte::with(['estudiante', 'periodo', 'programa', 'seguimientoBienestar'])
            ->orderBy('creado_en', 'desc');
        
        if ($periodoId) {
            $query->where('periodo_id', $periodoId);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('programa_id')) {
            $query->where('programa_id', $request->programa_id);
        }
        
        $reportes = $query->get();
        
        $estudiantes = $reportes->groupBy('estudiante_id')->map(function ($reportesEstudiante) {
            $primero = $reportesEstudiante->first();
            return [
                'estudiante'      => $primero->estudiante,
                'documento'       => $primero->estudiante->documento,
                'total'           => $reportesEstudiante->count(),
                'pendientes'      => $reportesEstudiante->where('estado', 'pendiente')->count(),
                'en_seguimiento'  => $reportesEstudiante->where('estado', 'en_seguimiento')->count(),
                'cerrados'        => $reportesEstudiante->where('estado', 'cerrado')->count(),
                'ultimo_reporte'  => $reportesEstudiante->first()->creado_en,
                'con_bienestar'   => $reportesEstudiante->filter(fn($r) => $r->seguimientoBienestar)->count(),
            ];
        })->sortByDesc('pendientes');
        
        $programas = Programa::all();
        $periodoSeleccionado = $periodoId ? Periodo::find($periodoId) : null;
        
        // Si es petición AJAX, devolver solo el contenido principal
        if ($request->ajax()) {
            $html = view('seguimiento.partials.content', compact('estudiantes', 'periodoSeleccionado'))->render();
            return response()->json(['html' => $html]);
        }
        
        return view('seguimiento.Index', compact('estudiantes', 'programas', 'periodos', 'periodoSeleccionado'));
    }

    public function show(Request $request, $documento)
    {
        $estudiante = Estudiante::where('documento', $documento)->firstOrFail();
        $periodoId = $request->get('periodo_id');
        
        $query = Reporte::with([
            'periodo', 'programa', 'materia', 'usuario.persona',
            'seguimientoBienestar.observaciones.usuario.persona'
        ])
            ->where('estudiante_id', $estudiante->id)
            ->orderBy('creado_en', 'desc');
        
        if ($periodoId) {
            $query->where('periodo_id', $periodoId);
        }
        
        $reportes = $query->get();
        
        // Actualizar nivel de alerta para cada reporte no cerrado
        foreach ($reportes as $reporte) {
            if ($reporte->estado !== 'cerrado') {
                $reporte->actualizarNivelAlerta();
            }
        }
        
        $periodoSeleccionado = $periodoId ? Periodo::find($periodoId) : null;
        
        return view('seguimiento.Detalle', compact('estudiante', 'reportes', 'periodoSeleccionado'));
    }
}