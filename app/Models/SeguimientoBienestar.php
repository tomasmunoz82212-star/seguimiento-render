<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoBienestar extends Model
{
    protected $table = 'seguimientos_bienestar';
    public $timestamps = false;

    protected $fillable = [
        'reporte_id',
        'usuario_id',
        'dificultad_economica',
        'trabaja_y_estudia',
        'falta_apoyo_familiar',
        'ansiedad_estres',
        'depresion_tristeza',
        'baja_autoestima',
        'desmotivacion',
        'problema_salud_fisica',
        'problema_salud_mental',
        'conflicto_pares',
        'conflicto_docentes',
        'bullying_acoso',
        'dificultad_aprendizaje',
        'problema_adaptacion',
        'falta_habitos_estudio',
        'problema_familiar',
        'responsabilidad_hogar',
        'otro',
        'detalle_otro',
        'razon_cierre',
        'estado',
    ];

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'reporte_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function observaciones()
    {
        return $this->hasMany(ObservacionSeguimiento::class, 'seguimiento_id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Accesor: devuelve un array con las etiquetas de los aspectos marcados como verdaderos.
     * Se usa en la vista para no tener que escribir lógica PHP.
     */
    public function getAspectosActivosConEtiquetasAttribute()
    {
        $aspectos = [
            'dificultad_economica'   => 'Dificultad económica',
            'trabaja_y_estudia'      => 'Trabaja y estudia',
            'falta_apoyo_familiar'   => 'Falta apoyo familiar',
            'ansiedad_estres'        => 'Ansiedad o estrés',
            'depresion_tristeza'     => 'Depresión o tristeza',
            'baja_autoestima'        => 'Baja autoestima',
            'desmotivacion'          => 'Desmotivación',
            'problema_salud_fisica'  => 'Problema salud física',
            'problema_salud_mental'  => 'Problema salud mental',
            'conflicto_pares'        => 'Conflicto con compañeros',
            'conflicto_docentes'     => 'Conflicto con docentes',
            'bullying_acoso'         => 'Bullying o acoso',
            'dificultad_aprendizaje' => 'Dificultad de aprendizaje',
            'problema_adaptacion'    => 'Problema de adaptación',
            'falta_habitos_estudio'  => 'Falta hábitos de estudio',
            'problema_familiar'      => 'Problema familiar',
            'responsabilidad_hogar'  => 'Responsabilidades del hogar',
            'otro'                   => 'Otro',
        ];

        $activos = [];
        foreach ($aspectos as $campo => $etiqueta) {
            if ($this->$campo) {
                $activos[$campo] = $etiqueta;
            }
        }
        return $activos;
    }
}