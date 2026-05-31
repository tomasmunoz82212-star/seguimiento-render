<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    public $timestamps = true; // Cambiar a true

    protected $fillable = [
        'persona_id',
        'usuario',
        'contraseña',
        'rol_id',
        'estado',
    ];

    protected $hidden = [
        'contraseña',
    ];

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function getNombreCompletoAttribute()
    {
        if ($this->persona) {
            $nombre = $this->persona->primer_nombre;
            if ($this->persona->segundo_nombre) {
                $nombre .= ' ' . $this->persona->segundo_nombre;
            }
            $nombre .= ' ' . $this->persona->primer_apellido;
            if ($this->persona->segundo_apellido) {
                $nombre .= ' ' . $this->persona->segundo_apellido;
            }
            return $nombre;
        }
        return $this->usuario;
    }
}