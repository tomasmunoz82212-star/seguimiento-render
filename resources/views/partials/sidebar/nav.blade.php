<nav class="sb-nav">

    @if(in_array(session('rol'), ['administrador', 'coordinacion', 'bienestar']))
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
    @endif

    @if(in_array(session('rol'), ['administrador', 'coordinacion']))
        <a href="/periodos" class="nav-item {{ request()->is('periodos*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Periodos Académicos
        </a>
    @endif

    @if(in_array(session('rol'), ['administrador', 'coordinacion', 'bienestar']))
        <a href="/seguimiento" class="nav-item {{ request()->is('seguimiento*') ? 'active' : '' }}">
            <i class="fa-solid fa-triangle-exclamation"></i> Seguimiento de Alertas
        </a>
    @endif

    @if(in_array(session('rol'), ['administrador', 'coordinacion']))
        <a href="/analitica" class="nav-item {{ request()->is('analitica*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Analítica y Reportes
        </a>
    @endif

    @if(session('rol') === 'bienestar')
        <a href="/bienestar" class="nav-item {{ request()->is('bienestar') ? 'active' : '' }}">
            <i class="fa-solid fa-heart"></i> Panel Bienestar
        </a>
    @endif

    @if(session('rol') === 'administrador')
        <a href="/usuarios" class="nav-item {{ request()->is('usuarios*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Usuarios del Sistema
        </a>
    @endif

    @if(session('rol') === 'docente')
        <a href="/nuevo-reporte" class="nav-item {{ request()->is('nuevo-reporte') ? 'active' : '' }}">
            <i class="fa-solid fa-pen-to-square"></i> Nuevo Reporte
        </a>
        <a href="/mis-reportes" class="nav-item {{ request()->is('mis-reportes') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Mis Reportes
        </a>
    @endif

    @if(session('rol') === 'bienestar')
    <li class="nav-item">
        <a href="/notificaciones" class="nav-link" style="color: rgba(255, 255, 255, 0.65) !important;">
            <i class="fa-regular fa-bell"></i>
            <span>Notificaciones</span>
            <span id="notificacion-badge" class="badge-notificacion" style="display: {{ \App\Helpers\NotificacionHelper::contarNoLeidas() > 0 ? 'inline-block' : 'none' }};">
                {{ \App\Helpers\NotificacionHelper::contarNoLeidas() }}
            </span>
        </a>
    </li>
    @endif

</nav>