<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class ContrasenaController extends Controller
{
    public function showChangeForm()
    {
        // Verificar si hay un usuario pendiente por cambiar contraseña
        if (!session('user_id_temp')) {
            return redirect('/dashboard');
        }
        
        return view('auth.CambiarContrasena');
    }

    public function updatePassword(Request $request)
    {
        // Validar que la sesión temporal existe
        if (!session('user_id_temp')) {
            return redirect('/Login')->with('error', 'Sesión expirada. Inicia sesión nuevamente.');
        }

        $request->validate([
            'nueva_contraseña' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
                'confirmed',
            ],
            'nueva_contraseña_confirmation' => 'required',
        ], [
            'nueva_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'nueva_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial (@ $ ! % * ? & #).',
            'nueva_contraseña.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $usuario = Usuario::find(session('user_id_temp'));
        
        if (!$usuario) {
            return redirect('/Login')->with('error', 'Sesión inválida. Inicia sesión nuevamente.');
        }

        $contrasenaInicial = $usuario->persona->documento;
        if ($request->nueva_contraseña === $contrasenaInicial) {
            return back()->with('error', 'La nueva contraseña no puede ser igual a tu número de cédula.');
        }

        if (password_verify($request->nueva_contraseña, $usuario->contraseña)) {
            return back()->with('error', 'La nueva contraseña no puede ser igual a la actual.');
        }

        $usuario->update([
            'contraseña' => bcrypt($request->nueva_contraseña),
        ]);

        session()->forget('user_id_temp');

        session([
            'usuario'         => $usuario->usuario,
            'rol'             => $usuario->rol->nombre,
            'rol_sigla'       => $usuario->rol->sigla,
            'nombre_completo' => $usuario->persona->nombre_completo,
            'correo'          => $usuario->persona->correo ?? 'No tiene correo',
            'user_id'         => $usuario->id,
            'debe_cambiar_contrasena' => false,
        ]);

        return redirect('/dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}