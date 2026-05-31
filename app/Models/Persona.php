<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';
    public $timestamps = false;

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'documento',
        'correo',
        'telefono',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->primer_apellido,
            $this->segundo_apellido,
        ])));
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'persona_id');
    }
}