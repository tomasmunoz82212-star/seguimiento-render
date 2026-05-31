<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Persona;
use App\Models\Usuario;
use App\Helpers\UsuarioHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UsuarioController extends Controller
{
    // ── VISTAS ──────────────────────────────────────────

    public function showLogin()
    {
        if (session('usuario')) return redirect('/dashboard');
        return view('auth.Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario'    => 'required',
            'contraseña' => 'required',
        ]);

        $user = Usuario::with(['rol', 'persona'])->where('usuario', $request->usuario)->first();

        if (!$user) {
            return redirect('/Login')->with('error', 'Usuario no encontrado');
        }

        if (!password_verify($request->contraseña, $user->contraseña)) {
            session(['ultimo_usuario_intentado' => $request->usuario]);
            
            return redirect('/Login')->with('error', 'Contraseña incorrecta');
        }

        if ($user->estado !== 'activo') {
            return redirect('/Login')->with('error', 'Tu usuario está inactivo. Contacta al administrador.');
        }

        $correoUsuario = $user->persona->correo ?? 'No tiene correo';
        $contrasenaInicial = $user->persona->documento;
        $esAdmin = $user->rol->sigla === 'ADM';
        
        // Verificar si la contraseña actual es la cédula
        if (password_verify($contrasenaInicial, $user->contraseña)) {
            // Es la cédula
            if (!$esAdmin) {
                // No es admin -> debe cambiar contraseña
                session(['user_id_temp' => $user->id]);
                return redirect('/cambiar-contrasena');
            }
        }
        
        // Login normal
        session([
            'usuario'         => $user->usuario,
            'rol'             => $user->rol->nombre,
            'rol_sigla'       => $user->rol->sigla,
            'nombre_completo' => $user->persona->nombre_completo,
            'correo'          => $correoUsuario,
            'user_id'         => $user->id,
            'debe_cambiar_contrasena' => false,
        ]);
        
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/Login')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }

    // ── CRUD ─────────────────────────────────────────────

    public function index()
    {
        $usuarios = Usuario::with(['rol', 'persona'])->get();
        $roles    = Rol::all();
        return view('usuarios.Usuarios', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'primer_nombre'    => 'required|max:50',
            'segundo_nombre'   => 'nullable|max:50',
            'primer_apellido'  => 'required|max:50',
            'segundo_apellido' => 'nullable|max:50',
            'documento'        => [
                'required',
                'string',
                'min:6',
                'max:10',
                'regex:/^[1-9][0-9]{5,9}$/',
                'unique:personas,documento'
            ],
            'correo'           => 'nullable|email|max:100',
            'telefono'         => [
                'nullable',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/'
            ],
            'rol_id'           => 'required|exists:roles,id',
        ], [
            'documento.regex' => 'La cédula debe tener entre 6 y 10 dígitos y no comenzar con 0',
            'documento.min' => 'La cédula debe tener mínimo 6 dígitos',
            'documento.max' => 'La cédula debe tener máximo 10 dígitos',
            'documento.unique' => 'Ya existe un usuario con esta cédula',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos',
            'telefono.regex' => 'El teléfono solo debe contener números',
        ]);

        DB::transaction(function () use ($request) {
            $usuario = UsuarioHelper::generarUsuario(
                $request->documento,
                $request->primer_nombre
            );
            
            $persona = Persona::create([
                'primer_nombre'    => $request->primer_nombre,
                'segundo_nombre'   => $request->segundo_nombre,
                'primer_apellido'  => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'documento'        => $request->documento,
                'correo'           => $request->correo,
                'telefono'         => $request->telefono,
            ]);

            // Contraseña inicial = cédula
            $contrasenaInicial = $request->documento;
            
            Usuario::create([
                'persona_id'       => $persona->id,
                'usuario'          => $usuario,
                'contraseña'       => bcrypt($contrasenaInicial),
                'rol_id'           => $request->rol_id,
                'estado'           => 'activo',
            ]);
        });

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente. La contraseña inicial es su número de cédula.');
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);

        $request->validate([
            'primer_nombre'    => 'required|max:50',
            'segundo_nombre'   => 'nullable|max:50',
            'primer_apellido'  => 'required|max:50',
            'segundo_apellido' => 'nullable|max:50',
            'documento'        => [
                'required',
                'max:20',
                'regex:/^[1-9][0-9]{5,9}$/',
                'unique:personas,documento,' . $usuario->persona_id
            ],
            'correo'           => 'nullable|email|max:100',
            'telefono'         => 'nullable|size:10|regex:/^[0-9]{10}$/',
            'usuario'          => 'required|max:50|unique:usuarios,usuario,' . $id,
            'rol_id'           => 'required|exists:roles,id',
            'contraseña'       => 'nullable|min:6',
        ], [
            'documento.regex' => 'La cédula debe tener entre 6 y 10 dígitos y no comenzar con 0',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos',
            'contraseña.min' => 'La contraseña debe tener mínimo 6 caracteres',
        ]);

        DB::transaction(function () use ($request, $usuario) {
            $usuario->persona->update([
                'primer_nombre'    => $request->primer_nombre,
                'segundo_nombre'   => $request->segundo_nombre,
                'primer_apellido'  => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'documento'        => $request->documento,
                'correo'           => $request->correo,
                'telefono'         => $request->telefono,
            ]);

            $datos = [
                'usuario' => $request->usuario,
                'rol_id'  => $request->rol_id,
            ];

            if ($request->filled('contraseña')) {
                $datos['contraseña'] = bcrypt($request->contraseña);
            }

            $usuario->update($datos);
        });

        return redirect('/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    public function cambiarEstado(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'estado' => 'required|in:activo,inactivo'
        ]);
        
        // Evitar que un administrador se desactive a sí mismo
        $usuarioActual = Usuario::where('usuario', session('usuario'))->first();
        
        if ($usuarioActual && $usuarioActual->id == $usuario->id && $request->estado === 'inactivo') {
            return redirect('/usuarios')->with('error', 'No puedes desactivar tu propio usuario.');
        }
        
        $usuario->update([
            'estado' => $request->estado
        ]);
        
        $mensaje = $request->estado === 'activo' 
            ? 'Usuario activado correctamente.' 
            : 'Usuario desactivado correctamente.';
        
        return redirect('/usuarios')->with('success', $mensaje);
    }

   public function destroy($id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);
        
        // Evitar que un administrador se elimine a sí mismo
        $usuarioActual = Usuario::where('usuario', session('usuario'))->first();
        
        if ($usuarioActual && $usuarioActual->id == $usuario->id) {
            return redirect('/usuarios')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        DB::transaction(function () use ($usuario) {
            // ✅ 1. Eliminar observaciones de seguimiento del usuario
            \App\Models\ObservacionSeguimiento::where('usuario_id', $usuario->id)->delete();
            
            // ✅ 2. Eliminar seguimientos de bienestar del usuario
            \App\Models\SeguimientoBienestar::where('usuario_id', $usuario->id)->delete();
            
            // ✅ 3. Eliminar notificaciones del usuario
            \App\Models\Notificacion::where('usuario_id', $usuario->id)->delete();
            
            // ✅ 4. Eliminar reportes del usuario (LA CLAVE)
            \App\Models\Reporte::where('usuario_id', $usuario->id)->delete();
            
            // ✅ 5. Eliminar el usuario
            $persona = $usuario->persona;
            $usuario->delete();

            // ✅ 6. Eliminar la persona si no tiene más usuarios
            if ($persona->usuarios()->count() === 0) {
                $persona->delete();
            }
        });

        return redirect('/usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

    public function cargaMasiva(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            $archivo = $request->file('archivo');
            $data = Excel::toArray([], $archivo);
            $filas = $data[0];
            array_shift($filas); // Eliminar encabezado
            
            $rolDocente = Rol::where('sigla', 'DOC')->first();
            
            if (!$rolDocente) {
                return redirect()->back()->with('error', 'No se encontró el rol de docente.');
            }
            
            $creados = 0;
            $errores = 0;
            
            foreach ($filas as $fila) {
                $primerNombre = trim($fila[0] ?? '');
                $segundoNombre = trim($fila[1] ?? null);
                $primerApellido = trim($fila[2] ?? '');
                $segundoApellido = trim($fila[3] ?? null);
                $documento = trim($fila[4] ?? '');
                $correo = trim($fila[5] ?? null);
                $telefono = trim($fila[6] ?? null);
                
                // Validar datos requeridos
                if (empty($primerNombre) || empty($primerApellido) || empty($documento)) {
                    $errores++;
                    continue;
                }
                
                // Validar cédula
                if (!preg_match('/^[1-9][0-9]{5,9}$/', $documento)) {
                    $errores++;
                    continue;
                }
                
                // Validar teléfono si existe
                if (!empty($telefono) && !preg_match('/^[0-9]{10}$/', $telefono)) {
                    $telefono = null;
                }
                
                // Validar correo si existe
                if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $correo = null;
                }
                
                DB::beginTransaction();
                
                try {
                    // Verificar si ya existe persona
                    $persona = Persona::where('documento', $documento)->first();
                    
                    if (!$persona) {
                        $persona = Persona::create([
                            'primer_nombre'    => $primerNombre,
                            'segundo_nombre'   => $segundoNombre,
                            'primer_apellido'  => $primerApellido,
                            'segundo_apellido' => $segundoApellido,
                            'documento'        => $documento,
                            'correo'           => $correo,
                            'telefono'         => $telefono,
                        ]);
                    }
                    
                    // Verificar si ya existe usuario
                    $usuarioExistente = Usuario::where('persona_id', $persona->id)->first();
                    
                    if ($usuarioExistente) {
                        $errores++;
                        DB::rollBack();
                        continue;
                    }
                    
                    // Generar nombre de usuario
                    $usuario = UsuarioHelper::generarUsuario($documento, $primerNombre);
                    
                    // Crear usuario docente
                    Usuario::create([
                        'persona_id' => $persona->id,
                        'usuario'    => $usuario,
                        'contraseña' => bcrypt($documento),
                        'rol_id'     => $rolDocente->id,
                        'estado'     => 'activo',
                    ]);
                    
                    $creados++;
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errores++;
                    Log::error('Error importando docente: ' . $e->getMessage());
                }
            }
            
            $mensaje = "Se crearon {$creados} docentes correctamente.";
            if ($errores > 0) {
                $mensaje .= " {$errores} registros tenían errores y no se importaron.";
            }
            
            return redirect('/usuarios')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}