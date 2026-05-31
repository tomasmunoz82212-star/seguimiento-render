<div class="sa-seccion-titulo">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>Estudiante(s) con alertas: {{ $estudiantes->count() }}</span>
</div>

<div class="seguimiento-alertas-cards">
    @if($estudiantes->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-bell-slash"></i>
            <p>No hay alertas registradas.</p>
        </div>
    @else
        @foreach($estudiantes as $est)
        <div class="sa-card {{ $est['pendientes'] > 0 ? 'sa-card-alerta' : '' }}">
            <div class="sc-left">
                <div class="sc-avatar">{{ strtoupper(substr($est['estudiante']->nombre, 0, 1)) }}</div>
                <div>
                    <div class="sc-nombre">{{ $est['estudiante']->nombre }}</div>
                    <div class="sc-doc">CC {{ $est['documento'] }}</div>
                </div>
            </div>
            <div class="sc-counters">
                <div class="sc-counter" style="--cc:#E65100"><span>{{ $est['pendientes'] }}</span><small>Pendientes</small></div>
                <div class="sc-counter" style="--cc:#1565C0"><span>{{ $est['en_seguimiento'] }}</span><small>En seguimiento</small></div>
                <div class="sc-counter" style="--cc:#2D7D32"><span>{{ $est['cerrados'] }}</span><small>Cerrados</small></div>
                <div class="sc-counter" style="--cc:#6A1B9A"><span>{{ $est['total'] }}</span><small>Total alertas</small></div>
            </div>
            <div class="sc-bienestar">
                @if($est['con_bienestar'] > 0)
                    <span class="sc-bien-badge sc-bien-si"><i class="fa-solid fa-heart-pulse"></i> Atendió {{ $est['con_bienestar'] }}</span>
                @else
                    <span class="sc-bien-badge sc-bien-no"><i class="fa-solid fa-circle-exclamation"></i> Sin atención</span>
                @endif
            </div>
            <a href="/seguimiento/{{ $est['documento'] }}{{ isset($periodoSeleccionado) ? '?periodo_id=' . $periodoSeleccionado->id : '' }}" class="btn btn-secondary btn-sm">Ver historial</a>
        </div>
        @endforeach
    @endif
</div>