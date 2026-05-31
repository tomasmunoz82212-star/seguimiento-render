@extends('layouts.panel')
@section('titulo', 'Seguimiento de Alertas — ' . $estudiante->nombre)

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/SeguimientoAlertas.css') }}">
@endpush

@section('contenido')
<a href="/seguimiento{{ isset($periodoSeleccionado) ? '?periodo_id=' . $periodoSeleccionado->id : '' }}" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver al Seguimiento de Alertas
</a>

@if(isset($periodoSeleccionado) && $periodoSeleccionado)
<div style="background: #E3F2FD; padding: 8px 12px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-calendar"></i> Mostrando alertas del período: <strong>{{ $periodoSeleccionado->nombre }}</strong>
</div>
@endif

{{-- Perfil del estudiante --}}
<div class="perfil-estudiante">
    <div class="perfil-avatar">
        {{ strtoupper(substr($estudiante->nombre, 0, 1)) }}
    </div>
    <div class="perfil-info">
        <div class="perfil-nombre">{{ $estudiante->nombre }}</div>
        <div class="perfil-datos">
            <span class="perfil-dato">
                <i class="fa-solid fa-id-card"></i>
                CC {{ $estudiante->documento }}
            </span>
            @if($estudiante->correo)
            <span class="perfil-dato">
                <i class="fa-solid fa-envelope"></i>
                {{ $estudiante->correo }}
            </span>
            @endif
            @if($estudiante->telefono)
            <span class="perfil-dato">
                <i class="fa-solid fa-phone"></i>
                {{ $estudiante->telefono }}
            </span>
            @endif
        </div>
    </div>
    <div style="display:flex; gap:8px">
        <div class="sc-counter" style="--cc:#E65100">
            <span>{{ $reportes->where('estado','pendiente')->count() }}</span>
            <small>Pendientes</small>
        </div>
        <div class="sc-counter" style="--cc:#1565C0">
            <span>{{ $reportes->where('estado','en_seguimiento')->count() }}</span>
            <small>En seguimiento</small>
        </div>
        <div class="sc-counter" style="--cc:#2D7D32">
            <span>{{ $reportes->where('estado','cerrado')->count() }}</span>
            <small>Cerrados</small>
        </div>
    </div>
</div>

{{-- Timeline de alertas --}}
<div class="timeline-simple">
    @foreach($reportes->reverse()->values() as $index => $r)
    <div class="reporte-card" id="reporte-{{ $r->id }}">
        <div class="reporte-header">
            <div class="reporte-numero">
                Alerta #{{ $index + 1 }}
            </div>
            <div class="reporte-meta">
                <span class="meta-fecha">
                    <i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($r->creado_en)->format('d/m/Y') }}
                </span>
                <span class="badge-tipo">{{ ucfirst($r->tipo) }}</span>
                <span class="badge-estado {{ $r->estado }}">{{ ucfirst(str_replace('_', ' ', $r->estado)) }}</span>

                @if($r->estado === 'pendiente')
                    <span id="badge-nivel-{{ $r->id }}">
                        @include('components.badge-nivel', ['nivel' => $r->nivel_alerta])
                    </span>
                    <small id="debug-nivel-{{ $r->id }}" style="display:block; font-size:10px; color:#999;">
                        Límite: {{ $r->fecha_limite_legible }}
                    </small>
                @else
                    <span id="badge-nivel-{{ $r->id }}" style="display:none;"></span>
                    <small id="debug-nivel-{{ $r->id }}" style="display:none;"></small>
                @endif
            </div>
        </div>

        {{-- Reporte del profesor --}}
        <div class="reporte-seccion">
            <div class="seccion-titulo">
                <i class="fa-regular fa-user"></i> Reporte del profesor
            </div>
            <div class="profesor-nombre">
                Profesor: {{ $r->usuario->persona->nombre_completo }}
            </div>
            <div class="reporte-detalle">
                <div class="detalle-linea">
                    <strong>Período:</strong> {{ $r->periodo->nombre }}
                </div>
                <div class="detalle-linea">
                    <strong>Carrera:</strong> {{ $r->carrera_estudiante }}
                </div>
                <div class="detalle-linea">
                    <strong>Materia:</strong> {{ $r->materia->nombre ?? '—' }}
                </div>
            </div>
            <div class="observaciones">
                <div class="obs-titulo">Descripción del caso</div>
                <p>{{ $r->descripcion }}</p>
            </div>
        </div>

        {{-- Seguimiento Bienestar --}}
        @if($r->seguimientoBienestar)
        <div class="reporte-seccion bienestar">
            <div class="seccion-titulo">
                <i class="fa-regular fa-heart"></i> Seguimiento de Bienestar
            </div>
            <div class="bienestar-estado">
                Estado: <strong>{{ ucfirst(str_replace('_', ' ', $r->seguimientoBienestar->estado)) }}</strong>
            </div>

            @if(count($r->seguimientoBienestar->aspectos_activos_con_etiquetas) > 0)
            <div class="aspectos">
                <div class="aspectos-titulo">Aspectos identificados</div>
                <div class="aspectos-lista">
                    @foreach($r->seguimientoBienestar->aspectos_activos_con_etiquetas as $etiqueta)
                        <span class="aspecto-tag">{{ $etiqueta }}</span>
                    @endforeach
                    @if($r->seguimientoBienestar->otro && $r->seguimientoBienestar->detalle_otro)
                        <span class="aspecto-tag">{{ $r->seguimientoBienestar->detalle_otro }}</span>
                    @endif
                </div>
            </div>
            @endif

            @if($r->seguimientoBienestar->observaciones->count() > 0)
            <div class="observaciones" style="margin-top: 15px;">
                <div class="obs-titulo">
                    <i class="fa-solid fa-history"></i> Historial de seguimiento
                </div>
                @foreach($r->seguimientoBienestar->observaciones as $obs)
                <div style="border-left: 3px solid {{ $obs->contacto_fallido ? '#C62828' : '#2D7D32' }}; padding-left: 12px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $obs->usuario->persona->nombre_completo }}</strong>
                            <span class="badge" style="background:#E8F5E9; color:#2D7D32; margin-left:8px">
                                <i class="fa-solid fa-{{ $obs->medio_contacto == 'presencial' ? 'person' : ($obs->medio_contacto == 'telefono' ? 'phone' : ($obs->medio_contacto == 'whatsapp' ? 'brands fa-whatsapp' : 'video')) }}"></i>
                                {{ ucfirst($obs->medio_contacto) }}
                            </span>
                            @if($obs->contacto_fallido)
                                <span class="badge" style="background:#FFEBEE; color:#C62828; margin-left:5px">
                                    <i class="fa-solid fa-triangle-exclamation"></i> No contactado
                                </span>
                            @endif
                        </div>
                        <small style="color:#6c757d">{{ $obs->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @if($obs->contacto_fallido && $obs->motivo_no_contacto)
                        <p style="margin:5px 0; color:#C62828; font-size:12px">
                            <strong>Motivo:</strong> {{ $obs->motivo_no_contacto }}
                        </p>
                    @endif
                    <p style="margin-top:8px; white-space: pre-line;">{{ $obs->observacion }}</p>
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($r->seguimientoBienestar->razon_cierre))
            <div class="observaciones cierre">
                <div class="obs-titulo">Solución / Cierre del caso</div>
                <p>{{ $r->seguimientoBienestar->razon_cierre }}</p>
            </div>
            @elseif($r->estado === 'cerrado')
            <div class="observaciones cierre vacio">
                <div class="obs-titulo">Caso cerrado sin solución registrada</div>
                <p>No se registró una solución al cerrar este caso.</p>
            </div>
            @endif
        </div>
        @else
        <div class="reporte-seccion sin-bienestar">
            <i class="fa-regular fa-bell"></i> Bienestar aún no ha registrado seguimiento para esta alerta.
        </div>
        @endif
    </div>
    @endforeach
</div>

@if($reportes->isEmpty())
<div class="empty-state">
    <i class="fa-solid fa-bell-slash"></i>
    <p>No hay alertas registradas {{ isset($periodoSeleccionado) ? 'en el período ' . $periodoSeleccionado->nombre : '' }}.</p>
</div>
@endif
@endsection