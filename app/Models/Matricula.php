<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $table    = 'matriculas';
    protected $fillable = ['estudiante_id', 'periodo_id', 'programa_id', 'semestre'];
    public $timestamps  = false;

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }
}