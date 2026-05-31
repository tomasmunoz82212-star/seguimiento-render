@extends('layouts.panel')
@section('titulo', 'Mis Reportes')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/reportes.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Mis Reportes</div>
        <div class="page-sub">Historial de reportes generados por usted</div>
    </div>
    <a href="/nuevo-reporte" class="btn btn-secondary">
        <i class="fa-solid fa-plus"></i> Nuevo reporte
    </a>
</div>

@include('components.selector-periodo')

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if($reportes->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-file-circle-xmark"></i>
        <p>No has generado reportes aún.</p>
        <a href="/nuevo-reporte" class="btn btn-primary">Crear primer reporte</a>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Materia</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportes as $r)
                <tr>
                    <td><strong>{{ $r->estudiante->nombre }}</strong></td>
                    <td class="text-gray">{{ $r->programa->nombre }}</td>
                    <td class="text-gray">{{ $r->materia->nombre ?? '—' }}</td>
                    <td>
                        <span class="badge badge-tipo badge-tipo-{{ $r->tipo }}">
                            {{ ucfirst($r->tipo) }}
                        </span>
                    </td>
                    <td class="text-gray">{{ $r->periodo->nombre }}</td>
                    <td class="mono text-gray">
                        {{ \Carbon\Carbon::parse($r->creado_en)->format('d/m/Y') }}
                    </td>
                    <td>
                        <span class="badge badge-estado-{{ $r->estado }}">
                            {{ ucfirst(str_replace('_', ' ', $r->estado)) }}
                        </span>
                    </td>
                    <td>
                        <div class="tabla-acciones">
                            <a href="/reportes/{{ $r->id }}" class="btn-link">
                                <i class="fa-solid fa-eye"></i> Ver
                            </a>
                            @if($r->estado === 'pendiente')
                                <form method="POST" action="/reportes/{{ $r->id }}"
                                    onsubmit="return confirm('¿Eliminar este reporte?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-link btn-link-danger">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection