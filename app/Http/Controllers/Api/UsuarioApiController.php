<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'primer_nombre'    => 'required|max:50',
                'segundo_nombre'   => 'nullable|max:50',
                'primer_apellido'  => 'required|max:50',
                'segundo_apellido' => 'nullable|max:50',
                'documento'        => 'required|unique:personas,documento|max:20',
                'correo'           => 'nullable|email|max:100',
                'telefono'         => 'nullable|max:20',
                'usuario'          => 'required|unique:usuarios,usuario|max:50',
                'contraseña'       => 'required|min:6',
                'rol_id'           => 'required|exists:roles,id',
            ]);

            DB::transaction(function () use ($request) {
                $persona = Persona::create($request->only([
                    'primer_nombre', 'segundo_nombre', 'primer_apellido',
                    'segundo_apellido', 'documento', 'correo', 'telefono'
                ]));

                Usuario::create([
                    'persona_id' => $persona->id,
                    'usuario'    => $request->usuario,
                    'contraseña' => bcrypt($request->contraseña),
                    'rol_id'     => $request->rol_id,
                    'estado'     => 'activo',
                ]);
            });

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'usuario' => $request->usuario
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el usuario: ' . $e->getMessage()
            ], 500);
        }
    }
}