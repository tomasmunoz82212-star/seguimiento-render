@extends('layouts.panel')
@section('titulo', 'Comparativa de Períodos')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Analitica.css') }}">
@endpush

@section('contenido')
<a href="/analitica" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver a Analítica
</a>

<div class="page-header">
    <div>
        <div class="page-title">Comparativa de Períodos Académicos</div>
        <div class="page-sub">Analice la evolución de estudiantes entre períodos</div>
    </div>
</div>

@if(isset($error))
    <div class="alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
    </div>
    <div class="card text-center" style="margin-top: 20px">
        <i class="fa-solid fa-calendar-circle-exclamation" style="font-size: 48px; color: #9DA3B4; margin-bottom: 16px; display: block;"></i>
        <p>Para realizar comparativas, necesitas tener al menos dos períodos académicos registrados.</p>
        <a href="/periodos" class="btn btn-primary" style="margin-top: 16px">
            <i class="fa-solid fa-plus"></i> Crear Períodos
        </a>
    </div>
@else
    <div class="card max-800" style="margin: 0 auto">
        <form method="POST" action="/analitica/comparativa-pdf" target="_blank">
            @csrf
            
            <div class="card-title">Seleccione los períodos a comparar</div>
            
            <div class="alert-banner alert-info" style="margin-bottom: 20px">
                <i class="fa-solid fa-circle-info"></i>
                <span>Se comparará la población estudiantil entre dos períodos, identificando desertores, graduados y cambios de carrera.</span>
            </div>
            
            <div class="form-group">
                <label>Período anterior (base)</label>
                <select name="periodo_anterior_id" class="form-control" required>
                    <option value="">— Seleccionar período —</option>
                    @foreach($periodos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ \Carbon\Carbon::parse($p->fecha_inicio)->format('Y') }})</option>
                    @endforeach
                </select>
                <small class="text-gray">Se analizarán los estudiantes de este período</small>
            </div>
            
            <div class="form-group">
                <label>Período actual (comparación)</label>
                <select name="periodo_actual_id" class="form-control" required>
                    <option value="">— Seleccionar período —</option>
                    @foreach($periodos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ \Carbon\Carbon::parse($p->fecha_inicio)->format('Y') }})</option>
                    @endforeach
                </select>
                <small class="text-gray">Se verificará si los estudiantes continúan matriculados</small>
            </div>
            
            <hr class="modal-sep">
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-file-pdf"></i> Generar Reporte PDF
                </button>
                <button type="reset" class="btn btn-outline">Limpiar</button>
            </div>
        </form>
    </div>
    
    <div class="card" style="margin-top: 20px">
        <div class="card-title">¿Qué información incluye este reporte?</div>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div class="info-item">
                <i class="fa-solid fa-person-walking-arrow-right" style="color: #E65100; font-size: 24px;"></i>
                <h4>Desertores</h4>
                <p>Estudiantes que NO continúan y NO cumplen con los semestres mínimos para graduarse</p>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-graduation-cap" style="color: #2D7D32; font-size: 24px;"></i>
                <h4>Graduados</h4>
                <p>Estudiantes que completaron el plan de estudios (10 semestres profesionales / 6 tecnologías)</p>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-arrow-right-arrow-left" style="color: #1565C0; font-size: 24px;"></i>
                <h4>Cambios de Carrera</h4>
                <p>Estudiantes que cambiaron de programa académico entre períodos</p>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-chart-line" style="color: #6A1B9A; font-size: 24px;"></i>
                <h4>Reportes Asociados</h4>
                <p>Para cada grupo, se muestra cuántos reportes tenían en el período anterior</p>
            </div>
        </div>
    </div>
@endif
@endsection