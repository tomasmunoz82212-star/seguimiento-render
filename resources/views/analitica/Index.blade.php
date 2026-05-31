@extends('layouts.panel')
@section('titulo', 'Analítica y Reportes')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/analitica.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Analítica y Reportes</div>
        <div class="page-sub">Generación de reportes y análisis de datos</div>
    </div>
</div>

<div class="analitica-acciones">
    
    {{-- Tarjeta: Reporte General --}}
    <div class="card accion-card">
        <div class="accion-icon">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <div class="accion-contenido">
            <h3>Reporte General de Alertas</h3>
            <p>Exporta un PDF con el resumen de alertas filtrado por período académico:</p>
            <ul>
                <li>Total de alertas por estado (pendiente, seguimiento, cerrado)</li>
                <li>Distribución por tipo (académico, asistencia, comportamiento)</li>
                <li>Alertas por programa académico</li>
            </ul>
            
            <div class="form-group" style="margin-top: 16px; max-width: 300px;">
                <label>Seleccionar período:</label>
                <select id="periodo_reporte" class="form-control">
                    <option value="">-- Todos los períodos --</option>
                    @foreach($periodos as $p)
                        <option value="{{ $p->id }}" {{ isset($periodoSeleccionado) && $periodoSeleccionado && $periodoSeleccionado->id == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="accion-boton">
            <button class="btn btn-primary" onclick="generarReporteGeneral()">
                <i class="fa-solid fa-file-pdf"></i> Generar PDF
            </button>
        </div>
    </div>

    {{-- Tarjeta: Comparativa de Períodos --}}
    <div class="card accion-card">
        <div class="accion-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div class="accion-contenido">
            <h3>Comparativa de Períodos Académicos</h3>
            <p>Analiza la evolución de los estudiantes entre dos períodos:</p>
            <ul>
                <li>Desertores (no continúan y no cumplen semestres mínimos)</li>
                <li>Graduados (completaron plan de estudios)</li>
                <li>Cambios de carrera entre períodos</li>
                <li>Reportes asociados a cada grupo</li>
            </ul>
        </div>
        <div class="accion-boton">
            <a href="/analitica/comparativa" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-right"></i> Ir a Comparativa
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function generarReporteGeneral() {
    const periodoId = document.getElementById('periodo_reporte').value;
    let url = '/analitica/reporte-general-pdf';
    if (periodoId) {
        url += '?periodo_id=' + periodoId;
    }
    window.open(url, '_blank');
}
</script>
@endpush