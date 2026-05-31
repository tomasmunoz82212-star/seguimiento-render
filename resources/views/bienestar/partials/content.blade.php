<div class="bienestar-cards">
    @if($reportes->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-heart-pulse"></i>
            <p>No hay reportes pendientes de seguimiento.</p>
        </div>
    @else
        @foreach($reportes as $r)
        <div class="bienestar-card">
            <div class="bc-estudiante">
                <div class="bc-avatar">{{ strtoupper(substr($r->estudiante->nombre, 0, 1)) }}</div>
                <div class="bc-datos">
                    <div class="bc-nombre">{{ $r->estudiante->nombre }}</div>
                    <div class="bc-carrera">{{ $r->programa->nombre }}</div>
                </div>
            </div>
            <div class="bc-estados">
                <div class="bc-estado-grupo">
                    <span class="bc-estado-titulo">Tipo</span>
                    <span class="badge badge-tipo badge-tipo-{{ $r->tipo }}">{{ ucfirst($r->tipo) }}</span>
                </div>
                <div class="bc-estado-grupo">
                    <span class="bc-estado-titulo">Estado</span>
                    <span class="badge badge-estado-{{ $r->estado }}">{{ ucfirst(str_replace('_', ' ', $r->estado)) }}</span>
                </div>
                <div class="bc-estado-grupo">
                    <span class="bc-estado-titulo">Seguimiento</span>
                    @if($r->seguimientoBienestar)
                        <span class="bc-seguimiento bc-seguimiento-si"><i class="fa-solid fa-circle-check"></i> Atendido</span>
                    @else
                        <span class="bc-seguimiento bc-seguimiento-no"><i class="fa-solid fa-circle-exclamation"></i> Sin seguimiento</span>
                    @endif
                </div>
            </div>
            <a href="/bienestar/{{ $r->id }}" class="btn btn-secondary btn-sm">Gestionar</a>
        </div>
        @endforeach
    @endif
</div>