<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table    = 'periodos';
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'estado'];
    public $timestamps  = false;

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'periodo_id');
    }
}