@extends('layouts.app')

@section('cuerpo')
<aside class="sidebar">
    @include('partials.sidebar.brand')
    @include('partials.sidebar.nav')
    @include('partials.sidebar.footer')
</aside>

<main class="main-content">
    @yield('contenido')
</main>

{{-- MODAL DE INFORMACIÓN DEL USUARIO --}}
@include('partials.modals.user-info')
@endsection
