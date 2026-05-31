@extends('layouts.panel')
@section('titulo', 'Seguimiento de Alertas')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/SeguimientoAlertas.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Seguimiento de Alertas</div>
        <div class="page-sub">Historial consolidado de alertas por estudiante</div>
    </div>
</div>

{{-- Barra de filtros --}}
<div class="filtros-bar" style="flex-wrap: wrap;">
    <form method="GET" action="/seguimiento" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
        <select name="estado" onchange="this.form.submit()">
            <option value="">— Todos los estados —</option>
            <option value="pendiente"      {{ request('estado') === 'pendiente'      ? 'selected' : '' }}>Pendiente</option>
            <option value="en_seguimiento" {{ request('estado') === 'en_seguimiento' ? 'selected' : '' }}>En seguimiento</option>
            <option value="cerrado"        {{ request('estado') === 'cerrado'        ? 'selected' : '' }}>Cerrado</option>
        </select>

        <select name="programa_id" onchange="this.form.submit()">
            <option value="">— Todas las carreras —</option>
            @foreach($programas as $p)
                <option value="{{ $p->id }}" {{ request('programa_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>

        @if(isset($periodos) && $periodos->count() > 0)
        <select name="periodo_id" onchange="this.form.submit()" style="min-width: 160px;">
            <option value="">— Todos los períodos —</option>
            @foreach($periodos as $p)
                <option value="{{ $p->id }}" {{ isset($periodoSeleccionado) && $periodoSeleccionado->id == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>
        @endif

        @if(request('estado') || request('programa_id') || request('periodo_id'))
            <a href="/seguimiento" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-xmark"></i> Limpiar filtros
            </a>
        @endif
    </form>

    <div class="filtros-total" style="display: flex; align-items: center; gap: 15px;">
        @if(isset($periodoSeleccionado) && $periodoSeleccionado)
            <span class="badge" style="background: #E8F5E9; color: #2D7D32; padding: 5px 12px; border-radius: 20px;">
                <i class="fa-solid fa-calendar"></i> <strong>{{ $periodoSeleccionado->nombre }}</strong>
            </span>
        @else
            <span class="badge" style="background: #E3F2FD; color: #1565C0; padding: 5px 12px; border-radius: 20px;">
                <i class="fa-solid fa-calendar"></i> <strong>Todos los períodos</strong>
            </span>
        @endif
    </div>
</div>
@include('seguimiento.partials.content')

@endsection