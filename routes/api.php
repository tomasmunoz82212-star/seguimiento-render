<?php

use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\ReporteApiController;

Route::post('/usuarios', [UsuarioApiController::class, 'store']);
Route::get('/reporte/{id}/nivel-alerta', [App\Http\Controllers\Api\ReporteApiController::class, 'nivelAlerta']);