@extends('layouts.app')

@section('titulo', 'Nueva Contraseña')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/Recuperacion.css') }}">
@endpush

@section('cuerpo')
<body class="recuperacion-page">
    <div class="recuperacion-card">
        <div class="card-title">
            <i class="fa-solid fa-lock"></i>
            <h2>Nueva Contraseña</h2>
            <p>Ingresa tu nueva contraseña</p>
        </div>

        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/cambiar-contrasena-recuperacion">
            @csrf
            <div class="form-group">
                <label>Nueva contraseña</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password" data-target="password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <small>Mínimo 8 caracteres, una mayúscula, un número y un carácter especial (@ $ ! % * ? & #)</small>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Confirmar nueva contraseña</label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                    <button type="button" class="toggle-password" data-target="password_confirmation">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Actualizar contraseña
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="/Login" class="btn-link">Volver al inicio de sesión</a>
            </div>
        </form>
    </div>
</body>
@endsection