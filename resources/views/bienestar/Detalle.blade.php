@extends('layouts.panel')
@section('titulo', 'Panel Bienestar')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Bienestar.css') }}">
@endpush

@section('contenido')
<a href="/bienestar" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver al panel
</a>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Perfil del estudiante --}}
<div class="perfil-estudiante">
    <div class="perfil-avatar">
        {{ strtoupper(substr($reporte->estudiante->nombre, 0, 1)) }}
    </div>
    <div class="perfil-info">
        <div class="perfil-nombre">{{ $reporte->estudiante->nombre }}</div>
        <div class="perfil-datos">
            <span class="perfil-dato">
                <i class="fa-solid fa-id-card"></i>
                CC {{ $reporte->estudiante->documento }}
            </span>
            @if($reporte->estudiante->correo)
            <span class="perfil-dato">
                <i class="fa-solid fa-envelope"></i>
                {{ $reporte->estudiante->correo }}
            </span>
            @endif
            @if($reporte->estudiante->telefono)
            <span class="perfil-dato">
                <i class="fa-solid fa-phone"></i>
                {{ $reporte->estudiante->telefono }}
            </span>
            @endif
            <span class="perfil-dato">
                <i class="fa-solid fa-graduation-cap"></i>
                {{ $reporte->programa->nombre }}
            </span>
        </div>
    </div>
    <div class="perfil-badges">
        <span class="badge badge-tipo badge-tipo-{{ $reporte->tipo }}">
            {{ ucfirst($reporte->tipo) }}
        </span>
        <span class="badge badge-estado-{{ $reporte->estado }}">
            {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
        </span>

        @if($reporte->estado === 'pendiente')
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                <span id="badge-nivel-{{ $reporte->id }}">
                    @include('components.badge-nivel', ['nivel' => $reporte->nivel_alerta])
                </span>
                <small style="font-size:10px; color:#999;">
                    Límite: {{ $reporte->fecha_limite_legible }}
                </small>        
            </div>
        @endif
    </div>
</div>

{{-- Reporte del profesor --}}
<div class="reporte-profesor-card" style="margin-bottom:20px">
    <div class="reporte-profesor-header">
        <div class="reporte-profesor-icon">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <div>
            <div class="reporte-profesor-titulo">Reporte del Profesor</div>
            <div class="reporte-profesor-docente">
                {{ $reporte->usuario->persona->nombre_completo }}
            </div>
            <div class="reporte-profesor-meta">
                {{ \Carbon\Carbon::parse($reporte->creado_en)->format('d/m/Y') }}
            </div>
        </div>
    </div>
    <div class="reporte-profesor-body">
        <div class="reporte-profesor-tags">
            <span class="rp-tag">
                <i class="fa-solid fa-calendar-days"></i>
                <span><strong>Período:</strong> {{ $reporte->periodo->nombre }}</span>
            </span>
            <span class="rp-tag">
                <i class="fa-solid fa-book"></i>
                <span><strong>Materia:</strong> {{ $reporte->materia->nombre ?? '—' }}</span>
            </span>
        </div>
        <div class="reporte-descripcion-bloque">
            <div class="rp-desc-label">
                <i class="fa-solid fa-quote-left"></i> Descripción del docente
            </div>
            <p>{{ $reporte->descripcion }}</p>
        </div>
    </div>
</div>

{{-- Formulario de aspectos --}}
@if(!$seguimiento && $reporte->estado !== 'cerrado')
<div class="card">
    <div class="card-title">Aspectos que afectan al estudiante</div>
    <form method="POST" action="/bienestar/{{ $reporte->id }}">
        @csrf

        {{-- Socioeconómicos --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-coins"></i> Socioeconómicos</div>
            <label class="aspecto-check"><input type="checkbox" name="dificultad_economica"> Dificultad económica</label>
            <label class="aspecto-check"><input type="checkbox" name="trabaja_y_estudia"> Trabaja y estudia simultáneamente</label>
            <label class="aspecto-check"><input type="checkbox" name="falta_apoyo_familiar"> Falta de apoyo familiar</label>
        </div>

        {{-- Psicológicos --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-brain"></i> Psicológicos / Emocionales</div>
            <label class="aspecto-check"><input type="checkbox" name="ansiedad_estres"> Ansiedad o estrés</label>
            <label class="aspecto-check"><input type="checkbox" name="depresion_tristeza"> Depresión o tristeza persistente</label>
            <label class="aspecto-check"><input type="checkbox" name="baja_autoestima"> Baja autoestima</label>
            <label class="aspecto-check"><input type="checkbox" name="desmotivacion"> Desmotivación hacia el estudio</label>
        </div>

        {{-- Salud --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-heart-pulse"></i> Salud</div>
            <label class="aspecto-check"><input type="checkbox" name="problema_salud_fisica"> Problema de salud física</label>
            <label class="aspecto-check"><input type="checkbox" name="problema_salud_mental"> Problema de salud mental</label>
        </div>

        {{-- Convivencia --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-people-group"></i> Convivencia</div>
            <label class="aspecto-check"><input type="checkbox" name="conflicto_pares"> Conflicto con compañeros</label>
            <label class="aspecto-check"><input type="checkbox" name="conflicto_docentes"> Conflicto con docentes</label>
            <label class="aspecto-check"><input type="checkbox" name="bullying_acoso"> Bullying o acoso</label>
        </div>

        {{-- Académicos --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-book-open"></i> Académicos</div>
            <label class="aspecto-check"><input type="checkbox" name="dificultad_aprendizaje"> Dificultad de aprendizaje</label>
            <label class="aspecto-check"><input type="checkbox" name="problema_adaptacion"> Problema de adaptación</label>
            <label class="aspecto-check"><input type="checkbox" name="falta_habitos_estudio"> Falta de hábitos de estudio</label>
        </div>

        {{-- Familiares --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-house-chimney-user"></i> Familiares</div>
            <label class="aspecto-check"><input type="checkbox" name="problema_familiar"> Problema o conflicto familiar</label>
            <label class="aspecto-check"><input type="checkbox" name="responsabilidad_hogar"> Responsabilidades del hogar</label>
        </div>

        {{-- Otro --}}
        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo"><i class="fa-solid fa-ellipsis"></i> Otro</div>
            <label class="aspecto-check">
                <input type="checkbox" name="otro" id="chk-otro" onchange="toggleOtro()"> Otro motivo
            </label>
            <div id="detalle-otro-wrap" style="display:none; margin-top:8px">
                <input type="text" name="detalle_otro" placeholder="Describa el motivo..." style="width:100%">
            </div>
        </div>

        {{-- Medio de contacto y primera observación --}}
        <div class="aspecto-grupo" style="border-left-color:#6A1B9A">
            <div class="aspecto-grupo-titulo" style="color:#6A1B9A">
                <i class="fa-solid fa-phone-flip"></i> Medio de contacto utilizado <span style="color:#C62828">*</span>
            </div>
            <div class="form-grid-contacto">
                @foreach(['presencial','telefono','meet','teams','whatsapp','correo'] as $medio)
                <label class="contacto-option">
                    <input type="radio" name="medio_contacto" value="{{ $medio }}" required>
                    <div class="contacto-option-body">
                        <i class="fa-solid fa-{{ $medio == 'presencial' ? 'person' : ($medio == 'telefono' ? 'phone' : ($medio == 'whatsapp' ? 'brands fa-whatsapp' : 'video')) }}"></i>
                        <span>{{ ucfirst($medio) }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="aspecto-grupo">
            <label class="aspecto-check">
                <input type="checkbox" name="contacto_fallido" id="contacto_fallido" onchange="toggleMotivoNoContacto()">
                No se pudo contactar al estudiante
            </label>
            <div id="motivo-no-contacto-wrap" style="display:none; margin-top:10px">
                <input type="text" name="motivo_no_contacto" class="form-control" placeholder="Motivo (ej. teléfono apagado)">
            </div>
        </div>

        <div class="form-group">
            <label>Observación inicial (opcional)</label>
            <textarea name="observacion_inicial" rows="3" placeholder="Describa la interacción..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar seguimiento</button>
    </form>
</div>
@endif

{{-- Historial de observaciones --}}
@if($seguimiento && $seguimiento->observaciones->count() > 0)
<div class="card" style="margin-bottom:20px">
    <div class="card-title">Historial de Observaciones</div>
    <div style="max-height: 400px; overflow-y: auto;">
        @foreach($seguimiento->observaciones as $obs)
        <div style="border-bottom: 1px solid #E2E4EA; padding: 12px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <div>
                    <strong>{{ $obs->usuario->persona->nombre_completo }}</strong>
                    <span class="badge" style="background:#E8F5E9; color:#2D7D32; margin-left:8px">
                        <i class="fa-solid fa-{{ $obs->medio_contacto == 'presencial' ? 'person' : ($obs->medio_contacto == 'telefono' ? 'phone' : ($obs->medio_contacto == 'whatsapp' ? 'brands fa-whatsapp' : 'video')) }}"></i>
                        {{ ucfirst($obs->medio_contacto) }}
                    </span>
                    @if($obs->contacto_fallido)
                        <span class="badge" style="background:#FFEBEE; color:#C62828">
                            <i class="fa-solid fa-triangle-exclamation"></i> No contactado
                        </span>
                    @endif
                </div>
                <small style="color: #9DA3B4;">{{ $obs->created_at->format('d/m/Y H:i') }}</small>
            </div>
            @if($obs->contacto_fallido && $obs->motivo_no_contacto)
                <p style="margin: 5px 0; color:#C62828; font-size:12px">
                    <strong>Motivo:</strong> {{ $obs->motivo_no_contacto }}
                </p>
            @endif
            <p style="margin: 0; white-space: pre-line;">{{ $obs->observacion }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Formulario para agregar nueva observación --}}
@if($seguimiento && $reporte->estado !== 'cerrado')
<div class="card">
    <div class="card-title">Agregar nueva observación</div>
    <form method="POST" action="{{ route('bienestar.agregar-observacion', $seguimiento->id) }}">
        @csrf

        <input type="hidden" name="contacto_fallido" value="0">

        <div class="aspecto-grupo">
            <div class="aspecto-grupo-titulo">Medio de contacto <span style="color:#C62828">*</span></div>
            <div class="form-grid-contacto">
                @foreach(['presencial','telefono','meet','teams','whatsapp','correo'] as $medio)
                <label class="contacto-option">
                    <input type="radio" name="medio_contacto" value="{{ $medio }}" required>
                    <div class="contacto-option-body">
                        <i class="fa-solid fa-{{ $medio == 'presencial' ? 'person' : ($medio == 'telefono' ? 'phone' : ($medio == 'whatsapp' ? 'brands fa-whatsapp' : 'video')) }}"></i>
                        <span>{{ ucfirst($medio) }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="aspecto-check">
            <input type="checkbox" name="contacto_fallido" id="contacto_fallido_nuevo" value="1" onchange="toggleMotivoNoContactoNuevo()">
            <label for="contacto_fallido_nuevo">No se pudo contactar al estudiante</label>
        </div>
        <div id="motivo-no-contacto-nuevo-wrap" style="display:none; margin: 10px 0;">
            <input type="text" name="motivo_no_contacto" class="form-control" placeholder="Motivo (ej. teléfono apagado)">
        </div>

        <div class="form-group">
            <label>Observación <span style="color:#C62828">*</span></label>
            <textarea name="observacion" rows="3" required placeholder="Escriba la observación..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar observación
        </button>
    </form>
</div>
@endif

{{-- Cerrar caso --}}
@if($reporte->estado !== 'cerrado')
<div class="card cerrar-caso-card">
    <div class="card-title" style="color:#C62828">Cerrar caso</div>
    <p style="font-size:13px; color:#9DA3B4; margin-bottom:12px">
        Una vez cerrado el caso no podrá modificarse. Indique la solución o razón del cierre.
    </p>

    <form method="POST" action="/bienestar/{{ $reporte->id }}/cerrar" onsubmit="return validarCierre(event)">
        @csrf
        
        <div class="form-group">
            <label>Solución / Razón de cierre <span style="color:#C62828">*</span></label>
            <textarea name="razon_cierre" id="razon_cierre" rows="3"
                      placeholder="Ej: Se realizó acompañamiento psicológico, el estudiante mejoró su rendimiento.&#10;&#10;Ó&#10;Se remitió a la EPS para atención médica.&#10;&#10;Ó&#10;El estudiante no requiere seguimiento adicional."
                      required style="width:100%; padding:10px; border-radius:8px; border:1.5px solid #E2E4EA"></textarea>
        </div>

        <div class="modal-actions" style="margin-top:16px">
            <button type="submit" class="btn btn-outline" style="border-color:#C62828; color:#C62828">
                <i class="fa-solid fa-lock"></i> Cerrar caso
            </button>
        </div>
    </form>
</div>
@else
{{-- Caso cerrado --}}
<div class="card">
    <div class="card-title" style="color:#2D7D32">
        <i class="fa-solid fa-circle-check"></i> Caso cerrado
    </div>
    @if($seguimiento && !empty($seguimiento->razon_cierre))
    <div style="background:#E8F5E9; padding:16px; border-radius:10px; border-left:4px solid #2D7D32">
        <strong style="display:block; margin-bottom:8px; color:#1B5E20">Solución / Razón de cierre:</strong>
        <p style="margin:0; white-space:pre-line">{{ $seguimiento->razon_cierre }}</p>
    </div>
    @else
    <div style="background:#FFF3E0; padding:16px; border-radius:10px; border-left:4px solid #E65100">
        <strong style="display:block; margin-bottom:8px; color:#E65100">Caso cerrado sin solución registrada</strong>
        <p style="margin:0">No se registró una solución al cerrar este caso.</p>
    </div>
    @endif
</div>
@endif

@push('scripts')
<script src="{{ asset('js/modules/bienestar.js') }}"></script>
@endpush
@endsection