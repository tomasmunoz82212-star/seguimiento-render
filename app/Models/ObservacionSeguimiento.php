<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservacionSeguimiento extends Model
{
    protected $table = 'observaciones_seguimiento';
    public $timestamps = true;

    protected $fillable = [
        'seguimiento_id',
        'usuario_id',
        'medio_contacto',
        'contacto_fallido',
        'motivo_no_contacto',
        'observacion'
    ];

    protected $casts = [
        'contacto_fallido' => 'boolean'
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoBienestar::class, 'seguimiento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}