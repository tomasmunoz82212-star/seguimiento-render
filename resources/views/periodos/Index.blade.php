@extends('layouts.panel')
@section('titulo', 'Periodos Académicos')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Periodos.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Periodos Académicos</div>
        <div class="page-sub">Gestión de periodos y carga de estudiantes</div>
    </div>
    @if(session('rol') === 'administrador')
    <a href="/periodos/nuevo" class="btn btn-secondary">
        <i class="fa-solid fa-plus"></i> Nuevo periodo
    </a>
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

@if($periodos->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-calendar-xmark"></i>
        <p>No hay periodos registrados aún.</p>
        @if(session('rol') === 'administrador')
        <a href="/periodos/nuevo" class="btn btn-primary">Crear primer periodo</a>
        @endif
    </div>
@else
    <div class="periodo-cards">
        @foreach($periodos as $p)
        <div class="periodo-card {{ $p->estado === 'activo' ? 'periodo-activo' : '' }}">

            <div class="periodo-card-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>

            <div class="periodo-card-info">
                <div class="periodo-card-nombre">{{ $p->nombre }}</div>
                <div class="periodo-card-fechas">
                    {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                    —
                    {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }}
                </div>
                <div style="margin-top: 8px">
                    @if($p->estado === 'activo')
                        <span class="badge badge-activo">
                            <span class="badge-dot"></span> Activo
                        </span>
                    @else
                        <span class="badge badge-cerrado">Cerrado</span>
                    @endif
                </div>
            </div>

            <div class="periodo-card-stats">
                <div class="periodo-card-count">{{ $p->matriculas_count }}</div>
                <div class="periodo-card-count-label">Estudiantes</div>
            </div>

            <div class="periodo-card-actions">
                @if(session('rol') === 'administrador')
                <a href="/periodos/{{ $p->id }}/editar" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-pen"></i> Actualizar
                </a>
                @endif
                <a href="/periodos/{{ $p->id }}/estudiantes" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-users"></i> Ver estudiantes
                </a>
                <form method="POST" action="/periodos/{{ $p->id }}"
                      onsubmit="return confirm('¿Eliminar el período {{ $p->nombre }}? Se eliminarán también sus estudiantes.')">
                    @csrf
                    @method('DELETE')
                    @if(session('rol') === 'administrador')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                    @endif
                </form>
            </div>

        </div>
        @endforeach
    </div>
@endif
@endsection