<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'usuario_id',
        'reporte_id',
        'periodo_id',
        'tipo',
        'mensaje',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'reporte_id');
    }

    // NUEVA RELACIÓN
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function getTiempoLegibleAttribute()
    {
        return \Carbon\Carbon::parse($this->created_at)
            ->timezone('America/Bogota')
            ->diffForHumans();
    }
}