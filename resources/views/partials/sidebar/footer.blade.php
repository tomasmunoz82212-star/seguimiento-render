<div class="sb-footer">
    <div class="user-menu-trigger" onclick="toggleUserMenu(event)">
        <div class="user-avatar-footer">
            {{ strtoupper(substr(session('nombre_completo') ?? session('usuario'), 0, 1)) }}
        </div>
        <div class="user-info-footer">
            <strong>{{ session('nombre_completo') ?? session('usuario') }}</strong>
            <small>{{ ucfirst(session('rol')) }}</small>
        </div>
        <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
    </div>

    <div id="userDropdown" class="user-dropdown hidden">
        <a href="#" class="dropdown-item" onclick="mostrarModalUsuario(); return false;">
            <i class="fa-solid fa-user"></i>
            <span>Mi información</span>
        </a>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="dropdown-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</div>