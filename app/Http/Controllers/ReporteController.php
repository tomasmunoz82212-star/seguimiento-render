<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\Materia;
use App\Models\Reporte;
use App\Models\Usuario;
use App\Models\Matricula;
use App\Models\ConfiguracionSistema;
use App\Models\Notificacion;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use App\Helpers\PeriodoHelper;
use Illuminate\Support\Facades\Cache;

class ReporteController extends Controller
{
    public function create()
    {
        $periodo   = Periodo::where('estado', 'activo')->orderBy('id', 'desc')->first();
        $programas = Programa::all();
        return view('reportes.nuevo', compact('periodo', 'programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'documento'   => 'required|exists:estudiantes,documento',
            'periodo_id'  => 'required|exists:periodos,id',
            'programa_id' => 'required|exists:programas,id',
            'materia_id'  => 'required|exists:materias,id',
            'tipo'        => 'required|in:academico,asistencia,comportamiento',
            'descripcion' => 'required|min:10',
        ]);

        $estudiante = Estudiante::where('documento', $request->documento)->first();

        $matriculado = $estudiante->matriculas()
            ->where('periodo_id', $request->periodo_id)
            ->exists();

        if (!$matriculado) {
            return redirect()->back()->withInput()
                ->with('error', 'El estudiante no está matriculado en el período seleccionado.');
        }

        $config = ConfiguracionSistema::first();

        // Determinar la fecha límite según el modo (prueba en minutos o normal en días)
        if ($config && $config->modo_prueba_minutos) {
            $fechaLimite = now()->addMinutes(3);      // 3 minutos para pruebas rápidas
        } else {
            $diasLimite = $config->dias_limite_seguimiento ?? 5;
            $fechaLimite = now()->addDays($diasLimite);
        }

        $reporte = Reporte::create([
            'estudiante_id'            => $estudiante->id,
            'periodo_id'               => $request->periodo_id,
            'usuario_id'               => $this->usuarioActual()->id,
            'programa_id'              => $request->programa_id,
            'materia_id'               => $request->materia_id,
            'tipo'                     => $request->tipo,
            'descripcion'              => $request->descripcion,
            'estado'                   => 'pendiente',
            'fecha_limite_seguimiento' => $fechaLimite,
            'nivel_alerta'             => 'verde',
        ]);

        // =============================================
        // Notificar a Bienestar sobre el nuevo reporte
        // =============================================
        NotificacionService::notificarNuevoReporte($reporte);

        // =============================================
        // Guardar timestamp para detectar nuevos reportes
        // =============================================
        Cache::put('ultimo_nuevo_reporte', time(), 300);

        return redirect('/mis-reportes')->with('success', 'Reporte guardado correctamente.');
    }

    /**
     * Crear notificación cuando se genera un nuevo reporte
     */
    private function crearNotificacionNuevoReporte($reporte)
    {
        // Obtener todos los usuarios con rol de Bienestar (sigla 'BIE')
        $usuariosBienestar = Usuario::whereHas('rol', function($query) {
            $query->where('sigla', 'BIE');
        })->get();

        $mensaje = "📋 Nuevo reporte #{$reporte->id} - Estudiante: {$reporte->estudiante->nombre} - Tipo: " . ucfirst($reporte->tipo);

        foreach ($usuariosBienestar as $usuario) {
            Notificacion::create([
                'usuario_id'  => $usuario->id,
                'reporte_id'  => $reporte->id,
                'periodo_id'  => $reporte->periodo_id,
                'tipo'        => 'nuevo_reporte',
                'mensaje'     => $mensaje,
                'leida'       => false,
            ]);
        }
    }

    public function misReportes(Request $request)
    {
        $usuario = $this->usuarioActual();
        if (!$usuario) {
            return redirect('/Login')->with('error', 'Debes iniciar sesión para ver tus reportes.');
        }

        $periodos = Periodo::orderBy('id', 'desc')->get();
        
        $periodoId = $request->get('periodo_id');
        if ($periodoId) {
            PeriodoHelper::setPeriodoSesion($periodoId);
        } else {
            $periodoId = PeriodoHelper::getPeriodoSesion()->id ?? null;
        }
        
        $query = Reporte::with(['estudiante', 'periodo', 'programa', 'materia'])
            ->where('usuario_id', $usuario->id);
        
        if ($periodoId) {
            $query->where('periodo_id', $periodoId);
        }
        
        $reportes = $query->orderBy('creado_en', 'desc')->get();
        $periodoSeleccionado = $periodoId ? Periodo::find($periodoId) : null;
        
        return view('reportes.mis', compact('reportes', 'periodos', 'periodoSeleccionado'));
    }

    public function show($id)
    {
        $reporte = Reporte::with(['estudiante', 'periodo', 'usuario', 'programa', 'materia'])
            ->findOrFail($id);
        return view('reportes.detalle', compact('reporte'));
    }

    public function buscarEstudiante(Request $request)
    {
        $estudiante = Estudiante::where('documento', $request->documento)->first();

        if (!$estudiante) {
            return response()->json(['found' => false]);
        }

        // Obtener el período activo
        $periodo = Periodo::where('estado', 'activo')->orderBy('id', 'desc')->first();
        
        if (!$periodo) {
            return response()->json([
                'found' => true,
                'nombre' => $estudiante->nombre,
                'correo' => $estudiante->correo,
                'error' => 'No hay período activo'
            ]);
        }

        // Obtener la matrícula del estudiante en el período activo
        $matricula = $estudiante->matriculas()
            ->where('periodo_id', $periodo->id)
            ->with('programa')
            ->first();

        $programaId = $matricula && $matricula->programa ? $matricula->programa->id : null;
        $programaNombre = $matricula && $matricula->programa ? $matricula->programa->nombre : null;

        // Obtener las materias del programa
        $materias = [];
        if ($programaId) {
            $materias = Materia::where('programa_id', $programaId)
                ->orderBy('nombre')
                ->get(['id', 'nombre']);
        }

        return response()->json([
            'found' => true,
            'nombre' => $estudiante->nombre,
            'correo' => $estudiante->correo,
            'programa_id' => $programaId,
            'programa_nombre' => $programaNombre,
            'materias' => $materias
        ]);
    }

    public function materiasPorPrograma($programa_id)
    {
        $materias = Materia::where('programa_id', $programa_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        return response()->json($materias);
    }

    public function update(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);

        $request->validate([
            'descripcion' => 'required|min:10',
        ]);

        $reporte->update([
            'descripcion' => $request->descripcion,
        ]);

        return redirect('/reportes/' . $id)->with('success', 'Reporte actualizado.');
    }

    public function destroy($id)
    {
        $usuario = $this->usuarioActual();
        $reporte = Reporte::findOrFail($id);

        if ($reporte->usuario_id !== $usuario->id || $reporte->estado !== 'pendiente') {
            return redirect('/mis-reportes')->with('error', 'No puedes eliminar este reporte.');
        }

        $reporte->delete();
        return redirect('/mis-reportes')->with('success', 'Reporte eliminado correctamente.');
    }

    private function usuarioActual()
    {
        return Usuario::where('usuario', session('usuario'))->first();
    }
}