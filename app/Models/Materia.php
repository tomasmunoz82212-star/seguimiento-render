<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    public $timestamps = false;

    protected $fillable = ['nombre', 'programa_id'];

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }
}