<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Usuario;
use App\Models\SeguimientoBienestar;
use App\Models\Periodo;
use App\Helpers\PeriodoHelper;
use App\Models\ObservacionSeguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BienestarController extends Controller
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
            ->whereIn('estado', ['pendiente', 'en_seguimiento']);
        
        if ($periodoId) {
            $query->where('periodo_id', $periodoId);
        }
        
        $reportes = $query->orderBy('creado_en', 'desc')->get();
        $periodoSeleccionado = $periodoId ? Periodo::find($periodoId) : null;
        
        // Si es petición AJAX, devolver solo el contenido principal
        if ($request->ajax()) {
            $html = view('bienestar.partials.content', compact('reportes', 'periodoSeleccionado'))->render();
            return response()->json(['html' => $html]);
        }
        
        return view('bienestar.Index', compact('reportes', 'periodos', 'periodoSeleccionado'));
    }

    public function show($reporteId)
    {
        $reporte = Reporte::with([
            'estudiante', 'periodo', 'programa', 'materia', 
            'usuario.persona', 
            'seguimientoBienestar.observaciones.usuario.persona'
        ])->findOrFail($reporteId);
        
        // Actualizar nivel de alerta si el reporte no está cerrado
        if ($reporte->estado !== 'cerrado') {
            $reporte->actualizarNivelAlerta();
        }
        
        $seguimiento = $reporte->seguimientoBienestar;
        return view('bienestar.Detalle', compact('reporte', 'seguimiento'));
    }

    public function store(Request $request, $reporteId)
    {
        $request->validate([
            'medio_contacto'      => 'required|in:presencial,telefono,meet,teams,whatsapp,correo',
            'observacion_inicial' => 'nullable|string|max:1000',
            'contacto_fallido'    => 'nullable|boolean',
            'motivo_no_contacto'  => 'nullable|string|max:255'
        ]);

        $reporte = Reporte::findOrFail($reporteId);
        $usuario = Usuario::where('usuario', session('usuario'))->first();

        $aspectos = [
            'dificultad_economica', 'trabaja_y_estudia', 'falta_apoyo_familiar',
            'ansiedad_estres', 'depresion_tristeza', 'baja_autoestima', 'desmotivacion',
            'problema_salud_fisica', 'problema_salud_mental',
            'conflicto_pares', 'conflicto_docentes', 'bullying_acoso',
            'dificultad_aprendizaje', 'problema_adaptacion', 'falta_habitos_estudio',
            'problema_familiar', 'responsabilidad_hogar', 'otro',
        ];

        $datosSeguimiento = [
            'reporte_id' => $reporteId,
            'usuario_id' => $usuario->id,
        ];
        foreach ($aspectos as $a) {
            $datosSeguimiento[$a] = $request->has($a) ? 1 : 0;
        }
        $datosSeguimiento['detalle_otro'] = $request->detalle_otro;
        $datosSeguimiento['estado'] = 'en_proceso';

        DB::transaction(function () use ($request, $reporte, $usuario, $datosSeguimiento) {
            $seguimiento = SeguimientoBienestar::updateOrCreate(
                ['reporte_id' => $reporte->id],
                $datosSeguimiento
            );

            $contactoFallido = $request->boolean('contacto_fallido');
            $tieneObservacion = $request->filled('observacion_inicial');

            if ($tieneObservacion || $contactoFallido) {
                $textoObservacion = $tieneObservacion 
                    ? $request->observacion_inicial 
                    : 'Intento de contacto fallido.';

                ObservacionSeguimiento::create([
                    'seguimiento_id'     => $seguimiento->id,
                    'usuario_id'         => $usuario->id,
                    'medio_contacto'     => $request->medio_contacto,
                    'contacto_fallido'   => $contactoFallido,
                    'motivo_no_contacto' => $request->motivo_no_contacto,
                    'observacion'        => $textoObservacion,
                ]);
            }

            if ($reporte->estado === 'pendiente') {
                $reporte->update([
                    'estado' => 'en_seguimiento',
                    'nivel_alerta' => 'verde',
                ]);

                Cache::put('ultimo_nuevo_reporte', time(), 300);
            }
        });

        return redirect('/bienestar/' . $reporteId)
            ->with('success', 'Seguimiento registrado correctamente.');
    }

    public function cerrar(Request $request, $reporteId)
    {
        $request->validate([
            'razon_cierre' => 'required|string|min:5|max:500',
        ]);

        $reporte = Reporte::findOrFail($reporteId);
        $reporte->update(['estado' => 'cerrado']);

        Cache::put('ultimo_nuevo_reporte', time(), 300);

        if ($reporte->seguimientoBienestar) {
            $reporte->seguimientoBienestar->update([
                'estado' => 'cerrado',
                'razon_cierre' => $request->razon_cierre,
            ]);
        } else {
            $usuario = Usuario::where('usuario', session('usuario'))->first();
            SeguimientoBienestar::create([
                'reporte_id' => $reporteId,
                'usuario_id' => $usuario->id,
                'estado' => 'cerrado',
                'razon_cierre' => $request->razon_cierre,
            ]);
        }

        return redirect('/bienestar/' . $reporteId)
            ->with('success', 'Caso cerrado correctamente.');
    }

    public function agregarObservacion(Request $request, $seguimientoId)
    {
        $request->validate([
            'medio_contacto'    => 'required|in:presencial,telefono,meet,teams,whatsapp,correo',
            'observacion'       => 'required|string|max:1000',
            'contacto_fallido'  => 'nullable|boolean',
            'motivo_no_contacto'=> 'nullable|string|max:255'
        ]);

        $seguimiento = SeguimientoBienestar::findOrFail($seguimientoId);
        $usuario = Usuario::where('usuario', session('usuario'))->first();

        ObservacionSeguimiento::create([
            'seguimiento_id'     => $seguimiento->id,
            'usuario_id'         => $usuario->id,
            'medio_contacto'     => $request->medio_contacto,
            'contacto_fallido'   => $request->boolean('contacto_fallido'),
            'motivo_no_contacto' => $request->motivo_no_contacto,
            'observacion'        => $request->observacion
        ]);

        return redirect('/bienestar/' . $seguimiento->reporte_id)
            ->with('success', 'Observación agregada correctamente.');
    }
}