<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BienestarController;
use App\Http\Controllers\SeguimientoAlertasController;
use App\Http\Controllers\ContrasenaController;
use App\Http\Controllers\RecuperacionController;
use App\Http\Controllers\AnaliticaController;
use App\Http\Controllers\NotificacionController;

Route::get('/', function () {
    return redirect('/Login');
});

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================

// ── AUTH ──
Route::get('/Login',   [UsuarioController::class, 'showLogin'])->name('login');
Route::post('/login',  [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout']);

// ── RECUPERACIÓN DE CONTRASEÑA ──
Route::get('/recuperar-contrasena', [RecuperacionController::class, 'showFormulario']);
Route::post('/recuperar-contrasena/enviar', [RecuperacionController::class, 'enviarCodigo']);
Route::get('/validar-codigo', [RecuperacionController::class, 'showValidarCodigo']);
Route::post('/validar-codigo', [RecuperacionController::class, 'validarCodigo']);
Route::post('/reenviar-codigo', [RecuperacionController::class, 'reenviarCodigo']);
Route::get('/cambiar-contrasena-recuperacion', [RecuperacionController::class, 'showNuevaContrasena']);
Route::post('/cambiar-contrasena-recuperacion', [RecuperacionController::class, 'actualizarContrasena']);

// ── CAMBIAR CONTRASEÑA (cuando el usuario inicia sesión por primera vez) ──
// Esta ruta necesita acceso pero con sesión iniciada, pero el middleware auth.check puede causar problemas
// La manejamos con verificación manual en el controlador
Route::get('/cambiar-contrasena', [ContrasenaController::class, 'showChangeForm'])->name('cambiar-contrasena');
Route::post('/cambiar-contrasena', [ContrasenaController::class, 'updatePassword']);

// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================

Route::middleware(['auth.check'])->group(function () {
    
    // ── DASHBOARD ──
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // ── USUARIOS ──
    Route::get('/usuarios',         [UsuarioController::class, 'index']);
    Route::post('/usuarios',        [UsuarioController::class, 'store']);
    Route::put('/usuarios/{id}',    [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
    Route::patch('/usuarios/{id}/cambiar-estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');
    Route::post('/usuarios/carga-masiva', [UsuarioController::class, 'cargaMasiva']);
    
    // ── PERÍODOS ──
    Route::get('/periodos',                  [PeriodoController::class, 'index']);
    Route::get('/periodos/nuevo',            [PeriodoController::class, 'create']);
    Route::post('/periodos',                 [PeriodoController::class, 'store']);
    Route::get('/periodos/{id}/editar',      [PeriodoController::class, 'edit']);
    Route::put('/periodos/{id}',             [PeriodoController::class, 'update']);
    Route::delete('/periodos/{id}',          [PeriodoController::class, 'destroy']);
    Route::get('/periodos/{id}/estudiantes', [PeriodoController::class, 'estudiantes']);
    Route::post('/periodos/{id}/listado',    [PeriodoController::class, 'actualizarListado']);
    
    // ── REPORTES ──
    Route::get('/nuevo-reporte',                       [ReporteController::class, 'create']);
    Route::post('/nuevo-reporte',                      [ReporteController::class, 'store']);
    Route::get('/mis-reportes',                        [ReporteController::class, 'misReportes']);
    Route::get('/reportes/{id}',                       [ReporteController::class, 'show']);
    Route::put('/reportes/{id}',                       [ReporteController::class, 'update']);
    Route::delete('/reportes/{id}',                    [ReporteController::class, 'destroy']);
    Route::get('/buscar-estudiante',                   [ReporteController::class, 'buscarEstudiante']);
    Route::get('/materias-por-programa/{programa_id}', [ReporteController::class, 'materiasPorPrograma']);
    
    // ── BIENESTAR ──
    Route::get('/bienestar',              [BienestarController::class, 'index']);
    Route::get('/bienestar/{id}',         [BienestarController::class, 'show']);
    Route::post('/bienestar/{id}',        [BienestarController::class, 'store']);
    Route::post('/bienestar/{id}/cerrar', [BienestarController::class, 'cerrar']);
    Route::post('/bienestar/seguimiento/{seguimiento}/observacion', [BienestarController::class, 'agregarObservacion'])->name('bienestar.agregar-observacion');
    
    // ── SEGUIMIENTO DE ALERTAS ──
    Route::get('/seguimiento',             [SeguimientoAlertasController::class, 'index']);
    Route::get('/seguimiento/{documento}', [SeguimientoAlertasController::class, 'show']);
    
    // ── ANALÍTICA Y REPORTES ──
    Route::get('/analitica', [AnaliticaController::class, 'index']);
    Route::get('/analitica/reporte-general-pdf', [AnaliticaController::class, 'generarReporteGeneralPDF']);
    Route::get('/analitica/comparativa', [AnaliticaController::class, 'comparativaPeriodos']);
    Route::post('/analitica/comparativa-pdf', [AnaliticaController::class, 'generarComparativaPDF']);
    
    // ── NOTIFICACIONES ──
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas']);
});

// ============================================
// RUTAS API
// ============================================

Route::get('/api/notificaciones/contador', [App\Http\Controllers\Api\NotificacionApiController::class, 'contador']);
Route::get('/api/reportes/actualizar-niveles', [App\Http\Controllers\Api\ReporteApiController::class, 'actualizarTodosLosNiveles']);
Route::get('/api/reporte/{id}/nivel-alerta', [App\Http\Controllers\Api\ReporteApiController::class, 'nivelAlerta']);
Route::get('/api/ultimo-reporte', function() {
    return response()->json([
        'timestamp' => cache()->get('ultimo_nuevo_reporte', 0)
    ]);
});

// API para usuarios (requiere autenticación)
Route::middleware(['auth.check'])->group(function () {
    Route::post('/api/usuarios', [App\Http\Controllers\Api\UsuarioApiController::class, 'store']);
});

// API para crear el PRIMER administrador (solo si no hay ninguno)
Route::post('/api/crear-primer-admin', function (Request $request) {
    // Verificar que no exista ningún administrador en el sistema
    $existeAdmin = App\Models\Usuario::whereHas('rol', function($q) {
        $q->where('sigla', 'ADM');
    })->exists();
    
    if ($existeAdmin) {
        return response()->json([
            'error' => 'Ya existe un administrador en el sistema. No se puede crear otro por esta vía.'
        ], 403);
    }
    
    // Si no hay admin, permitir crear
    $controller = new App\Http\Controllers\Api\UsuarioApiController();
    return $controller->store($request);
});