@extends('layouts.panel')
@section('titulo', 'Nuevo Periodo Académico')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/periodos.css') }}">
@endpush

@section('contenido')
<a href="/periodos" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver a periodos
</a>

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Periodo Académico</div>
        <div class="page-sub">Configure el periodo y cargue el listado de estudiantes matriculados</div>
    </div>
</div>

@if(session('error'))
    <div class="alert-error">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

<div class="card max-640">
    <form method="POST" action="/periodos" enctype="multipart/form-data">
        @csrf

        <div class="card-title">Datos del periodo</div>

        <div class="form-row">
            <div class="form-group">
                <label>Nombre del periodo</label>
                <input type="text" name="nombre"
                       placeholder="Ej: 2026-1"
                       value="{{ old('nombre') }}" required>
                @error('nombre')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="alert-banner alert-info" style="margin-top:8px">
            <i class="fa-solid fa-circle-info"></i>
            <span>Al crear un nuevo periodo, el periodo activo actual se cerrará automáticamente.</span>
        </div>

        <div class="form-row" style="margin-top:16px">
            <div class="form-group">
                <label>Fecha de inicio</label>
                <input type="date" name="fecha_inicio"
                       value="{{ old('fecha_inicio') }}" required>
                @error('fecha_inicio')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Fecha de fin</label>
                <input type="date" name="fecha_fin"
                       value="{{ old('fecha_fin') }}" required>
                @error('fecha_fin')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <hr class="modal-sep">

        <div class="card-title" style="margin-top:20px">
            Carga de estudiantes matriculados
        </div>

        <div class="alert-banner alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>El archivo Excel debe tener las columnas en este orden:
            <strong>N° Documento, Nombre, Carrera, Semestre, Correo, Teléfono</strong>.
            La primera fila debe ser el encabezado.</span>
        </div>

        <div class="upload-zone"
             id="upload-zone"
             onclick="document.getElementById('archivo').click()">
            <i class="fa-solid fa-file-arrow-up"></i>
            <strong>Arrastra el archivo aquí o haz clic para seleccionar</strong>
            <small>Formatos: .xlsx, .xls — Máximo 10 MB</small>
        </div>

        <input type="file" id="archivo" name="archivo"
               accept=".xlsx,.xls" style="display:none"
               onchange="mostrarArchivo(this)" required>

        @error('archivo')
            <span class="field-error">{{ $message }}</span>
        @enderror

        <div class="preview-box" id="preview-box">
            Archivo seleccionado:
            <strong id="nombre-archivo" style="color:#9DA3B4">Ninguno</strong>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i>
                Crear periodo y cargar estudiantes
            </button>
            <a href="/periodos" class="btn btn-outline">Cancelar</a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/periodos.js') }}"></script>
@endpush