<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\NotificacionService;

class Reporte extends Model
{
    protected $table    = 'reportes';
    public    $timestamps = false;
    const     CREATED_AT  = 'creado_en';

    protected $fillable = [
        'estudiante_id',
        'periodo_id',
        'usuario_id',
        'programa_id',
        'materia_id',
        'tipo',
        'descripcion',
        'estado',
        'fecha_limite_seguimiento',
        'nivel_alerta',
    ];

    public function estudiante() { return $this->belongsTo(Estudiante::class); }
    public function periodo()    { return $this->belongsTo(Periodo::class); }
    public function usuario()    { return $this->belongsTo(Usuario::class); }
    public function programa()   { return $this->belongsTo(Programa::class); }
    public function materia()    { return $this->belongsTo(Materia::class); }

    public function seguimientoBienestar()
    {
        return $this->hasOne(SeguimientoBienestar::class, 'reporte_id');
    }

    public function getCarreraEstudianteAttribute()
    {
        $matricula = $this->estudiante->matriculas()
            ->where('periodo_id', $this->periodo_id)
            ->with('programa')
            ->first();
        
        return $matricula && $matricula->programa 
            ? $matricula->programa->nombre 
            : 'No registrada';
    }

    /**
     * Calcular el nivel de alerta según los días restantes
     */

    public function calcularNivelAlerta()
    {
        if (!$this->fecha_limite_seguimiento) {
            return 'verde';
        }
        
        $config = ConfiguracionSistema::first();
        $modoPrueba = $config->modo_prueba_minutos ?? false;
        
        $ahoraColombia = now()->timezone('America/Bogota');
        $fechaLimiteColombia = \Carbon\Carbon::parse($this->fecha_limite_seguimiento)
                                    ->timezone('America/Bogota');
        
        if ($modoPrueba) {
            $minutosRestantes = $ahoraColombia->diffInMinutes($fechaLimiteColombia, false);
            
            if ($minutosRestantes < 0) return 'expirado';
            if ($minutosRestantes <= 1) return 'rojo';
            if ($minutosRestantes <= 2) return 'naranja';
            return 'verde';
        }
        
        // Modo normal: días
        $hoy = $ahoraColombia->copy()->startOfDay();
        $fechaLimiteDia = $fechaLimiteColombia->copy()->startOfDay();
        $diasRestantes = $hoy->diffInDays($fechaLimiteDia, false);
        
        if ($diasRestantes < 0) return 'expirado';
        
        $diasRojo = $config->dias_alerta_roja ?? 1;
        $diasNaranja = $config->dias_alerta_naranja ?? 3;
        
        if ($diasRestantes <= $diasRojo) return 'rojo';
        if ($diasRestantes <= $diasNaranja) return 'naranja';
        return 'verde';
    }

    /**
     * Actualizar el nivel de alerta y guardarlo
     */
    public function actualizarNivelAlerta()
    {
        // Solo actualizar niveles si el reporte está pendiente
        if ($this->estado !== 'pendiente') {
            return $this->nivel_alerta;
        }

        $nivelAnterior = $this->nivel_alerta;
        $nivelNuevo = $this->calcularNivelAlerta();

        if ($nivelAnterior !== $nivelNuevo) {
            $this->update(['nivel_alerta' => $nivelNuevo]);

            if ($this->estado !== 'cerrado') {
                NotificacionService::notificarCambioNivel($this, $nivelAnterior, $nivelNuevo);
            }
        }

        return $nivelNuevo;
    }

    /**
     * Devuelve la fecha límite formateada como "d/m/Y H:i".
     */
    public function getFechaLimiteLegibleAttribute()
    {
        if (!$this->fecha_limite_seguimiento) {
            return 'No definida';
        }

        return \Carbon\Carbon::parse($this->fecha_limite_seguimiento)
                    ->timezone('America/Bogota')
                    ->format('d/m/Y H:i');
    }
}