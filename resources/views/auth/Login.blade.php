@extends('layouts.app')

@section('titulo', 'Inicio de sesión')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/Login.css') }}">
@endpush

@section('cuerpo')
<div class="login-container">
    <img src="{{ asset('images/logo-poli-v.png') }}" alt="Logo del Politécnico">

    <h3>Seguimiento - CRU</h3>
    <p>Politécnico Colombiano Jaime Isaza Cadavid</p>

    <form method="POST" action="/login">
        @csrf
        <label for="usuario">Usuario</label>
        <div class="input-container">
            <i class="fa-solid fa-user"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario" value="{{ old('usuario') }}" required>
        </div>

        <label for="password">Contraseña</label>
        <div class="input-container">
            <i class="fa-solid fa-lock"></i>
            <div class="password-wrapper">
                <input type="password" id="password" name="contraseña" placeholder="Ingrese su contraseña" required>
                <button type="button" class="toggle-password" data-target="password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        @if(session('error'))
            <p style="color:red">{{ session('error') }}</p>
        @endif

        <button type="submit">
            <i class="fa-solid fa-right-to-bracket"></i>
            Iniciar sesión
        </button>
    </form>

    {{-- Enlace para recuperar contraseña --}}
    <!-- <div class="login-forgot" style="text-align: center; margin-top: 15px;">
        <a href="/recuperar-contrasena" class="btn-link">¿Olvidaste tu contraseña?</a>
    </div> -->
</div>
@endsection