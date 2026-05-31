@extends('layouts.panel')
@section('titulo', 'Notificaciones')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/notificaciones.css') }}">
@endpush

@section('contenido')
<div class="page-header">
    <div>
        <div class="page-title"><i class="fa-regular fa-bell"></i> Notificaciones</div>
        <div class="page-sub">Historial de alertas y avisos del sistema</div>
    </div>
</div>

@include('notificaciones.partials.content')

@endsection

@push('scripts')
<script src="{{ asset('js/modules/notificaciones.js') }}"></script>
@endpush