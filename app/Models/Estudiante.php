<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table    = 'estudiantes';
    protected $fillable = ['documento', 'nombre', 'correo', 'telefono'];
    public $timestamps  = false;

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'estudiante_id');
    }
}