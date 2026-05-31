<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SEG - @yield('titulo', 'Panel')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('estilos')
</head>
<body>
    @yield('cuerpo')
    
    {{-- Scripts globales --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/actualizar-niveles.js') }}"></script>
    <script src="{{ asset('js/modules/notificaciones.js') }}"></script>
    <script src="{{ asset('js/auto-refresh.js') }}"></script>
    
    @stack('scripts')
</body>
</html>