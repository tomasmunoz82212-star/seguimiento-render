@extends('layouts.app')

@section('titulo', 'Validar Código')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/Recuperacion.css') }}">
@endpush

@section('cuerpo')
<body class="recuperacion-page">
    <div class="recuperacion-card">
        <div class="card-title">
            <i class="fa-solid fa-key"></i>
            <h2>Verifica tu identidad</h2>
            <p>Ingresa el código de 6 dígitos que enviamos a tu correo</p>
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

        <form method="POST" action="/validar-codigo">
            @csrf
            <div class="form-group">
                <label>Código de verificación</label>
                <input type="text" name="codigo" maxlength="6" placeholder="000000" required>
                @error('codigo')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-check-circle"></i> Verificar código
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <form method="POST" action="/reenviar-codigo" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-link">
                        ¿No recibiste el código? Reenviar
                    </button>
                </form>
                <br>
                <a href="/recuperar-contrasena" class="btn-link">Usar otro correo</a>
            </div>
        </form>
    </div>
</body>
@endsection

@push('scripts')
<script src="{{ asset('js/recuperacion.js') }}"></script>
@endpush