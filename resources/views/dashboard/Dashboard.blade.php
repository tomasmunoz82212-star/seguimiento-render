@extends('layouts.panel')
@section('titulo', 'Dashboard')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/Dashboard.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title">Bienvenido, {{ session('nombre_completo') ?? session('usuario') }}</div>
        <div class="page-sub">{{ ucfirst(session('rol')) }} — Sistema de Seguimiento</div>
    </div>
    @if($periodo)
        <span class="badge badge-activo">
            <span class="badge-dot"></span> Período activo: {{ $periodo->nombre }}
        </span>
    @endif
</div>

@if(!$periodo)
    <div class="alert-error">
        <i class="fa-solid fa-circle-xmark"></i>
        No hay ningún período activo. Contacte al administrador.
    </div>
@else
    {{-- Solo mostrar el contenido si hay período activo --}}
    @include('dashboard.partials.content')
@endif

@endsection

@push('scripts')
@if($periodo)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Pasar datos a variables globales para el script de gráficos
    window.tipoAcademico = {{ $tipoAcademico }};
    window.tipoAsistencia = {{ $tipoAsistencia }};
    window.tipoComportamiento = {{ $tipoComportamiento }};
    window.porCarrera = @json($porCarrera);
    window.reportesPorSemestre = @json($reportesPorSemestre);
    window.reportesPorMateria = @json($reportesPorMateria);
</script>
<script src="{{ asset('js/dashboard-charts.js') }}"></script>
@endif
@endpush