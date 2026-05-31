@extends('layouts.panel')
@section('titulo', 'Detalle del Reporte')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/reportes.css') }}">
@endpush

@section('contenido')
<a href="/mis-reportes" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver a mis reportes
</a>

<div class="page-header">
    <div>
        <div class="page-title">Detalle del Reporte</div>
        <div class="page-sub">
            Registrado el {{ \Carbon\Carbon::parse($reporte->creado_en)->format('d/m/Y') }}
        </div>
    </div>
    <div style="display:flex; gap:8px; align-items:center">
        <span class="badge badge-tipo badge-tipo-{{ $reporte->tipo }}">
            {{ ucfirst($reporte->tipo) }}
        </span>
        <span class="badge badge-estado-{{ $reporte->estado }}">
            {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
        </span>
    </div>
</div>

<div class="detalle-grid">

    <div class="detalle-col">
        <div class="card">
            <div class="card-title">Información del estudiante</div>
            <div class="detalle-row">
                <span class="detalle-key">N° Documento</span>
                <span class="detalle-val mono">{{ $reporte->estudiante->documento }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Nombre</span>
                <span class="detalle-val">{{ $reporte->estudiante->nombre }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Correo</span>
                <span class="detalle-val" style="font-size:12px">
                    {{ $reporte->estudiante->correo ?? '—' }}
                </span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Teléfono</span>
                <span class="detalle-val">{{ $reporte->estudiante->telefono ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="detalle-col" style="flex:1.6">
        <div class="card">
            <div class="card-title">Información del reporte</div>
            <div class="detalle-row">
                <span class="detalle-key">Período</span>
                <span class="detalle-val">{{ $reporte->periodo->nombre }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Carrera</span>
                <span class="detalle-val">{{ $reporte->programa->nombre }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Materia</span>
                <span class="detalle-val">{{ $reporte->materia->nombre ?? '—' }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Tipo</span>
                <span class="detalle-val">{{ ucfirst($reporte->tipo) }}</span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Estado</span>
                <span class="detalle-val">
                    {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
                </span>
            </div>
            <div class="detalle-row">
                <span class="detalle-key">Registrado por</span>
                <span class="detalle-val">{{ $reporte->usuario->usuario }}</span>
            </div>
            <div class="detalle-row" style="align-items:flex-start">
                <span class="detalle-key">Descripción</span>
                <span class="detalle-val" style="line-height:1.7">
                    {{ $reporte->descripcion }}
                </span>
            </div>
        </div>

        @if($reporte->estado === 'pendiente' && session('rol') === 'docente')
        <div class="card" style="margin-top:16px">
            <div class="card-title">Editar descripción</div>
            <form method="POST" action="/reportes/{{ $reporte->id }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <textarea name="descripcion" rows="4">{{ $reporte->descripcion }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </form>
        </div>
        @endif
    </div>

</div>
@endsection