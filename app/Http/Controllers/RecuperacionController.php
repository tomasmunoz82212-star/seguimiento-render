<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\CodigoRecuperacionMail;
use Carbon\Carbon;

class RecuperacionController extends Controller
{
    /**
     * Mostrar formulario de recuperación o enviar código automáticamente
     */
    public function showFormulario()
    {
        // Obtener el último usuario que intentó login
        $usuarioIntentado = session('ultimo_usuario_intentado');
        
        if ($usuarioIntentado) {
            $user = Usuario::where('usuario', $usuarioIntentado)->first();
            if ($user && $user->persona && $user->persona->correo) {
                // Enviar código automáticamente
                $request = new \Illuminate\Http\Request(['correo' => $user->persona->correo]);
                return $this->enviarCodigo($request);
            }
        }
        
        // Si no hay usuario en sesión, mostrar formulario
        return view('auth.recuperar-contrasena');
    }

    /**
     * Enviar código de verificación al correo
     */
    public function enviarCodigo(Request $request)
    {
        $correo = $request->input('correo');
        
        if (!$correo) {
            return redirect('/recuperar-contrasena')->with('error', 'No se pudo identificar tu correo. Intenta nuevamente.');
        }

        $persona = Persona::where('correo', $correo)->first();

        if (!$persona) {
            return redirect('/recuperar-contrasena')->with('error', 'No existe una cuenta asociada a este correo electrónico.');
        }

        $usuario = $persona->usuarios->first();

        if (!$usuario) {
            return redirect('/recuperar-contrasena')->with('error', 'No existe una cuenta activa para este correo.');
        }

        // Generar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar en la tabla password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $correo],
            [
                'token' => bcrypt($codigo),
                'codigo' => $codigo,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(15)
            ]
        );

        // Enviar correo con logging
        try {
            // Verificar que la configuración de mail existe
            \Log::info('Intentando enviar correo a: ' . $correo);
            \Log::info('Configuración MAIL_HOST: ' . env('MAIL_HOST'));
            \Log::info('Configuración MAIL_PORT: ' . env('MAIL_PORT'));
            
            Mail::to($correo)->send(new CodigoRecuperacionMail($codigo, $persona->primer_nombre));
            
            \Log::info('Correo enviado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error enviando correo: ' . $e->getMessage());
            return redirect('/recuperar-contrasena')->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }

        session(['reset_email' => $correo]);

        return redirect('/validar-codigo')->with('success', 'Se ha enviado un código de verificación a tu correo electrónico.');
    }

    /**
     * Mostrar formulario para validar código
     */
    public function showValidarCodigo()
    {
        if (!session('reset_email')) {
            return redirect('/recuperar-contrasena');
        }

        return view('auth.validar-codigo');
    }

    /**
     * Validar el código ingresado
     */
    public function validarCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:6'
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect('/recuperar-contrasena')->with('error', 'Sesión expirada. Inicia nuevamente el proceso.');
        }

        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$reset) {
            return back()->with('error', 'Código inválido. Solicita uno nuevo.');
        }

        // Validar expiración
        if (Carbon::parse($reset->expires_at)->isPast()) {
            return back()->with('error', 'El código ha expirado. Solicita uno nuevo.');
        }

        // Validar código
        if ($reset->codigo !== $request->codigo) {
            return back()->with('error', 'Código incorrecto. Intenta nuevamente.');
        }

        // Código válido, redirigir a cambiar contraseña
        session(['reset_validated' => true]);

        return redirect('/cambiar-contrasena-recuperacion');
    }

    /**
     * Mostrar formulario para nueva contraseña
     */
    public function showNuevaContrasena()
    {
        if (!session('reset_validated') || !session('reset_email')) {
            return redirect('/recuperar-contrasena')->with('error', 'Acceso no autorizado.');
        }

        return view('auth.nueva-contrasena');
    }

    /**
     * Actualizar contraseña
     */
    public function actualizarContrasena(Request $request)
    {
        if (!session('reset_validated') || !session('reset_email')) {
            return redirect('/recuperar-contrasena')->with('error', 'Acceso no autorizado.');
        }

        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/'
            ]
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial (@ $ ! % * ? & #).',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        $email = session('reset_email');

        $persona = Persona::where('correo', $email)->first();

        if (!$persona) {
            return redirect('/recuperar-contrasena')->with('error', 'Usuario no encontrado.');
        }

        $usuario = $persona->usuarios->first();

        if (!$usuario) {
            return redirect('/recuperar-contrasena')->with('error', 'Usuario no encontrado.');
        }

        // Validar que no sea la cédula
        if ($request->password === $persona->documento) {
            return back()->with('error', 'La nueva contraseña no puede ser igual a tu número de cédula.');
        }

        // Validar que no sea la misma contraseña actual
        if (Hash::check($request->password, $usuario->contraseña)) {
            return back()->with('error', 'La nueva contraseña no puede ser igual a la actual.');
        }

        // Actualizar contraseña
        $usuario->update([
            'contraseña' => Hash::make($request->password)
        ]);

        // Limpiar sesión y tokens
        session()->forget(['reset_email', 'reset_validated', 'ultimo_usuario_intentado']);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect('/Login')->with('success', 'Contraseña actualizada correctamente. Inicia sesión con tu nueva contraseña.');
    }

    /**
     * Reenviar código
     */
    public function reenviarCodigo()
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect('/recuperar-contrasena')->with('error', 'Sesión expirada. Inicia nuevamente el proceso.');
        }

        $persona = Persona::where('correo', $email)->first();

        if (!$persona) {
            return redirect('/recuperar-contrasena')->with('error', 'Usuario no encontrado.');
        }

        // Generar nuevo código
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Actualizar token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => bcrypt($codigo),
                'codigo' => $codigo,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(15)
            ]
        );

        // Enviar correo
        try {
            Mail::to($email)->send(new CodigoRecuperacionMail($codigo, $persona->primer_nombre));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reenviar el correo.');
        }

        return back()->with('success', 'Se ha reenviado un nuevo código a tu correo.');
    }
}