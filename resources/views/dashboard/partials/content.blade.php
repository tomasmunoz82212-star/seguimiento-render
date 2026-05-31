{{-- Datos ocultos para JS (gráficos) --}}
<input type="hidden" id="data-tipo-academico" value="{{ $tipoAcademico }}">
<input type="hidden" id="data-tipo-asistencia" value="{{ $tipoAsistencia }}">
<input type="hidden" id="data-tipo-comportamiento" value="{{ $tipoComportamiento }}">

{{-- Tarjetas resumen --}}
<div class="stat-grid">
    <div class="stat-card" style="--accent:#1B5E20">
        <div class="stat-value">{{ $totalReportes }}</div>
        <div class="stat-label">Total reportes</div>
    </div>
    <div class="stat-card" style="--accent:#E65100">
        <div class="stat-value">{{ $pendientes }}</div>
        <div class="stat-label">Pendientes</div>
    </div>
    <div class="stat-card" style="--accent:#1565C0">
        <div class="stat-value">{{ $enSeguimiento }}</div>
        <div class="stat-label">En seguimiento</div>
    </div>
    <div class="stat-card" style="--accent:#2D7D32">
        <div class="stat-value">{{ $cerrados }}</div>
        <div class="stat-label">Cerrados</div>
    </div>
</div>

{{-- Fila 1: Reportes por tipo + Reportes por carrera --}}
<div class="chart-row">
    <div class="card chart-main">
        <div class="card-title">Reportes por tipo de situación</div>
        <div class="chart-wrap" style="height: 220px;">
            <canvas id="chartTipos"></canvas>
        </div>
    </div>
    <div class="card chart-main">
        <div class="card-title">Reportes por carrera</div>
        <div class="chart-wrap" style="height: 220px;">
            <canvas id="chartCarreras"></canvas>
        </div>
        @foreach($porCarrera as $nombre => $total)
            {{-- Mostrar solo números enteros --}}
            <div data-carrera-nombre="{{ $nombre }}" data-carrera-total="{{ round($total) }}" style="display:none;"></div>
        @endforeach
    </div>
</div>

{{-- Fila 2: Reportes por semestre --}}
<div class="card chart-full-width">
    <div class="card-title">Reportes por semestre académico</div>
    <div class="chart-wrap" style="height: 260px;">
        <canvas id="chartSemestres"></canvas>
    </div>
    @foreach($porCarrera as $nombre => $total)
        <div data-carrera-nombre="{{ $nombre }}" data-carrera-total="{{ (int) $total }}" style="display:none;"></div>
    @endforeach
</div>

{{-- Fila 3: Top materias (ancho completo) --}}
<div class="card chart-full-width">
    <div class="card-title">Materias con más reportes (Top 10)</div>
    <div class="chart-wrap" style="min-height: 320px;">
        <canvas id="chartMaterias"></canvas>
    </div>
    @foreach($reportesPorMateria as $materia)
        <div data-materia-nombre="{{ $materia['nombre'] }}" 
             data-materia-programa="{{ $materia['programa'] }}" 
             data-materia-total="{{ round($materia['total']) }}" 
             style="display:none;"></div>
    @endforeach
</div>

<style>
.chart-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
}
.chart-main {
    flex: 1;
    min-width: 280px;
}
.chart-full-width {
    width: 100%;
    margin-top: 20px;
}
.chart-wrap {
    position: relative;
    height: 260px;
}
.chart-full-width .chart-wrap {
    min-height: 320px;
    height: auto;
}

/* Responsive para pantallas pequeñas */
@media (max-width: 768px) {
    .chart-row {
        flex-direction: column;
    }
    .chart-main {
        width: 100%;
    }
    .chart-full-width .chart-wrap {
        min-height: 280px;
    }
}
</style>