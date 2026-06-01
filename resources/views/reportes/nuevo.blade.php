@extends('layouts.panel')
@section('titulo', 'Nuevo Reporte')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Reportes.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Nuevo Reporte</div>
        <div class="page-sub">Registre una situación observada en un estudiante</div>
    </div>
</div>

@if(session('error'))
    <div class="alert-error">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

<div class="card max-800" style="margin: 0 auto">
    <form method="POST" action="/nuevo-reporte" id="form-reporte">
        @csrf

        <div class="card-title">Datos del estudiante</div>

        <div class="form-group">
            <label>Número de documento</label>
            <div class="input-search" style="display: flex; gap: 8px;">
                <input type="text"
                    id="documento-input"
                    placeholder="Ej: 1234567890"
                    style="flex: 1;">
                <button type="button" id="btn-buscar" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <input type="hidden" name="documento" id="documento-hidden">
            @error('documento')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="estudiante-info" id="estudiante-info" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="est-nombre" readonly>
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="text" id="est-correo" readonly>
                </div>
            </div>
            <div class="form-group">
                <label>Carrera del estudiante</label>
                <input type="text" id="est-carrera" readonly style="background:#E8F5E9; font-weight:500;">
            </div>
        </div>

        <hr class="modal-sep">

        <div class="card-title" style="margin-top:20px">Datos del reporte</div>

        <div class="form-group">
            <label>Período académico</label>
            @if($periodo)
                <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">
                <div class="periodo-activo-info">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>{{ $periodo->nombre }}</span>
                    <span class="badge badge-activo">
                        <span class="badge-dot"></span> Activo
                    </span>
                </div>
            @else
                <div class="alert-error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    No hay ningún período activo. Contacte al administrador.
                </div>
            @endif
        </div>

        <input type="hidden" name="programa_id" id="programa_id">
        <input type="hidden" name="materia_id" id="materia_id_input">

        <div class="form-group">
            <label>Materia</label>
            <div class="materia-selector">
                <button type="button" id="btn-seleccionar-materia" class="btn btn-outline" style="width: 100%; justify-content: space-between;" disabled>
                    <span id="materia-seleccionada">— Seleccionar materia —</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
            @error('materia_id')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Tipo de situación</label>
            <select name="tipo" required>
                <option value="">— Seleccionar tipo —</option>
                <option value="academico"      {{ old('tipo') === 'academico'      ? 'selected' : '' }}>Académico</option>
                <option value="asistencia"     {{ old('tipo') === 'asistencia'     ? 'selected' : '' }}>Asistencia</option>
                <option value="comportamiento" {{ old('tipo') === 'comportamiento' ? 'selected' : '' }}>Comportamiento</option>
            </select>
            @error('tipo')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Descripción detallada</label>
            <textarea name="descripcion" rows="5"
                      placeholder="Describa la situación observada..."
                      required>{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <hr class="modal-sep">

        <div style="display:flex; gap:10px; margin-top:16px">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar reporte
            </button>
            <button type="button" class="btn btn-outline" onclick="limpiarTodo()">
                <i class="fa-solid fa-broom"></i> Limpiar
            </button>
        </div>

    </form>
</div>

{{-- MODAL PARA SELECCIONAR MATERIA --}}
<div id="modal-materias" class="modal-overlay" style="display: none;">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-book"></i> Seleccionar materia</span>
            <button class="modal-close" onclick="cerrarModalMaterias()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Buscar materia</label>
                <input type="text" id="buscador-materias" class="form-control" 
                       placeholder="Escribe el nombre de la materia (sin tildes, mayúsculas o minúsculas)..."
                       autocomplete="off">
            </div>
            <div id="lista-materias" style="max-height: 350px; overflow-y: auto; border: 1px solid #E2E4EA; border-radius: 10px;">
                <div class="text-center" style="padding: 20px; color: #9DA3B4;">
                    Primero selecciona un estudiante
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="cerrarModalMaterias()">Cancelar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/reportes.js') }}"></script>
@endpush