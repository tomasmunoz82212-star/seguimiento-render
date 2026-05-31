<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Reporte;
use App\Models\Programa;
use App\Models\Matricula;
use App\Models\Rol;
use App\Helpers\PeriodoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnaliticaController extends Controller
{
    /**
     * Vista principal de Analítica y Reportes
     */
    public function index(Request $request)
    {
        $roles = Rol::all();
        $periodos = Periodo::orderBy('id', 'desc')->get();
        
        $periodoId = $request->get('periodo_id');
        if ($periodoId) {
            PeriodoHelper::setPeriodoSesion($periodoId);
        } else {
            $periodoId = PeriodoHelper::getPeriodoIdSesion();
        }
        
        $periodoSeleccionado = $periodoId ? Periodo::find($periodoId) : null;
        
        return view('analitica.Index', compact('roles', 'periodos', 'periodoSeleccionado'));
    }

    /**
     * Generar PDF con reporte general de alertas
     */
    public function generarReporteGeneralPDF(Request $request)
    {
        $periodoId = $request->query('periodo_id');
        
        if (!$periodoId) {
            $periodo = Periodo::where('estado', 'activo')->first();
            $periodoId = $periodo ? $periodo->id : null;
        }
        
        $periodo = $periodoId ? Periodo::find($periodoId) : null;
        
        $alertasQuery = Reporte::with(['estudiante', 'periodo', 'programa', 'materia', 'usuario.persona']);
        
        if ($periodoId) {
            $alertasQuery->where('periodo_id', $periodoId);
        }
        
        $alertas = $alertasQuery->orderBy('creado_en', 'desc')->get();
        
        $totalAlertas = $alertas->count();
        
        $alertasPorEstado = [
            'pendiente' => $alertas->where('estado', 'pendiente')->count(),
            'en_seguimiento' => $alertas->where('estado', 'en_seguimiento')->count(),
            'cerrado' => $alertas->where('estado', 'cerrado')->count(),
        ];
        
        $alertasPorTipo = [
            'academico' => $alertas->where('tipo', 'academico')->count(),
            'asistencia' => $alertas->where('tipo', 'asistencia')->count(),
            'comportamiento' => $alertas->where('tipo', 'comportamiento')->count(),
        ];
        
        // Calcular máximo para gráfico de tipos
        $maxTipo = max(array_values($alertasPorTipo));
        if ($maxTipo == 0) $maxTipo = 1;
        
        $alertasPorPrograma = [];
        $programas = Programa::all();
        foreach ($programas as $programa) {
            $total = $alertas->where('programa_id', $programa->id)->count();
            if ($total > 0) {
                $alertasPorPrograma[] = [
                    'nombre' => $programa->nombre,
                    'total' => $total
                ];
            }
        }
        
        usort($alertasPorPrograma, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        // Calcular máximo para gráfico de programas
        $maxPrograma = !empty($alertasPorPrograma) ? max(array_column($alertasPorPrograma, 'total')) : 1;
        
        $alertasPendientes = $alertas->where('estado', 'pendiente');
        $alertasSeguimiento = $alertas->where('estado', 'en_seguimiento');
        $alertasCerrados = $alertas->where('estado', 'cerrado');
        
        $this->cargarCarreraEnAlertas($alertasPendientes, $periodoId);
        $this->cargarCarreraEnAlertas($alertasSeguimiento, $periodoId);
        $this->cargarCarreraEnAlertas($alertasCerrados, $periodoId);
        
        $fechaGeneracion = now()->format('d/m/Y');
        $nombrePeriodo = $periodo ? $periodo->nombre : 'Todos los períodos';
        
        $pdf = Pdf::loadView('analitica.pdf_general', compact(
            'totalAlertas',
            'alertasPorEstado',
            'alertasPorTipo',
            'alertasPorPrograma',
            'alertasPendientes',
            'alertasSeguimiento',
            'alertasCerrados',
            'fechaGeneracion',
            'nombrePeriodo',
            'periodoId',
            'maxTipo',
            'maxPrograma'
        ));
        
        $filename = $periodo 
            ? 'reporte_alertas_' . $periodo->nombre . '_' . now()->format('Ymd_His') . '.pdf'
            : 'reporte_alertas_general_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Carga el nombre de la carrera para cada alerta
     */
    private function cargarCarreraEnAlertas($alertas, $periodoId)
    {
        foreach ($alertas as $alerta) {
            $periodoABuscar = $periodoId ?? $alerta->periodo_id;
            
            $matricula = Matricula::where('estudiante_id', $alerta->estudiante_id)
                ->where('periodo_id', $periodoABuscar)
                ->with('programa')
                ->first();
            
            $alerta->carrera_nombre = $matricula && $matricula->programa 
                ? $matricula->programa->nombre 
                : 'No registrada';
        }
    }

    /**
     * Vista para seleccionar períodos a comparar
     */
    public function comparativaPeriodos(Request $request)
    {
        $roles = Rol::all();
        $periodos = Periodo::orderBy('id', 'desc')->get();
        
        if ($periodos->count() < 2) {
            return view('analitica.Comparativa', [
                'roles' => $roles,
                'periodos' => $periodos,
                'error' => '⚠️ No hay suficientes períodos para realizar una comparativa. Se necesitan al menos dos períodos académicos.'
            ]);
        }
        
        return view('analitica.Comparativa', compact('roles', 'periodos'));
    }

    /**
     * Generar PDF con comparativa de períodos
     */
    public function generarComparativaPDF(Request $request)
    {
        $request->validate([
            'periodo_anterior_id' => 'required|exists:periodos,id',
            'periodo_actual_id' => 'required|exists:periodos,id|different:periodo_anterior_id',
        ]);
        
        $periodoAnterior = Periodo::findOrFail($request->periodo_anterior_id);
        $periodoActual = Periodo::findOrFail($request->periodo_actual_id);
        
        // Asegurar que el período anterior es más antiguo
        if ($periodoAnterior->fecha_inicio > $periodoActual->fecha_inicio) {
            $temp = $periodoAnterior;
            $periodoAnterior = $periodoActual;
            $periodoActual = $temp;
        }
        
        // Obtener estudiantes matriculados en cada período
        $matriculadosAnterior = Matricula::where('periodo_id', $periodoAnterior->id)
            ->with(['estudiante', 'programa'])
            ->get()
            ->keyBy('estudiante_id');
        
        $matriculadosActual = Matricula::where('periodo_id', $periodoActual->id)
            ->with(['estudiante', 'programa'])
            ->get()
            ->keyBy('estudiante_id');
        
        // Analizar cada estudiante del período anterior
        $desertores = [];
        $graduados = [];
        $cambiosCarrera = [];
        $estudiantesActivos = [];
        
        // Obtener semestre máximo por tipo de programa
        $semestreMaximoPorPrograma = [];
        foreach ($matriculadosAnterior as $matricula) {
            $programaId = $matricula->programa_id;
            if (!isset($semestreMaximoPorPrograma[$programaId])) {
                $programa = $matricula->programa;
                $semestreMaximoPorPrograma[$programaId] = $programa->tipo === 'profesional' ? 10 : 6;
            }
        }
        
        foreach ($matriculadosAnterior as $estudianteId => $matriculaAnterior) {
            $estudiante = $matriculaAnterior->estudiante;
            $programaAnterior = $matriculaAnterior->programa;
            $semestreAnterior = $matriculaAnterior->semestre;
            $semestreMaximo = $semestreMaximoPorPrograma[$programaAnterior->id];
            
            if (!isset($matriculadosActual[$estudianteId])) {
                // Estudiante no está en el período actual
                if ($semestreAnterior >= $semestreMaximo) {
                    // Posible graduado (completó el plan)
                    $graduados[] = [
                        'estudiante' => $estudiante,
                        'documento' => $estudiante->documento,
                        'programa' => $programaAnterior->nombre,
                        'semestre' => $semestreAnterior,
                    ];
                } else {
                    // Desertor (no completó y no continuó)
                    $reportes = Reporte::where('estudiante_id', $estudianteId)
                        ->where('periodo_id', $periodoAnterior->id)
                        ->get();
                    
                    $desertores[] = [
                        'estudiante' => $estudiante,
                        'documento' => $estudiante->documento,
                        'programa' => $programaAnterior->nombre,
                        'semestre' => $semestreAnterior,
                        'total_reportes' => $reportes->count(),
                        'reportes_cerrados' => $reportes->where('estado', 'cerrado')->count(),
                        'reportes_pendientes' => $reportes->where('estado', 'pendiente')->count(),
                        'reportes_seguimiento' => $reportes->where('estado', 'en_seguimiento')->count(),
                    ];
                }
            } else {
                $matriculaActual = $matriculadosActual[$estudianteId];
                $programaActual = $matriculaActual->programa;
                
                if ($programaAnterior->id !== $programaActual->id) {
                    // Cambió de carrera
                    $reportes = Reporte::where('estudiante_id', $estudianteId)
                        ->where('periodo_id', $periodoAnterior->id)
                        ->get();
                    
                    $cambiosCarrera[] = [
                        'estudiante' => $estudiante,
                        'documento' => $estudiante->documento,
                        'programa_anterior' => $programaAnterior->nombre,
                        'programa_actual' => $programaActual->nombre,
                        'semestre_anterior' => $semestreAnterior,
                        'semestre_actual' => $matriculaActual->semestre,
                        'total_reportes' => $reportes->count(),
                    ];
                } else {
                    // Continúa activo
                    $estudiantesActivos[] = [
                        'estudiante' => $estudiante,
                        'documento' => $estudiante->documento,
                        'programa' => $programaAnterior->nombre,
                        'semestre_anterior' => $semestreAnterior,
                        'semestre_actual' => $matriculaActual->semestre,
                    ];
                }
            }
        }
        
        // Encontrar NUEVOS estudiantes (están en actual pero no en anterior)
        $nuevosEstudiantes = [];
        foreach ($matriculadosActual as $estudianteId => $matriculaActual) {
            if (!isset($matriculadosAnterior[$estudianteId])) {
                $estudiante = $matriculaActual->estudiante;
                $programaActual = $matriculaActual->programa;
                
                $nuevosEstudiantes[] = [
                    'estudiante' => $estudiante,
                    'documento' => $estudiante->documento,
                    'programa' => $programaActual->nombre,
                    'semestre' => $matriculaActual->semestre,
                ];
            }
        }
        
        // Agrupar desertores por programa
        $desertoresPorPrograma = [];
        foreach ($desertores as $desertor) {
            $programa = $desertor['programa'];
            if (!isset($desertoresPorPrograma[$programa])) {
                $desertoresPorPrograma[$programa] = [];
            }
            $desertoresPorPrograma[$programa][] = $desertor;
        }
        
        // Agrupar graduados por programa (solo conteo, sin detalle)
        $graduadosPorPrograma = [];
        foreach ($graduados as $graduado) {
            $programa = $graduado['programa'];
            if (!isset($graduadosPorPrograma[$programa])) {
                $graduadosPorPrograma[$programa] = 0;
            }
            $graduadosPorPrograma[$programa]++;
        }
        
        // Agrupar nuevos estudiantes por programa
        $nuevosPorPrograma = [];
        foreach ($nuevosEstudiantes as $nuevo) {
            $programa = $nuevo['programa'];
            if (!isset($nuevosPorPrograma[$programa])) {
                $nuevosPorPrograma[$programa] = [];
            }
            $nuevosPorPrograma[$programa][] = $nuevo;
        }
        
        $totalAnterior = $matriculadosAnterior->count();
        $totalActual = $matriculadosActual->count();
        $totalDesertores = count($desertores);
        $totalGraduados = count($graduados);
        $totalCambiosCarrera = count($cambiosCarrera);
        $totalActivos = count($estudiantesActivos);
        $totalNuevos = count($nuevosEstudiantes);
        
        $porcentajeRetencion = $totalAnterior > 0 
            ? round((($totalActivos + $totalGraduados) / $totalAnterior) * 100, 1)
            : 0;
        
        $fechaGeneracion = now()->format('d/m/Y');
        
        $pdf = Pdf::loadView('analitica.pdf_comparativa', compact(
            'periodoAnterior',
            'periodoActual',
            'totalAnterior',
            'totalActual',
            'totalDesertores',
            'totalGraduados',
            'totalCambiosCarrera',
            'totalActivos',
            'totalNuevos',
            'porcentajeRetencion',
            'desertoresPorPrograma',
            'graduadosPorPrograma',
            'nuevosPorPrograma',
            'cambiosCarrera',
            'fechaGeneracion'
        ));
        
        return $pdf->download('comparativa_' . $periodoAnterior->nombre . '_vs_' . $periodoActual->nombre . '.pdf');
    }
}