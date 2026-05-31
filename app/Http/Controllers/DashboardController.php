<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Reporte;
use App\Models\Matricula;
use App\Models\Materia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodo = Periodo::where('estado', 'activo')->first();

        if (session('rol') === 'docente') {
            return redirect('/nuevo-reporte');
        }

        if (!$periodo) {
            if ($request->ajax()) {
                $html = view('dashboard.partials.content', [
                    'totalReportes' => 0,
                    'pendientes' => 0,
                    'enSeguimiento' => 0,
                    'cerrados' => 0,
                    'tipoAcademico' => 0,
                    'tipoAsistencia' => 0,
                    'tipoComportamiento' => 0,
                    'porCarrera' => [],
                    'reportesPorSemestre' => [],
                    'reportesPorMateria' => [],
                ])->render();
                return response()->json(['html' => $html]);
            }
            return view('dashboard.Dashboard', ['periodo' => null]);
        }

        $reportes = Reporte::with(['estudiante.matriculas' => function($q) use ($periodo) {
            $q->where('periodo_id', $periodo->id)->with('programa');
        }, 'materia'])
            ->where('periodo_id', $periodo->id)
            ->get();

        // =============================================
        // 1. ESTADÍSTICAS BÁSICAS
        // =============================================
        $totalReportes    = $reportes->count();
        $pendientes       = $reportes->where('estado', 'pendiente')->count();
        $enSeguimiento    = $reportes->where('estado', 'en_seguimiento')->count();
        $cerrados         = $reportes->where('estado', 'cerrado')->count();

        $tipoAcademico      = $reportes->where('tipo', 'academico')->count();
        $tipoAsistencia     = $reportes->where('tipo', 'asistencia')->count();
        $tipoComportamiento = $reportes->where('tipo', 'comportamiento')->count();

        // =============================================
        // 2. REPORTES POR CARRERA
        // =============================================
        $porCarrera = $reportes->groupBy(function($reporte) {
            $matricula = $reporte->estudiante->matriculas->first();
            return $matricula && $matricula->programa 
                ? $matricula->programa->nombre 
                : 'Sin carrera asignada';
        })->map(fn($g) => (int) $g->count());

        // =============================================
        // 3. REPORTES POR SEMESTRE (vinculado a carrera)
        // =============================================
        $semestreMaximoPorPrograma = [];
        foreach ($reportes as $reporte) {
            $matricula = $reporte->estudiante->matriculas->first();
            if ($matricula && $matricula->programa) {
                $programaId = $matricula->programa->id;
                if (!isset($semestreMaximoPorPrograma[$programaId])) {
                    $semestreMaximoPorPrograma[$programaId] = $matricula->programa->tipo === 'profesional' ? 10 : 6;
                }
            }
        }

        // Inicializar array para semestres (1 al 10)
        $reportesPorSemestre = array_fill(1, 10, 0);

        foreach ($reportes as $reporte) {
            $matricula = $reporte->estudiante->matriculas->first();
            if ($matricula && $matricula->semestre) {
                $semestre = (int)$matricula->semestre;
                $programaId = $matricula->programa_id;
                $maxSemestre = $semestreMaximoPorPrograma[$programaId] ?? 10;
                
                if ($semestre <= $maxSemestre) {
                    $reportesPorSemestre[$semestre]++;
                }
            }
        }

        // =============================================
        // 4. REPORTES POR MATERIA (Top 10)
        // =============================================
        $reportesPorMateria = $reportes->whereNotNull('materia_id')
            ->groupBy('materia_id')
            ->map(function($grupo) {
                $materia = $grupo->first()->materia;
                return [
                    'nombre' => $materia ? $materia->nombre : 'Sin nombre',
                    'programa' => $materia && $materia->programa ? $materia->programa->nombre : 'Sin programa',
                    'total' => $grupo->count()
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        // =============================================
        // RESPUESTA AJAX
        // =============================================
        if ($request->ajax()) {
            $html = view('dashboard.partials.content', compact(
                'totalReportes', 'pendientes', 'enSeguimiento', 'cerrados',
                'tipoAcademico', 'tipoAsistencia', 'tipoComportamiento',
                'porCarrera', 'reportesPorSemestre', 'reportesPorMateria'
            ))->render();
            return response()->json(['html' => $html]);
        }

        return view('dashboard.Dashboard', compact(
            'periodo',
            'totalReportes', 'pendientes', 'enSeguimiento', 'cerrados',
            'tipoAcademico', 'tipoAsistencia', 'tipoComportamiento',
            'porCarrera', 'reportesPorSemestre', 'reportesPorMateria'
        ));
    }
}