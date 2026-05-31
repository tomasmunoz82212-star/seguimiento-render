@if($notificaciones->isEmpty())
    <div class="empty-state">
        <i class="fa-regular fa-bell-slash"></i>
        <p>No tienes notificaciones.</p>
    </div>
@else
    <div class="notificaciones-list">
        @foreach($notificaciones as $noti)
            <div class="notificacion-card {{ $noti->leida ? '' : 'no-leida' }}" data-id="{{ $noti->id }}">
                <div class="notificacion-icon">
                    @if($noti->tipo == 'cambio_nivel')
                        <i class="fa-solid fa-flag"></i>
                    @elseif($noti->tipo == 'caso_cerrado')
                        <i class="fa-solid fa-check-circle"></i>
                    @else
                        <i class="fa-regular fa-bell"></i>
                    @endif
                </div>
                <div class="notificacion-contenido">
                    <div class="notificacion-mensaje">{{ $noti->mensaje }}</div>
                    <div class="notificacion-meta">
                        <span class="notificacion-fecha">
                            <i class="fa-regular fa-clock"></i> {{ $noti->tiempo_legible }}
                        </span>
                        @if($noti->reporte_id)
                            <a href="/seguimiento/{{ $noti->reporte->estudiante->documento }}" class="notificacion-enlace">
                                <i class="fa-solid fa-arrow-right"></i> Ver reporte
                            </a>
                        @endif
                    </div>
                </div>
                @if(!$noti->leida)
                    <div class="notificacion-badge">
                        <span class="badge badge-pendiente">Nueva</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="paginacion">
        @if($notificaciones->hasPages())
            <div class="paginacion-enlaces">
                {{-- Anterior --}}
                @if($notificaciones->onFirstPage())
                    <span class="pag-link disabled">← Anterior</span>
                @else
                    <a href="{{ $notificaciones->previousPageUrl() }}" class="pag-link">← Anterior</a>
                @endif

                {{-- Números de página --}}
                @foreach($notificaciones->getUrlRange(1, $notificaciones->lastPage()) as $page => $url)
                    @if($page == $notificaciones->currentPage())
                        <span class="pag-link active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-link">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Siguiente --}}
                @if($notificaciones->hasMorePages())
                    <a href="{{ $notificaciones->nextPageUrl() }}" class="pag-link">Siguiente →</a>
                @else
                    <span class="pag-link disabled">Siguiente →</span>
                @endif
            </div>
        @endif
    </div>
@endif