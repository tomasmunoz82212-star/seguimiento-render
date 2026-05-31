@extends('layouts.app')

@section('titulo', 'Recuperar Contraseña')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/Recuperacion.css') }}">
@endpush

@section('cuerpo')
<body class="recuperacion-page">
    <div class="recuperacion-card">
        <div class="card-title">
            <i class="fa-solid fa-envelope"></i>
            <h2>Recuperar Contraseña</h2>
            <p>Ingresa tu correo institucional para restablecer tu contraseña</p>
        </div>

        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/recuperar-contrasena/enviar">
            @csrf
            <div class="form-group">
                <label>Correo electrónico institucional</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required 
                       placeholder="usuario@politecnicojic.edu.co">
                @error('correo')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Enviar código
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="/Login" class="btn-link">Volver al inicio de sesión</a>
            </div>
        </form>
    </div>
</body>
@endsection