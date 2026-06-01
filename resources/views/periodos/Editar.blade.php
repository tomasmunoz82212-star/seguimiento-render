@extends('layouts.panel')
@section('titulo', 'Periodo ' . $periodo->nombre)

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Periodos.css') }}">
@endpush

@section('contenido')
<a href="/periodos" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver a periodos
</a>

<div class="page-header">
    <div>
        <div class="page-title">Periodo {{ $periodo->nombre }}</div>
        <div class="page-sub">Actualice los datos del periodo o el listado de estudiantes</div>
    </div>
    @if($periodo->estado === 'activo')
        <span class="badge badge-activo"><span class="badge-dot"></span> Activo</span>
    @else
        <span class="badge badge-cerrado">Cerrado</span>
    @endif
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-error">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

<div class="card max-640" style="margin-bottom:20px">
    <div class="card-title">Datos del periodo</div>
    <form method="POST" action="/periodos/{{ $periodo->id }}">
        @csrf
        @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre"
                       value="{{ $periodo->nombre }}" required>
                @error('nombre')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Fecha de inicio</label>
                <input type="date" name="fecha_inicio"
                       value="{{ $periodo->fecha_inicio }}" required>
            </div>
            <div class="form-group">
                <label>Fecha de fin</label>
                <input type="date" name="fecha_fin"
                       value="{{ $periodo->fecha_fin }}" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:14px">
            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
        </button>
    </form>
</div>

<div class="card max-640">
    <div class="card-title">
        Actualizar listado —
        <span style="color:#2D7D32">{{ $periodo->matriculas_count }} registros actuales</span>
    </div>
    <div class="alert-banner alert-warn">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>El archivo reemplazará el listado actual del periodo. Columnas requeridas:
        <strong>N° Documento, Nombre, Carrera, Semestre, Correo, Teléfono</strong>.</span>
    </div>
    <form method="POST" action="/periodos/{{ $periodo->id }}/listado"
          enctype="multipart/form-data">
        @csrf
        <div class="upload-zone" id="upload-zone-edit"
             onclick="document.getElementById('archivo-edit').click()">
            <i class="fa-solid fa-file-arrow-up"></i>
            <strong>Subir nuevo archivo para actualizar el listado</strong>
            <small>Formatos: .xlsx, .xls — Máximo 10 MB</small>
        </div>
        <input type="file" id="archivo-edit" name="archivo"
               accept=".xlsx,.xls" style="display:none"
               onchange="mostrarArchivoEdit(this)">
        @error('archivo')
            <span class="field-error">{{ $message }}</span>
        @enderror
        <div class="preview-box">
            Archivo seleccionado:
            <strong id="nombre-archivo-edit" style="color:#9DA3B4">Ninguno</strong>
        </div>
        <div style="display:flex; gap:10px; margin-top:16px">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-arrows-rotate"></i> Actualizar listado
            </button>
            <a href="/periodos/{{ $periodo->id }}/estudiantes" class="btn btn-outline">
                <i class="fa-solid fa-users"></i> Ver estudiantes
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/periodos.js') }}"></script>
@endpush