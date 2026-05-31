@extends('layouts.app')

@section('titulo', 'Cambiar Contraseña')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/CambiarContrasena.css') }}">
@endpush

@section('cuerpo')
<div class="card">
    <div class="card-title">
        <i class="fa-solid fa-key"></i>
        <h2>Cambiar Contraseña</h2>
        <p>Es obligatorio cambiar tu contraseña por seguridad.</p>
    </div>

    @if(session('error'))
        <div class="alert-error">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="/cambiar-contrasena">
        @csrf

        <div class="form-group">
            <label>Nueva contraseña</label>
            <div class="password-wrapper">
                <input type="password" name="nueva_contraseña" id="nueva_contraseña" required>
                <button type="button" class="toggle-password" data-target="nueva_contraseña">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <small>Mínimo 8 caracteres, una mayúscula, un número y un carácter especial (@ $ ! % * ? & #)</small>
            @error('nueva_contraseña')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Confirmar nueva contraseña</label>
            <div class="password-wrapper">
                <input type="password" name="nueva_contraseña_confirmation" id="nueva_contraseña_confirmation" required>
                <button type="button" class="toggle-password" data-target="nueva_contraseña_confirmation">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Cambiar Contraseña
        </button>
    </form>
</div>
@endsection