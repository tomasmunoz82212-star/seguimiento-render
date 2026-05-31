<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';
    public $timestamps = false;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'dias_limite_seguimiento',
        'dias_alerta_naranja',
        'dias_alerta_roja',
        'modo_prueba_minutos', 
    ];

    protected $casts = [
        'modo_prueba_minutos' => 'boolean',
    ];
}