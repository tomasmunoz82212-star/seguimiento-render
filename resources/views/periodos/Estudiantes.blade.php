@extends('layouts.panel')
@section('titulo', 'Estudiantes — ' . $periodo->nombre)

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/periodos.css') }}">
@endpush

@section('contenido')
<a href="/periodos" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i> Volver a periodos
</a>

<div class="page-header">
    <div>
        <div class="page-title">Estudiantes — {{ $periodo->nombre }}</div>
        <div class="page-sub">
            {{ $periodo->matriculas_count }} estudiantes matriculados ·
            {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }}
            —
            {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }}
        </div>
    </div>
    @if($periodo->estado === 'activo')
        <span class="badge badge-activo"><span class="badge-dot"></span> Activo</span>
    @else
        <span class="badge badge-cerrado">Cerrado</span>
    @endif
</div>

@if($periodo->matriculas->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-users-slash"></i>
        <p>No hay estudiantes cargados en este período.</p>
        <a href="/periodos/{{ $periodo->id }}/editar" class="btn btn-primary">
            Cargar estudiantes
        </a>
    </div>
@else
    <div class="stat-grid" style="margin-bottom:22px">
        <div class="stat-card" style="--accent: #2D7D32">
            <div class="stat-value">{{ $periodo->matriculas_count }}</div>
            <div class="stat-label">Total matriculados</div>
        </div>
        @foreach($periodo->matriculas->groupBy('programa.nombre') as $carrera => $matriculas)
        <div class="stat-card" style="--accent: #1565C0">
            <div class="stat-value">{{ $matriculas->count() }}</div>
            <div class="stat-label">{{ $carrera }}</div>
        </div>
        @endforeach
    </div>

    <div style="margin-bottom:16px">
        <input type="text" id="buscador"
               placeholder="Buscar por documento o nombre..."
               oninput="filtrar()"
               style="width:100%; max-width:380px; padding:10px 14px;
                      border:1.5px solid #E2E4EA; border-radius:10px;
                      font-size:13px; outline:none">
    </div>

    <div class="table-wrap">
        <table id="tabla-estudiantes">
            <thead>
                <tr>
                    <th>N° Documento</th>
                    <th>Nombre</th>
                    <th>Carrera</th>
                    <th>Semestre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodo->matriculas as $m)
                <tr>
                    <td class="mono text-gray">{{ $m->estudiante->documento }}</td>
                    <td><strong>{{ $m->estudiante->nombre }}</strong></td>
                    <td>{{ $m->programa->nombre }}</td>
                    <td style="text-align:center">{{ $m->semestre }}°</td>
                    <td class="text-gray" style="font-size:12px">
                        {{ $m->estudiante->correo ?? '—' }}
                    </td>
                    <td class="text-gray">{{ $m->estudiante->telefono ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/modules/periodos.js') }}"></script>
@endpush