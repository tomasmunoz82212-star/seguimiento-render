<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::withCount('matriculas')->orderBy('id', 'desc')->get();
        return view('periodos.Index', compact('periodos'));
    }

    public function create()
    {
        return view('periodos.Nuevo');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|unique:periodos|max:20',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'archivo'      => 'required|mimes:xlsx,xls|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Cerrar todos los períodos activos anteriores
            Periodo::where('estado', 'activo')->update(['estado' => 'cerrado']);

            $periodo = Periodo::create([
                'nombre'       => $request->nombre,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin'    => $request->fecha_fin,
                'estado'       => 'activo',
            ]);

            $this->procesarExcel($request->file('archivo'), $periodo);

            DB::commit();
            return redirect('/periodos')->with('success', "Período {$periodo->nombre} creado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $periodo = Periodo::withCount('matriculas')
            ->with(['matriculas.estudiante', 'matriculas.programa'])
            ->findOrFail($id);
        return view('periodos.Editar', compact('periodo'));
    }

    public function update(Request $request, $id)
    {
        $periodo = Periodo::findOrFail($id);

        $request->validate([
            'nombre'       => 'required|max:20|unique:periodos,nombre,' . $id,
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
        ]);

        $periodo->update([
            'nombre'       => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
        ]);

        return redirect('/periodos/' . $id . '/editar')
            ->with('success', 'Período actualizado correctamente.');
    }

    public function actualizarListado(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        $periodo = Periodo::findOrFail($id);

        DB::beginTransaction();
        try {
            Matricula::where('periodo_id', $periodo->id)->delete();
            $this->procesarExcel($request->file('archivo'), $periodo);

            DB::commit();
            return redirect('/periodos/' . $id . '/editar')
                ->with('success', 'Listado actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $periodo = Periodo::findOrFail($id);

        // Primero eliminar seguimientos de bienestar relacionados a reportes del período
        \App\Models\SeguimientoBienestar::whereHas('reporte', function($q) use ($periodo) {
            $q->where('periodo_id', $periodo->id);
        })->delete();

        // Luego eliminar reportes
        \App\Models\Reporte::where('periodo_id', $periodo->id)->delete();

        // Luego eliminar matrículas
        Matricula::where('periodo_id', $periodo->id)->delete();

        // Finalmente eliminar el período
        $periodo->delete();

        return redirect('/periodos')->with('success', 'Período eliminado correctamente.');
    }

    public function estudiantes($id)
    {
        $periodo = Periodo::withCount('matriculas')
            ->with(['matriculas.estudiante', 'matriculas.programa'])
            ->findOrFail($id);
        return view('periodos.Estudiantes', compact('periodo'));
    }

    private function procesarExcel($archivo, Periodo $periodo)
    {
        $filas = Excel::toArray([], $archivo)[0];
        array_shift($filas); // Eliminar encabezado

        foreach ($filas as $fila) {
            if (empty($fila[0])) continue;

            [$documento, $nombre, $carrera, $semestre, $correo, $telefono]
                = array_pad($fila, 6, null);

            $documento = trim($documento);
            $semestre  = (int) $semestre;

            $estudiante = Estudiante::updateOrCreate(
                ['documento' => $documento],
                [
                    'documento' => $documento,
                    'nombre'    => trim($nombre),
                    'correo'    => $correo,
                    'telefono'  => $telefono,
                ]
            );

            $programa = Programa::firstOrCreate(
                ['nombre' => trim($carrera)],
                ['tipo'   => 'profesional']
            );

            Matricula::updateOrCreate(
                [
                    'estudiante_id' => $estudiante->id,
                    'periodo_id'    => $periodo->id,
                ],
                [
                    'programa_id' => $programa->id,
                    'semestre'    => $semestre,
                ]
            );
        }
    }
}