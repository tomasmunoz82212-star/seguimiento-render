@extends('layouts.panel')
@section('titulo', 'Panel Bienestar')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/Bienestar.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Panel Bienestar</div>
        <div class="page-sub">Gestión de aspectos que afectan a los estudiantes</div>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Barra de filtros con período --}}
@if(isset($periodos) && $periodos->count() > 0)
<div class="selector-periodo" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; background: #F7F8FA; padding: 12px 16px; border-radius: 10px;">
    <div class="periodo-actual">
        @if(isset($periodoSeleccionado) && $periodoSeleccionado)
            <span class="badge badge-activo" style="background: #E8F5E9; color: #2D7D32; padding: 6px 12px;">
                <i class="fa-solid fa-calendar"></i> Mostrando: <strong>{{ $periodoSeleccionado->nombre }}</strong>
            </span>
        @else
            <span class="badge" style="background: #E3F2FD; color: #1565C0; padding: 6px 12px;">
                <i class="fa-solid fa-calendar"></i> Mostrando: <strong>Todos los períodos</strong>
            </span>
        @endif
    </div>
    
    <form method="GET" action="/bienestar" class="form-inline">
        <div class="form-group" style="display: flex; gap: 8px; align-items: center; margin: 0;">
            <label style="font-size: 12px; margin: 0; font-weight: 600;">Filtrar por período:</label>
            <select name="periodo_id" class="form-control" style="width: 180px; padding: 6px 10px;" onchange="this.form.submit()">
                <option value="">-- Todos los períodos --</option>
                @foreach($periodos as $p)
                    <option value="{{ $p->id }}" {{ isset($periodoSeleccionado) && $periodoSeleccionado->id == $p->id ? 'selected' : '' }}>
                        {{ $p->nombre }}
                    </option>
                @endforeach
            </select>
            @if(isset($periodoSeleccionado) && $periodoSeleccionado)
                <a href="/bienestar" class="btn btn-outline btn-sm" style="padding: 5px 12px;">
                    <i class="fa-solid fa-times"></i> Limpiar
                </a>
            @endif
        </div>
    </form>
</div>
@endif

@include('bienestar.partials.content')

@endsection